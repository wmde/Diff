<?php

namespace Diff;
use \Diff\Exception as Exception;

/**
 * Base class for diffs. Diffs are collections of IDiffOp objects,
 * and are themselves IDiffOp objects as well.
 *
 * TODO: since this is an ArrayIterator, people can just add stuff using $diff[] = $diffOp.
 * The $typePointers is not currently getting updates in this case.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Diff extends GenericArrayObject implements IDiff {

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
	 * @param array $operations Operations in array format
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
	 * @return array of IDiffOp
	 */
	public function getOperations() {
		return $this->getArrayCopy();
	}

	/**
	 * @since 0.1
	 *
	 * @param string $type
	 *
	 * @return array of DiffOp
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
	 * @param $operations array of IDiffOp
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
	 * @return array of DiffOpAdd
	 */
	public function getAdditions() {
		return $this->getTypeOperations( 'add' );
	}

	/**
	 * Returns the remove operations.
	 *
	 * @since 0.1
	 *
	 * @return array of DiffOpRemove
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
		/**
		 * @var IDiffOp $diffOp
		 */
		foreach ( $originDiff as $key => $diffOp ) {
			if ( $originDiff->getType() === 'list' || array_key_exists( $key, $currentObject ) || $diffOp->getType() === 'add' ) {
				if ( $diffOp->isAtomic() ) {
					if ( $originDiff->getType() === 'list' ) {
						$isRemove = $diffOp->getType() === 'remove';
						$value = $isRemove ? $diffOp->getOldValue() : $diffOp->getNewValue();

						if ( !$isRemove ||
							( $isRemove && in_array( $value, $currentObject ) )
						) {
							$diff[] = $diffOp;
						}
					}
					else {
						$canApplyOp =
							( $diffOp->getType() === 'add' && !array_key_exists( $key, $currentObject ) )
								|| (
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
					$this->addReversibleOperations( $childDiff, $diffOp, $currentObject[$key] );

					if ( !$childDiff->isEmpty() ) {
						$diff[$key] = $childDiff;
					}
				}
			}
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

}
