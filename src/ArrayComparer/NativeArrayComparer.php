<?php

declare( strict_types = 1 );

namespace Diff\ArrayComparer;

/**
 * Adapter for PHPs native array_diff method.
 *
 * @since 0.8
 *
 * @license BSD-3-Clause
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
	public function diffArrays( array $arrayOne, array $arrayTwo ): array {
		return array_diff( $arrayOne, $arrayTwo );
	}

}
