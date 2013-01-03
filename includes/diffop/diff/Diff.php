<?php

namespace Diff;
use InvalidArgumentException;

/**
 * Base class for diffs. Diffs are collections of DiffOp objects,
 * and are themselves DiffOp objects as well.
 *
 * FIXME: current implementation forces ListDiff to override and nullify, which is bad.
 *
 * TOOD: when not assoc, only add and remove ops should be permitted
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Diff extends \GenericArrayObject implements IDiff {

	/**
	 * @since 0.1
	 *
	 * @var boolean|null
	 */
	protected $isAssociative;

	/**
	 * Pointers to the operations of certain types for quick lookup.
	 *
	 * @since 0.1
	 *
	 * @var array
	 */
	protected $typePointers = array(
		'add' => array(),
		'remove' => array(),
		'change' => array(),
		'list' => array(),
		'map' => array(),
		'diff' => array(),
	);

	/**
	 * @see Diff::__construct
	 *
	 * @since 0.1
	 *
	 * @param DiffOp[] $operations
	 * @param boolean|null $isAssociative
	 */
	public function __construct( array $operations = array(), $isAssociative = null ) {
		foreach ( $operations as  $operation ) {
			if ( !( $operation instanceof DiffOp ) ) {
				throw new InvalidArgumentException( 'All elements fed to the Diff constructor should be of type DiffOp' );
			}
		}

		if ( $isAssociative !== null && !is_bool( $isAssociative ) ) {
			throw new InvalidArgumentException( '$isAssociative should be a boolean or null' );
		}

		$this->isAssociative = $isAssociative;

		parent::__construct( $operations );
	}

	/**
	 * @see GenericArrayObject::getObjectType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getObjectType() {
		return '\Diff\DiffOp';
	}

	/**
	 * @see Diff::getOperations
	 *
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
	 * @see Diff::addOperations
	 *
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
	 * @see GenericArrayObject::preSetElement
	 *
	 * @since 0.1
	 *
	 * @param integer|string $index
	 * @param mixed $value
	 *
	 * @return boolean
	 * @throws Exception
	 */
	protected function preSetElement( $index, $value ) {
		/**
		 * @var DiffOp $value
		 */
		if ( $this->isAssociative === false && ( $value->getType() !== 'add' && $value->getType() !== 'remove' ) ) {
			throw new Exception( 'Diff operation with invalid type "' . $value->getType() . '" provided.' );
		}

		if ( array_key_exists( $value->getType(), $this->typePointers ) ) {
			$this->typePointers[$value->getType()][] = $index;
		}
		else {
			throw new Exception( 'Diff operation with invalid type "' . $value->getType() . '" provided.' );
		}

		return true;
	}

	/**
	 * @see GenericArrayObject::getSerializationData
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	protected function getSerializationData() {
		$assoc = $this->isAssociative === null ? 'n' : ( $this->isAssociative ? 't' : 'f' );

		return array_merge(
			parent::getSerializationData(),
			array(
				'typePointers' => $this->typePointers,
				'assoc' => $assoc
			)
		);
	}

	/**
	 * @see GenericArrayObject::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 *
	 * @return array
	 */
	public function unserialize( $serialization ) {
		$serializationData = parent::unserialize( $serialization );

		$this->typePointers = $serializationData['typePointers'];
		$this->isAssociative = $serializationData['assoc'] === 'n' ? null : $serializationData['assoc'] === 't';

		return $serializationData;
	}

	/**
	 * Returns the add operations.
	 *
	 * @since 0.1
	 *
	 * @return DiffOpAdd[]
	 */
	public function getAdditions() {
		return $this->getTypeOperations( 'add' );
	}

	/**
	 * Returns the remove operations.
	 *
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
	 * @see Diff::getApplicableDiff
	 *
	 * @since 0.1
	 * @deprecated since 0.4, use Patcher::getApplicableDiff
	 *
	 * @param array $currentObject
	 *
	 * @return Diff
	 */
	public function getApplicableDiff( array $currentObject ) {
		$patcher = new MapPatcher( false );
		return $patcher->getApplicableDiff( $currentObject, $this );
	}

	/**
	 * @see DiffOp::isAtomic
	 *
	 * @since 0.1
	 *
	 * @return boolean
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
	 * @return integer
	 */
	public function count() {
		$count = 0;

		/**
		 * @var DiffOp $diffOp
		 */
		foreach ( $this as $diffOp ) {
			$count += count( $diffOp );
		}

		return $count;
	}

	/**
	 * @see Diff::removeEmptyOperations
	 *
	 * @since 0.3
	 */
	public function removeEmptyOperations() {
		foreach ( $this->getArrayCopy() as $key => $operation ) {
			if ( $operation instanceof Diff && $operation->isEmpty() ) {
				unset( $this[$key] );
			}
		}
	}

	/**
	 * Returns the value of the isAssociative flag.
	 *
	 * @since 0.4
	 *
	 * @return boolean|null
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
	 * @return boolean
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
	 * @return boolean
	 */
	public function hasAssociativeOperations() {
		return !empty( $this->typePointers['change'] )
			|| !empty( $this->typePointers['diff'] )
			|| !empty( $this->typePointers['map'] )
			|| !empty( $this->typePointers['list'] );
	}

}
