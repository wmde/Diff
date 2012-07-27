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

	public function getType() {
		return 'change';
	}

	public function __construct( $oldValue, $newValue ) {
		$this->oldValue = $oldValue;
		$this->newValue = $newValue;
	}

	public function getOldValue() {
		return $this->oldValue;
	}

	public function getNewValue() {
		return $this->newValue;
	}

	public function toArray() {
		return array(
			$this->getType(),
			$this->newValue,
			$this->oldValue,
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