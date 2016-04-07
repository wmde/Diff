<?php

namespace Diff\DiffOp;

/**
 * Represents a removal.
 * This means the value is not present in the "new" object but was in the old.
 *
 * @since 0.1
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpRemove extends AtomicDiffOp {

	private $oldValue;

	/**
	 * @see DiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'remove';
	}

	/**
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
	 * @see Serializable::serialize
	 *
	 * @since 0.1
	 *
	 * @return string|null
	 */
	public function serialize() {
		return serialize( $this->oldValue );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 */
	public function unserialize( $serialization ) {
		$this->oldValue = unserialize( $serialization );
	}

	/**
	 * @see DiffOp::toArray
	 *
	 * @since 0.5
	 *
	 * @param callable|null $valueConverter optional callback used to convert any
	 *        complex values to arrays.
	 *
	 * @return array
	 */
	public function toArray( $valueConverter = null ) {
		return array(
			'type' => $this->getType(),
			'oldvalue' => $this->objectToArray( $this->oldValue, $valueConverter ),
		);
	}

}
