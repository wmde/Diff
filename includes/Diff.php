<?php

namespace Diff;
use \Diff\Exception as Exception;

/**
 * Base class for diffs. Diffs are collections of IDiffOp objects,
 * and are themselves IDiffOp objects as well.
 *
 * FIXME: since this is an ArrayIterator, people can just add stuff using $diff[] = $diffOp.
 * The $typePointers is not currently getting updates in this case.
 *
 * FIXME: current implementation forces ListDiff to override and nullify, which is bad.
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
	 * Creates and returns an empty Diff.
	 * @see IDiff::newEmpty
	 *
	 * @since 0.1
	 *
	 * @param $parentKey = null
	 *
	 * @return Diff
	 */
	public static function newEmpty( $parentKey = null ) {
		return new static( array(), $parentKey );
	}

	/**
	 * Key the operation has in it's parent diff.
	 *
	 * @since 0.1
	 *
	 * @var string|integer|null
	 */
	protected $parentKey;

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
	 * @see IDiff::__construct
	 *
	 * @since 0.1
	 *
	 * @param IDiffOp[] $operations
	 * @param string|integer|null $parentKey
	 */
	public function __construct( array $operations, $parentKey = null ) {
		parent::__construct( $operations );
		$this->parentKey = $parentKey;
	}

	/**
	 * @see GenericArrayObject::getObjectType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getObjectType() {
		return '\Diff\IDiffOp';
	}

	/**
	 * @see IDiff::getOperations
	 *
	 * @since 0.1
	 *
	 * @return IDiffOp[]
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
	 * @see IDiff::addOperations
	 *
	 * @since 0.1
	 *
	 * @param IDiffOp[] $operations
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
		 * @var IDiffOp $value
		 */
		if ( array_key_exists( $value->getType(), $this->typePointers ) ) {
			$this->typePointers[$value->getType()][] = $index;
		}
		else {
			throw new Exception( 'Diff operation with invalid type "' . $value->getType() . '" provided.' );
		}

		return true;
	}

	/**
	 * @see IDiff::getParentKey
	 *
	 * @since 0.1
	 *
	 * @return int|null|string
	 */
	public function getParentKey() {
		return $this->parentKey;
	}

	/**
	 * @see IDiff::hasParentKey
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function hasParentKey() {
		return !is_null( $this->parentKey );
	}

	/**
	 * @see GenericArrayObject::getSerializationData
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	protected function getSerializationData() {
		return array_merge(
			parent::getSerializationData(),
			array(
				'typePointers' => $this->typePointers,
				'parentKey' => $this->parentKey,
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
		$this->parentKey = $serializationData['parentKey'];

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
	 * @see IDiff::getApplicableDiff
	 *
	 * @since 0.1
	 *
	 * @param array $currentObject
	 *
	 * @return IDiff
	 */
	public function getApplicableDiff( array $currentObject ) {
		$undoDiff = static::newEmpty( $this->parentKey );
		static::addReversibleOperations( $undoDiff, $this, $currentObject );
		return $undoDiff;
	}

	/**
	 * Checks the operations in $originDiff for reversibility and adds those that are reversible to $diff.
	 *
	 * @since 0.1
	 *
	 * @param IDiff $diff The diff to add the reversible operations to
	 * @param IDiff $originDiff The diff with the operations we want to check reversibility for
	 * @param array $currentObject An array with the current structure used to check reversibility
	 *
	 * @return IDiff
	 */
	protected function addReversibleOperations( IDiff &$diff, IDiff $originDiff, array $currentObject ) {
		if ( function_exists( 'wfProfileIn' ) ) {
			wfProfileIn( __METHOD__ );
		}

		/**
		 * @var IDiffOp $diffOp
		 */
		foreach ( $originDiff as $key => $diffOp ) {
			if (
				// If the diff is a list, we do not need to check the keys
				$originDiff->getType() === 'list'

				// If it's not a list but the key is present, we're also fine
				|| array_key_exists( $key, $currentObject )

				// The key does not need to be present for new elements
				|| $diffOp->getType() === 'add'

				// Neither does it need to be present for list diffs that only have additions
				|| ( $diffOp->getType() === 'list' && $diffOp->getRemovals() === array() ) ) {

				if ( $diffOp->isAtomic() ) {
					if ( $originDiff->getType() === 'list' ) {
						$isRemove = $diffOp->getType() === 'remove';

						if ( !$isRemove ||
							( $isRemove && in_array( $diffOp->getOldValue(), $currentObject ) )
						) {
							$diff[] = $diffOp;
						}
					}
					else {
						$canApplyOp =
								( // An add operation for an element not yet present
									$diffOp->getType() === 'add'
									&& !array_key_exists( $key, $currentObject )
								)
								|| ( // A change or remove operation for a present element with correct source value
									$diffOp->getType() !== 'add'
									&& array_key_exists( $key, $currentObject )
									&& $currentObject[$key] === $diffOp->getOldValue()
								);

						if ( $canApplyOp ) {
							$diff[$key] = $diffOp;
						}
					}
				}
				else {
					$childDiff = $diffOp->getType() === 'map' ? MapDiff::newEmpty( $key ) : ListDiff::newEmpty( $key );

					// If the key was not yet present for a list diff with only additions, we need to add a new element
					if ( $diffOp->getType() === 'list' && $diffOp->getRemovals() === array() ) {
						$currentObject[$key] = array();
					}

					$this->addReversibleOperations( $childDiff, $diffOp, $currentObject[$key] );

					if ( !$childDiff->isEmpty() ) {
						$diff[$key] = $childDiff;
					}
				}
			}
		}

		if ( function_exists( 'wfProfileOut' ) ) {
			wfProfileOut( __METHOD__ );
		}
	}

	/**
	 * @see IDiffOp::isAtomic
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isAtomic() {
		return false;
	}

	/**
	 * @see IDiffOp::getType
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
		 * @var IDiffOp $diffOp
		 */
		foreach ( $this as $diffOp ) {
			$count += count( $diffOp );
		}

		return $count;
	}

	/**
	 * @see IDiff::removeEmptyOperations
	 *
	 * @since 0.3
	 */
	public function removeEmptyOperations() {
		foreach ( $this->getArrayCopy() as $key => $operation ) {
			if ( $operation instanceof \Diff\IDiff && $operation->isEmpty() ) {
				unset( $this[$key] );
			}
		}
	}

}
