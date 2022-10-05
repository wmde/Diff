<?php

declare( strict_types = 1 );

namespace Diff\DiffOp;

/**
 * Represents a removal.
 * This means the value is not present in the "new" object but was in the old.
 *
 * @since 0.1
 *
 * @license BSD-3-Clause
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
	public function getType(): string {
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
	#[\ReturnTypeWillChange]
	public function serialize() {
		return serialize( $this->oldValue );
	}

	/**
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function __serialize(): array {
		return [ $this->oldValue ];
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
		$this->oldValue = unserialize( $serialization );
	}

	/**
	 * @since 3.3.0
	 *
	 * @param array $data
	 */
	public function __unserialize( $data ): void {
		[ $this->oldValue ] = $data;
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
			'oldvalue' => $this->objectToArray( $this->oldValue, $valueConverter ),
		];
	}

}
