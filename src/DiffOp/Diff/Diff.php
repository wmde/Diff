<?php

namespace Diff\DiffOp\Diff;

use ArrayObject;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use InvalidArgumentException;

/**
 * Base class for diffs. Diffs are collections of DiffOp objects,
 * and are themselves DiffOp objects as well.
 *
 * @since 0.1
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 * @author Thiemo Mättig
 */
class Diff extends ArrayObject implements DiffOp {

	/**
	 * @var bool|null
	 */
	private $isAssociative = null;

	/**
	 * Pointers to the operations of certain types for quick lookup.
	 *
	 * @var array[]
	 */
	private $typePointers = array(
		'add' => array(),
		'remove' => array(),
		'change' => array(),
		'list' => array(),
		'map' => array(),
		'diff' => array(),
	);

	/**
	 * @var int
	 */
	private $indexOffset = 0;

	/**
	 * @since 0.1
	 *
	 * @param DiffOp[] $operations
	 * @param bool|null $isAssociative
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( array $operations = array(), $isAssociative = null ) {
		if ( $isAssociative !== null && !is_bool( $isAssociative ) ) {
			throw new InvalidArgumentException( '$isAssociative should be a boolean or null' );
		}

		parent::__construct( array() );

		foreach ( $operations as $offset => $operation ) {
			if ( !( $operation instanceof DiffOp ) ) {
				throw new InvalidArgumentException( 'All elements fed to the Diff constructor should be of type DiffOp' );
			}

			$this->offsetSet( $offset, $operation );
		}

		$this->isAssociative = $isAssociative;
	}

	/**
	 * @since 0.1
	 *
	 * @return DiffOp[]
	 */
	public function getOperations() {
		return $this->getArrayCopy();
	}

	/**
	 * @since 0.1
	 *
	 * @param string $type
	 *
	 * @return DiffOp[]
	 */
	public function getTypeOperations( $type ) {
		return array_intersect_key(
			$this->getArrayCopy(),
			array_flip( $this->typePointers[$type] )
		);
	}

	/**
	 * @since 0.1
	 *
	 * @param DiffOp[] $operations
	 */
	public function addOperations( array $operations ) {
		foreach ( $operations as $operation ) {
			$this->append( $operation );
		}
	}

	/**
	 * Gets called before a new element is added to the ArrayObject.
	 *
	 * At this point the index is always set (ie not null) and the
	 * value is always of the type returned by @see getObjectType.
	 *
	 * Should return a boolean. When false is returned the element
	 * does not get added to the ArrayObject.
	 *
	 * @param int|string $index
	 * @param DiffOp $value
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	private function preSetElement( $index, DiffOp $value ) {
		if ( $this->isAssociative === false && ( $value->getType() !== 'add' && $value->getType() !== 'remove' ) ) {
			throw new InvalidArgumentException( 'Diff operation with invalid type "' . $value->getType() . '" provided.' );
		}

		if ( array_key_exists( $value->getType(), $this->typePointers ) ) {
			$this->typePointers[$value->getType()][] = $index;
		}
		else {
			throw new InvalidArgumentException( 'Diff operation with invalid type "' . $value->getType() . '" provided.' );
		}

		return true;
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 */
	public function unserialize( $serialization ) {
		$serializationData = unserialize( $serialization );

		foreach ( $serializationData['data'] as $offset => $value ) {
			// Just set the element, bypassing checks and offset resolving,
			// as these elements have already gone through this.
			parent::offsetSet( $offset, $value );
		}

		$this->indexOffset = $serializationData['index'];

		$this->typePointers = $serializationData['typePointers'];

		if ( array_key_exists( 'assoc', $serializationData ) ) {
			$this->isAssociative = $serializationData['assoc'] === 'n' ? null : $serializationData['assoc'] === 't';
		} // The below cases are compat with < 0.4.
		elseif ( $this instanceof MapDiff ) {
			$this->isAssociative = true;
		}
		elseif ( $this instanceof ListDiff ) {
			$this->isAssociative = false;
		}
	}

	/**
	 * @since 0.1
	 *
	 * @return DiffOpAdd[]
	 */
	public function getAdditions() {
		return $this->getTypeOperations( 'add' );
	}

	/**
	 * @since 0.1
	 *
	 * @return DiffOpRemove[]
	 */
	public function getRemovals() {
		return $this->getTypeOperations( 'remove' );
	}

	/**
	 * @since 0.1
	 *
	 * @return DiffOpChange[]
	 */
	public function getChanges() {
		return $this->getTypeOperations( 'change' );
	}

	/**
	 * Returns the added values.
	 *
	 * @since 0.1
	 *
	 * @return array of mixed
	 */
	public function getAddedValues() {
		return array_map(
			function( DiffOpAdd $addition ) {
				return $addition->getNewValue();
			},
			$this->getTypeOperations( 'add' )
		);
	}

	/**
	 * Returns the removed values.
	 *
	 * @since 0.1
	 *
	 * @return array of mixed
	 */
	public function getRemovedValues() {
		return array_map(
			function( DiffOpRemove $addition ) {
				return $addition->getOldValue();
			},
			$this->getTypeOperations( 'remove' )
		);
	}

	/**
	 * @see DiffOp::isAtomic
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function isAtomic() {
		return false;
	}

	/**
	 * @see DiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'diff';
	}

	/**
	 * Counts the number of atomic operations in the diff.
	 * This means the size of a diff with as elements only empty diffs will be 0.
	 * Or that the size of a diff with one atomic operation and one diff that itself
	 * holds two atomic operations will be 3.
	 *
	 * @see Countable::count
	 *
	 * @since 0.1
	 *
	 * @return int
	 */
	public function count() {
		$count = 0;

		/**
		 * @var DiffOp $diffOp
		 */
		foreach ( $this as $diffOp ) {
			$count += $diffOp->count();
		}

		return $count;
	}

	/**
	 * @since 0.3
	 */
	public function removeEmptyOperations() {
		foreach ( $this->getArrayCopy() as $key => $operation ) {
			if ( $operation instanceof self && $operation->isEmpty() ) {
				unset( $this[$key] );
			}
		}
	}

	/**
	 * Returns the value of the isAssociative flag.
	 *
	 * @since 0.4
	 *
	 * @return bool|null
	 */
	public function isAssociative() {
		return $this->isAssociative;
	}

	/**
	 * Returns if the diff looks associative or not.
	 * This first checks the isAssociative flag and in case its null checks
	 * if there are any non-add-non-remove operations.
	 *
	 * @since 0.4
	 *
	 * @return bool
	 */
	public function looksAssociative() {
		return $this->isAssociative === null ? $this->hasAssociativeOperations() : $this->isAssociative;
	}

	/**
	 * Returns if the diff can be non-associative.
	 * This means it does not contain any non-add-non-remove operations.
	 *
	 * @since 0.4
	 *
	 * @return bool
	 */
	public function hasAssociativeOperations() {
		return !empty( $this->typePointers['change'] )
			|| !empty( $this->typePointers['diff'] )
			|| !empty( $this->typePointers['map'] )
			|| !empty( $this->typePointers['list'] );
	}

	/**
	 * Returns the Diff in array form where nested DiffOps are also turned into their array form.
	 *
	 * @see  DiffOp::toArray
	 *
	 * @since 0.5
	 *
	 * @param callable|null $valueConverter optional callback used to convert any
	 *        complex values to arrays.
	 *
	 * @return array
	 */
	public function toArray( $valueConverter = null ) {
		$operations = array();

		foreach ( $this->getOperations() as $key => $diffOp ) {
			$operations[$key] = $diffOp->toArray( $valueConverter );
		}

		return array(
			'type' => $this->getType(),
			'isassoc' => $this->isAssociative,
			'operations' => $operations
		);
	}

	/**
	 * @since 2.0
	 *
	 * @param mixed $target
	 *
	 * @return bool
	 */
	public function equals( $target ) {
		if ( $target === $this ) {
			return true;
		}

		if ( !( $target instanceof self ) ) {
			return false;
		}

		return $this->isAssociative === $target->isAssociative
			&& $this->getArrayCopy() == $target->getArrayCopy();
	}

	/**
	 * Finds a new offset for when appending an element.
	 * The base class does this, so it would be better to integrate,
	 * but there does not appear to be any way to do this...
	 *
	 * @return int
	 */
	private function getNewOffset() {
		while ( $this->offsetExists( $this->indexOffset ) ) {
			$this->indexOffset++;
		}

		return $this->indexOffset;
	}

	/**
	 * @see ArrayObject::append
	 *
	 * @since 0.1
	 *
	 * @param mixed $value
	 */
	public function append( $value ) {
		$this->setElement( null, $value );
	}

	/**
	 * @see ArrayObject::offsetSet()
	 *
	 * @since 0.1
	 *
	 * @param int|string $index
	 * @param mixed $value
	 */
	public function offsetSet( $index, $value ) {
		$this->setElement( $index, $value );
	}

	/**
	 * Method that actually sets the element and holds
	 * all common code needed for set operations, including
	 * type checking and offset resolving.
	 *
	 * If you want to do additional indexing or have code that
	 * otherwise needs to be executed whenever an element is added,
	 * you can overload @see preSetElement.
	 *
	 * @param int|string|null $index
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 */
	private function setElement( $index, $value ) {
		if ( !( $value instanceof DiffOp ) ) {
			throw new InvalidArgumentException(
				'Can only add DiffOp implementing objects to ' . get_called_class() . '.'
			);
		}

		if ( $index === null ) {
			$index = $this->getNewOffset();
		}

		if ( $this->preSetElement( $index, $value ) ) {
			parent::offsetSet( $index, $value );
		}
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function serialize() {
		$assoc = $this->isAssociative === null ? 'n' : ( $this->isAssociative ? 't' : 'f' );

		$data = array(
			'data' => $this->getArrayCopy(),
			'index' => $this->indexOffset,
			'typePointers' => $this->typePointers,
			'assoc' => $assoc
		);

		return serialize( $data );
	}

	/**
	 * Returns if the ArrayObject has no elements.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function isEmpty() {
		return $this->count() === 0;
	}

}
