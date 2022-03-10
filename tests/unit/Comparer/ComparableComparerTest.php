<?php

declare(strict_types=1);

namespace Diff\Tests\Comparer;

use Diff\Comparer\ComparableComparer;
use Diff\Tests\DiffTestCase;
use Diff\Tests\Fixtures\StubComparable;

/**
 * @covers  \Diff\Comparer\ComparableComparer
 *
 * @group   Diff
 * @group   Comparer
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ComparableComparerTest extends DiffTestCase {

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual($firstValue, $secondValue): void {
		$comparer = new ComparableComparer();

		$this->assertTrue($comparer->valuesAreEqual($firstValue, $secondValue));
	}

	public function equalProvider(): array {
		return [
			[
				new StubComparable(100),
				new StubComparable(100),
			],
			[
				new StubComparable('abc'),
				new StubComparable('abc'),
			],
			[
				new StubComparable(null),
				new StubComparable(null),
			],
		];
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual($firstValue, $secondValue): void {
		$comparer = new ComparableComparer();

		$this->assertFalse($comparer->valuesAreEqual($firstValue, $secondValue));
	}

	public function unequalProvider(): array {
		return [
			[
				null,
				null,
			],
			[
				new StubComparable(1),
				null,
			],
			[
				new StubComparable(1),
				new StubComparable(2),
			],
			[
				new StubComparable(1),
				new StubComparable('1'),
			],
			[
				new StubComparable(null),
				new StubComparable(false),
			],
		];
	}

}
