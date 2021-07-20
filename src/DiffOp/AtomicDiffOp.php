<?php

declare( strict_types = 1 );

namespace Diff\DiffOp;

/**
 * Base class for diff operations. A diff operation
 * represents a change to a single element.
 *
 * @since 0.1
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
abstract class AtomicDiffOp implements DiffOp {

	/**
	 * @see Countable::count
	 *
	 * @since 0.1
	 *
	 * @return int
	 */
	public function count(): int {
		return 1;
	}

	/**
	 * @see DiffOp::isAtomic
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function isAtomic(): bool {
		return true;
	}

	/**
	 * Converts an object to an array using the given callback function.
	 * If the convert callback is null or the value is not an object, the value is returned
	 * unchanged. The Converter callback is intended for converting the value into an array,
	 * but may also just leave the value unchanged if it cannot handle it.
	 *
	 * @since 0.5
	 *
	 * @param mixed $value The value to convert
	 * @param callable|null $valueConverter The converter to use if $value is an object
	 *
	 * @return mixed The $value unchanged, or the return value of calling $valueConverter on $value.
	 */
	protected function objectToArray( $value, $valueConverter = null ) {
		if ( $valueConverter !== null && is_object( $value ) ) {
			$value = call_user_func( $valueConverter, $value );
		}

		return $value;
	}

}
