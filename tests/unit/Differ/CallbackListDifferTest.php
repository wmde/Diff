<?php

declare(strict_types=1);

namespace Diff\Tests\Differ;

use Diff\Differ\CallbackListDiffer;
use Diff\Differ\Differ;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;
use stdClass;

/**
 * @covers  \Diff\Differ\CallbackListDiffer
 *
 * @group   Diff
 * @group   Differ
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackListDifferTest extends DiffTestCase {

	public function toDiffProvider(): array {
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
			'Two arrays with a single different element should differ by an add '
			. 'and a remove op even when they share identical elements',
		];

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff($old, $new, $expected, $message = ''): void {
		$callback = function ($foo, $bar) {
			return is_object($foo) ? $foo == $bar : $foo === $bar;
		};

		$this->doTestDiff(new CallbackListDiffer($callback), $old, $new, $expected, $message);
	}

	private function doTestDiff(Differ $differ, $old, $new, $expected, $message): void {
		$actual = $differ->doDiff($old, $new);

		$this->assertArrayEquals($expected, $actual, false, false, $message);
	}

	public function testCallbackComparisonReturningFalse(): void {
		$differ = new CallbackListDiffer(function () {
			return false;
		});

		$actual = $differ->doDiff([1, '2'], [1, '2', 'foo']);

		$expected = [
			new DiffOpAdd(1),
			new DiffOpAdd('2'),
			new DiffOpAdd('foo'),
			new DiffOpRemove(1),
			new DiffOpRemove('2'),
		];

		$this->assertArrayEquals(
			$expected, $actual, false, false,
			'All elements should be removed and added when comparison callback always returns false'
		);
	}

	public function testCallbackComparisonReturningTrue(): void {
		$differ = new CallbackListDiffer(function () {
			return true;
		});

		$actual = $differ->doDiff([1, '2', 'baz'], [1, 'foo', '2']);

		$expected = [];

		$this->assertArrayEquals(
			$expected, $actual, false, false,
			'No elements should be removed or added when comparison callback always returns true'
		);
	}

	public function testCallbackComparisonReturningNyanCat(): void {
		$differ = new CallbackListDiffer(function () {
			return '~=[,,_,,]:3';
		});

		$this->expectException('RuntimeException');

		$differ->doDiff([1, '2', 'baz'], [1, 'foo', '2']);
	}

}
