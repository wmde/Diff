<?php

declare( strict_types = 1 );

namespace Diff\DiffOp;

/**
 * Represents an addition.
 * This means the value was not present in the "old" object but is in the new.
 *
 * @since 0.1
 *
 * @license BSD-3-Clause
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
	public function getType(): string {
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
	#[\ReturnTypeWillChange]
	public function serialize() {
		return serialize( $this->newValue );
	}

	/**
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function __serialize(): array {
		return [ $this->newValue ];
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 */
	#[\ReturnTypeWillChange]
	public function unserialize( $serialization ) {
		$this->newValue = unserialize( $serialization );
	}

	/**
	 * @since 3.3.0
	 *
	 * @param array $data
	 */
	public function __unserialize( $data ): void {
		[ $this->newValue ] = $data;
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
	public function toArray( callable $valueConverter = null ): array {
		return [
			'type' => $this->getType(),
			'newvalue' => $this->objectToArray( $this->newValue, $valueConverter ),
		];
	}

}
