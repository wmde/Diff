<?php

declare( strict_types = 1 );

namespace Diff\Comparer;

/**
 * Adapter around a comparison callback that implements the ValueComparer
 * interface.
 *
 * @since 0.6
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackComparer implements ValueComparer {

	private $callback;

	/**
	 * @since 0.6
	 *
	 * @param callable $callback
	 */
	public function __construct( $callback ) {
		$this->callback = $callback;
	}

	public function valuesAreEqual( $firstValue, $secondValue ): bool {
		$valuesAreEqual = call_user_func_array( $this->callback, [ $firstValue, $secondValue ] );

		if ( !is_bool( $valuesAreEqual ) ) {
			throw new \RuntimeException( 'ValueComparer callback needs to return a boolean' );
		}

		return $valuesAreEqual;
	}

}
