<?php

declare( strict_types = 1 );

namespace Diff\ArrayComparer;

use Diff\Comparer\ValueComparer;

/**
 * Computes the difference between two arrays by comparing elements with
 * a ValueComparer.
 *
 * Quantity matters: [42, 42] and [42] are different
 *
 * @since 0.8
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrategicArrayComparer implements ArrayComparer {

	private $valueComparer;

	public function __construct( ValueComparer $valueComparer ) {
		$this->valueComparer = $valueComparer;
	}

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
			$valueOffset = $this->arraySearch( $element, $arrayTwo );

			if ( $valueOffset === false ) {
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
	 *
	 * @return bool|int|string
	 */
	private function arraySearch( $needle, array $haystack ) {
		foreach ( $haystack as $valueOffset => $thing ) {
			if ( $this->valueComparer->valuesAreEqual( $needle, $thing ) ) {
				return $valueOffset;
			}
		}

		return false;
	}

}
