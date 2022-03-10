<?php

declare(strict_types=1);

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\StrictArrayComparer;
use Diff\Tests\DiffTestCase;
use stdClass;

/**
 * @covers  \Diff\ArrayComparer\StrictArrayComparer
 *
 * @group   Diff
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictArrayComparerTest extends DiffTestCase {

	public function testCanConstruct(): void {
		new StrictArrayComparer();
		$this->assertTrue(true);
	}

	/**
	 * @dataProvider diffInputProvider
	 */
	public function testDiffReturnsExpectedValue(array $arrayOne, array $arrayTwo, array $expected, $message = ''): void {
		$differ = new StrictArrayComparer();

		$this->assertEquals(
			$expected,
			$differ->diffArrays($arrayOne, $arrayTwo),
			$message
		);
	}

	public function diffInputProvider(): array {
		$argLists = [];

		$argLists[] = [
			[],
			[],
			[],
			'The diff between empty arrays should be empty',
		];

		$argLists[] = [
			[1],
			[1],
			[],
			'The diff between identical arrays should be empty',
		];

		$argLists[] = [
			[1, 2, 1],
			[1, 1, 2],
			[],
			'The diff between arrays with the same values but different orders should be empty',
		];

		$argLists[] = [
			[1, 1],
			[1],
			[1],
			'The diff between an array with an element twice and an array that has it once should contain the element once',
		];

		$argLists[] = [
			['0'],
			[0],
			['0'],
			'Comparison should be strict',
		];

		$argLists[] = [
			[false],
			[null],
			[false],
			'Comparison should be strict',
		];

		$argLists[] = [
			[[1]],
			[[2]],
			[[1]],
			'Arrays are compared properly',
		];

		$argLists[] = [
			[[1]],
			[[1]],
			[],
			'Arrays are compared properly',
		];

		$argLists[] = [
			[new stdClass()],
			[new stdClass()],
			[],
			'Objects are compared based on value, not identity',
		];

		$argLists[] = [
			[(object) ['foo' => 'bar']],
			[(object) ['foo' => 'baz']],
			[(object) ['foo' => 'bar']],
			'Differences between objects are detected',
		];

		return $argLists;
	}

}
