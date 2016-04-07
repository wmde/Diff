<?php

namespace Diff\ArrayComparer;

/**
 * Adapter for PHPs native array_diff method.
 *
 * @since 0.8
 *
 * @license GPL-2.0+
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
