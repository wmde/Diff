<?php

declare(strict_types=1);

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers  \Diff\ArrayComparer\NativeArrayComparer
 *
 * @group   Diff
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NativeArrayComparerTest extends DiffTestCase {

	public function testCanConstruct(): void {
		new NativeArrayComparer();
		$this->assertTrue(true);
	}

	/**
	 * @dataProvider diffInputProvider
	 */
	public function testDiffArraysReturnsTheNativeValue(array $arrayOne, array $arrayTwo): void {
		$differ = new NativeArrayComparer();

		$this->assertEquals(
			array_diff($arrayOne, $arrayTwo),
			$differ->diffArrays($arrayOne, $arrayTwo)
		);
	}

	public function diffInputProvider(): array {
		$argLists = [];

		$argLists[] = [
			[],
			[],
		];

		$argLists[] = [
			['foo', 1],
			['foo', 1],
		];

		$argLists[] = [
			['bar', 2],
			['foo', 1],
		];

		$argLists[] = [
			[1, 'bar', 2, 1],
			['foo', 1, 3],
		];

		$argLists[] = [
			['', null, 2, false, 0],
			['0', true, 1, ' ', ''],
		];

		return $argLists;
	}

}
