<?php

declare( strict_types = 1 );

namespace Diff\ArrayComparer;

/**
 * Strict variant of PHPs array_diff method.
 *
 * Similar to @see array_diff with the following differences:
 *
 * - Strict comparison for arrays: ['42'] and [42] are different
 * - Quantity matters: [42, 42] and [42] are different
 * - Arrays and objects are compared properly: [[1]] and [[2]] are different
 * - Naive support for objects by using non-strict comparison
 * - Only works with two arrays (array_diff can take more)
 *
 * @since 0.8
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictArrayComparer implements ArrayComparer {

	/**
	 * @see ArrayComparer::diffArrays
	 *
	 * @since 0.8
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function diffArrays( array $arrayOne, array $arrayTwo ): array {
		$notInTwo = [];

		foreach ( $arrayOne as $element ) {
			$location = array_search( $element, $arrayTwo, !is_object( $element ) );

			if ( $location === false ) {
				$notInTwo[] = $element;
				continue;
			}

			unset( $arrayTwo[$location] );
		}

		return $notInTwo;
	}

}
