<?php

declare( strict_types = 1 );

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base class for unit tests in the Diff library.
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DiffTestCase extends TestCase {

	/**
	 * Utility method taking an array of elements and wrapping
	 * each element in it's own array. Useful for data providers
	 * that only return a single argument.
	 *
	 * @since 0.6
	 *
	 * @param array $elements
	 *
	 * @return array[]
	 */
	protected function arrayWrap( array $elements ) {
		return array_map(
			function( $element ) {
				return array( $element );
			},
			$elements
		);
	}

	/**
	 * Assert that two arrays are equal. By default this means that both arrays need to hold
	 * the same set of values. Using additional arguments, order and associated key can also
	 * be set as relevant.
	 *
	 * @since 0.6
	 *
	 * @param array $expected
	 * @param array $actual
	 * @param bool $ordered If the order of the values should match
	 * @param bool $named If the keys should match
	 * @param string $message
	 */
	protected function assertArrayEquals(
		array $expected,
		array $actual,
		$ordered = false,
		$named = false,
		$message = ''
	) {
		if ( !$ordered ) {
			$this->objectAssociativeSort( $expected );
			$this->objectAssociativeSort( $actual );
		}

		if ( !$named ) {
			$expected = array_values( $expected );
			$actual = array_values( $actual );
		}

		$this->assertEquals( $expected, $actual, $message );
	}

	/**
	 * Does an associative sort that works for objects.
	 *
	 * @param array $array
	 */
	private function objectAssociativeSort( array &$array ) {
		uasort(
			$array,
			function ( $a, $b ) {
				return serialize( $a ) > serialize( $b ) ? 1 : -1;
			}
		);
	}

}
