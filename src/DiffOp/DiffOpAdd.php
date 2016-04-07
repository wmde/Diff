<?php

namespace Diff\DiffOp;

/**
 * Represents an addition.
 * This means the value was not present in the "old" object but is in the new.
 *
 * @since 0.1
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpAdd extends AtomicDiffOp {

	private $newValue;

	/**
	 * @see DiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'add';
	}

	/**
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

	/**
	 * @see Serializable::serialize
	 *
	 * @since 0.1
	 *
	 * @return string|null
	 */
	public function serialize() {
		return serialize( $this->newValue );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 */
	public function unserialize( $serialization ) {
		$this->newValue = unserialize( $serialization );
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
			'newvalue' => $this->objectToArray( $this->newValue, $valueConverter ),
		);
	}

}
