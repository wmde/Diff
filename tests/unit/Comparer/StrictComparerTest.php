<?php

declare(strict_types=1);

namespace Diff\Tests\Comparer;

use Diff\Comparer\StrictComparer;
use Diff\Tests\DiffTestCase;
use stdClass;

/**
 * @covers  \Diff\Comparer\StrictComparer
 *
 * @group   Diff
 * @group   Comparer
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictComparerTest extends DiffTestCase {

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual($firstValue, $secondValue): void {
		$comparer = new StrictComparer();

		$this->assertTrue($comparer->valuesAreEqual($firstValue, $secondValue));
	}

	public function equalProvider(): array {
		return [
			[1, 1],
			['', ''],
			['1', '1'],
			['foo bar ', 'foo bar '],
			[4.2, 4.2],
			[null, null],
			[false, false],
			[true, true],
			[[], []],
			[[1], [1]],
			[[1, 2, 'a'], [1, 2, 'a']],
			[['a' => 1, 'b' => 2, null], ['a' => 1, 'b' => 2, null]],
		];
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual($firstValue, $secondValue): void {
		$comparer = new StrictComparer();

		$this->assertFalse($comparer->valuesAreEqual($firstValue, $secondValue));
	}

	public function unequalProvider(): array {
		return [
			[1, 2],
			['', '0'],
			['', ' '],
			['', 0],
			['', false],
			[null, false],
			[null, 0],
			['1', '01'],
			['foo bar', 'foo bar '],
			[4, 4.0],
			[4.2, 4.3],
			[false, true],
			[true, '1'],
			[[], [1]],
			[[1], [2]],
			[[1, 2, 'b'], [1, 2, 'c']],
			[['a' => 1, 'b' => 2], ['a' => 1, 'b' => 2, null]],
			[new stdClass(), new stdClass()],
			[(object) ['a' => 1, 'b' => 2, null], (object) ['a' => 1, 'b' => 3, null]],
		];
	}

}
