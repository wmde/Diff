<?php

declare( strict_types = 1 );

namespace Diff\ArrayComparer;

use Diff\Comparer\ValueComparer;

/**
 * Computes the difference between two ordered arrays by comparing elements with
 * a ValueComparer.
 *
 * Quantity matters: [42, 42] and [42] are different
 * Order matters: [42, 43] and [43, 42] are different
 *
 * @since 0.9
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Tobias Gritschacher < tobias.gritschacher@wikimedia.de >
 */
class OrderedArrayComparer implements ArrayComparer {

	private $valueComparer;

	public function __construct( ValueComparer $valueComparer ) {
		$this->valueComparer = $valueComparer;
	}

	/**
	 * @see ArrayComparer::diffArrays
	 *
	 * @since 0.9
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function diffArrays( array $arrayOne, array $arrayTwo ): array {
		$notInTwo = [];

		foreach ( $arrayOne as $valueOffset => $element ) {
			if ( !$this->arraySearch( $element, $arrayTwo, $valueOffset ) ) {
				$notInTwo[] = $element;
				continue;
			}

			unset( $arrayTwo[$valueOffset] );
		}

		return $notInTwo;
	}

	/**
	 * @param string|int $needle
	 * @param array $haystack
	 * @param int|string $valueOffset
	 *
	 * @return bool
	 */
	private function arraySearch( $needle, array $haystack, $valueOffset ): bool {
		if ( array_key_exists( $valueOffset, $haystack ) ) {
			return $this->valueComparer->valuesAreEqual( $needle, $haystack[$valueOffset] );
		}

		return false;
	}

}
