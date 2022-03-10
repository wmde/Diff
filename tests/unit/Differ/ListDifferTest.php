<?php

declare(strict_types=1);

namespace Diff\Tests\Differ;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\ArrayComparer\StrictArrayComparer;
use Diff\Differ\Differ;
use Diff\Differ\ListDiffer;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;
use stdClass;

/**
 * @covers  \Diff\Differ\ListDiffer
 *
 * @group   Diff
 * @group   Differ
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDifferTest extends DiffTestCase {

	public function arrayComparerProvider(): array {
		$add = [new DiffOpAdd(1)];

		return [
			'null' => [null, $add],
			'native object' => [new NativeArrayComparer(), []],
			'strict object' => [new StrictArrayComparer(), $add],
		];
	}

	/**
	 * @dataProvider arrayComparerProvider
	 */
	public function testConstructor($arrayComparer, array $expected) {
		$differ = new ListDiffer($arrayComparer);
		$diff = $differ->doDiff([1], [1, 1]);
		$this->assertEquals($expected, $diff);
	}

	public function toDiffProvider() {
		$argLists = $this->getCommonArgLists();

		$old = [42, 42];
		$new = [42];
		$expected = [new DiffOpRemove(42)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'[42, 42] to [42] should [rem(42)]',
		];

		$old = [42];
		$new = [42, 42];
		$expected = [new DiffOpAdd(42)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'[42] to [42, 42] should [add(42)]',
		];

		$old = ['42'];
		$new = [42];
		$expected = [new DiffOpRemove('42'), new DiffOpAdd(42)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'["42"] to [42] should [rem("42"), add(42)]',
		];

		$old = [[1]];
		$new = [[2]];
		$expected = [new DiffOpRemove([1]), new DiffOpAdd([2])];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'[[1]] to [[2]] should [rem([1]), add([2])]',
		];

		$old = [[2]];
		$new = [[2]];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'[[2]] to [[2]] should result in an empty diff',
		];

		// test "soft" object comparison
		$obj1 = new stdClass();
		$obj2 = new stdClass();
		$objX = new stdClass();

		$obj1->test = 'Test';
		$obj2->test = 'Test';
		$objX->xest = 'Test';

		$old = [$obj1];
		$new = [$obj2];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Two arrays containing equivalent objects should result in an empty diff',
		];

		$old = [$obj1];
		$new = [$objX];
		$expected = [new DiffOpRemove($obj1), new DiffOpAdd($objX)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Two arrays containing different objects of the same type should result in an add and a remove op.',
		];

		return $argLists;
	}

	/**
	 * Returns those that both work for native and strict mode.
	 */
	private function getCommonArgLists(): array {
		$argLists = [];

		$old = [];
		$new = [];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'There should be no difference between empty arrays',
		];

		$old = [42];
		$new = [42];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'There should be no difference between arrays with the same element',
		];

		$old = [42, 'ohi', 4.2, false];
		$new = [42, 'ohi', 4.2, false];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'There should be no difference between arrays with the same elements',
		];

		$old = [42, 'ohi', 4.2, false];
		$new = [false, 4.2, 'ohi', 42];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'There should be no difference between arrays with the same elements even when not ordered the same',
		];

		$old = [];
		$new = [42];
		$expected = [new DiffOpAdd(42)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'An array with a single element should be an add operation different from an empty array',
		];

		$old = [42];
		$new = [];
		$expected = [new DiffOpRemove(42)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'An empty array should be a remove operation different from an array with one element',
		];

		$old = [1];
		$new = [2];
		$expected = [new DiffOpRemove(1), new DiffOpAdd(2)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Two arrays with a single different element should differ by an add and a remove op',
		];

		$old = [9001, 42, 1, 0];
		$new = [9001, 2, 0, 42];
		$expected = [new DiffOpRemove(1), new DiffOpAdd(2)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Two arrays with a single different element should differ by an add'
			. 'and a remove op even when they share identical elements',
		];

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff($old, $new, $expected, $message = ''): void {
		$this->doTestDiff(new ListDiffer(), $old, $new, $expected, $message);
	}

	private function doTestDiff(Differ $differ, $old, $new, $expected, $message): void {
		$actual = $differ->doDiff($old, $new);

		$this->assertArrayEquals($expected, $actual, false, false, $message);
	}

	public function toDiffNativeProvider(): array {
		$argLists = $this->getCommonArgLists();

		$old = ['42'];
		$new = [42];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'["42"] to [42] should result in an empty diff',
		];

		$old = [42, 42];
		$new = [42];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'[42, 42] to [42] should result in an empty diff',
		];

		$old = [42];
		$new = [42, 42];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'[42] to [42, 42] should result in an empty diff',
		];

		// TODO: test toString()-based object comparison

		return $argLists;
	}

	/**
	 * @dataProvider toDiffNativeProvider
	 */
	public function testDoNativeDiff($old, $new, $expected, $message = ''): void {
		$this->doTestDiff(new ListDiffer(new NativeArrayComparer()), $old, $new, $expected, $message);
	}

	public function testDiffCallsArrayComparatorCorrectly(): void {
		$arrayComparer = $this->createMock('Diff\ArrayComparer\ArrayComparer');

		$arrayComparer->expects($this->exactly(2))
			->method('diffArrays')
			->with(
				$this->equalTo([42]),
				$this->equalTo([42])
			)
			->will($this->returnValue([]));

		$differ = new ListDiffer($arrayComparer);

		$differ->doDiff([42], [42]);
	}

}
