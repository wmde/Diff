<?php

namespace Diff;

/**
 * Represents an addition.
 * This means the value was not present in the "old" object but is in the new.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpAdd extends DiffOp {

	protected $newValue;

	/**
	 * @see IDiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'add';
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param mixed $newValue
	 */
	public function __construct( $newValue ) {
		$this->newValue = $newValue;
	}

	/**
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function getNewValue() {
		return $this->newValue;
	}

	public function toArray() {
		return array(
			$this->getType(),
			$this->newValue,
		);
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