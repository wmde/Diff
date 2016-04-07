<?php

namespace Diff\Comparer;

/**
 * Adapter around a comparison callback that implements the ValueComparer
 * interface.
 *
 * @since 0.6
 *
 * @license GPL-2.0+
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

	public function valuesAreEqual( $firstValue, $secondValue ) {
		return call_user_func_array( $this->callback, array( $firstValue, $secondValue ) );
	}

}
