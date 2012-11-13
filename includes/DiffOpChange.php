<?php

namespace Diff;

/**
 * Represents a change.
 * This means the item is present in both the new and old objects, but changed value.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpChange extends DiffOp {

	protected $newValue;
	protected $oldValue;

	/**
	 * @see IDiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'change';
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param mixed $oldValue
	 * @param mixed $newValue
	 */
	public function __construct( $oldValue, $newValue ) {
		$this->oldValue = $oldValue;
		$this->newValue = $newValue;
	}

	/**
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function getOldValue() {
		return $this->oldValue;
	}

	/**
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function getNewValue() {
		return $this->newValue;
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @since 0.1
	 *
	 * @return string|null
	 */
	public function serialize() {
		return serialize( array( $this->newValue, $this->oldValue ) );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 *
	 * @return DiffOpChange
	 */
	public function unserialize( $serialization ) {
		list( $this->newValue, $this->oldValue ) = unserialize( $serialization );
	}

	/**
	 * @see IDiffOp::isAtomic
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isAtomic() {
		return true;
	}

}