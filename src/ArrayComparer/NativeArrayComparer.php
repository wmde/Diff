<?php

namespace Diff\ArrayComparer;

/**
 * Adapter for PHPs native array_diff method.
 *
 * @since 0.8
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NativeArrayComparer implements ArrayComparer {

	/**
	 * @see ArrayComparer::diffArrays
	 *
	 * Uses @see array_diff.
	 *
	 * @since 0.8
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function diffArrays( array $arrayOne, array $arrayTwo ) {
		return array_diff( $arrayOne, $arrayTwo );
	}

}
