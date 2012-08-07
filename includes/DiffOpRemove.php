<?php

namespace Diff;

/**
 * Represents a removal.
 * This means the value is not present in the "new" object but was in the old.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpRemove extends DiffOp {

	protected $oldValue;

	/**
	 * @see IDiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'remove';
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param mixed $oldValue
	 */
	public function __construct( $oldValue ) {
		$this->oldValue = $oldValue;
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
	 * @see \Serializable::serialize()
	 *
	 * @since 0.1
	 *
	 * @return string|null
	 */
	public function serialize() {
		return serialize( $this->oldValue );
	}

	/**
	 * @see \Serializable::unserialize()
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 */
	public function unserialize( $serialization ) {
		$this->oldValue = unserialize( $serialization );
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