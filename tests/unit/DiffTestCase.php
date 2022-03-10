<?php

declare(strict_types=1);

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base class for unit tests in the Diff library.
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DiffTestCase extends TestCase {

	/**
	 * Utility method taking an array of elements and wrapping
	 * each element in it's own array. Useful for data providers
	 * that only return a single argument.
	 *
	 * @param array $elements
	 *
	 * @return array[]
	 * @since 0.6
	 *
	 */
	protected function arrayWrap(array $elements): array {
		return array_map(
			function ($element) {
				return [$element];
			},
			$elements
		);
	}

	/**
	 * Assert that two arrays are equal. By default this means that both arrays need to hold
	 * the same set of values. Using additional arguments, order and associated key can also
	 * be set as relevant.
	 *
	 * @param array  $expected
	 * @param array  $actual
	 * @param bool   $ordered If the order of the values should match
	 * @param bool   $named   If the keys should match
	 * @param string $message
	 * @since 0.6
	 *
	 */
	protected function assertArrayEquals(
		array  $expected,
		array  $actual,
		bool   $ordered = false,
		bool   $named = false,
		string $message = ''
	) {
		if (!$ordered) {
			$this->objectAssociativeSort($expected);
			$this->objectAssociativeSort($actual);
		}

		if (!$named) {
			$expected = array_values($expected);
			$actual = array_values($actual);
		}

		$this->assertEquals($expected, $actual, $message);
	}

	/**
	 * Does an associative sort that works for objects.
	 *
	 * @param array $array
	 */
	private function objectAssociativeSort(array &$array): void {
		uasort(
			$array,
			function ($a, $b) {
				return serialize($a) > serialize($b) ? 1 : -1;
			}
		);
	}

}
