<?php

declare(strict_types=1);

namespace Diff\Tests\Differ;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\Differ\Differ;
use Diff\Differ\ListDiffer;
use Diff\Differ\MapDiffer;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;
use Diff\Tests\Fixtures\StubValueComparer;

/**
 * @covers  \Diff\Differ\MapDiffer
 *
 * @group   Diff
 * @group   Differ
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapDifferTest extends DiffTestCase {

	public function toDiffProvider(): array {
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
			'There should be no difference between two arrays with the same element',
		];

		$old = [42, 10, 'ohi', false, null, ['.', 4.2]];
		$new = [42, 10, 'ohi', false, null, ['.', 4.2]];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'There should be no difference between two arrays with the same elements',
		];

		$old = [42, 42, 42];
		$new = [42, 42, 42];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'There should be no difference between two arrays with the same elements',
		];

		$old = [1, 2];
		$new = [2, 1];
		$expected = [new DiffOpChange(1, 2), new DiffOpChange(2, 1)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Switching position should cause a diff',
		];

		$old = [0, 1, 2, 3];
		$new = [0, 2, 1, 3];
		$expected = [1 => new DiffOpChange(1, 2), 2 => new DiffOpChange(2, 1)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Switching position should cause a diff',
		];

		$old = ['a' => 0, 'b' => 1, 'c' => 0];
		$new = ['a' => 42, 'b' => 1, 'c' => 42];
		$expected = ['a' => new DiffOpChange(0, 42), 'c' => new DiffOpChange(0, 42)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Doing the same change to two different elements should result in two identical change ops',
		];

		$old = ['a' => 0, 'b' => 1];
		$new = ['a' => 0, 'c' => 1];
		$expected = ['b' => new DiffOpRemove(1), 'c' => new DiffOpAdd(1)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Changing the key of an element should result in a remove and an add op',
		];

		$old = ['a' => 0, 'b' => 1];
		$new = ['b' => 1, 'a' => 0];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Changing the order of associative elements should have no effect.',
		];

		$old = ['a' => ['foo']];
		$new = ['a' => ['foo']];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Comparing equal substructures without recursion should return nothing.',
			false,
		];

		$old = [];
		$new = ['a' => ['foo', 'bar']];
		$expected = ['a' => new DiffOpAdd(['foo', 'bar'])];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding a substructure should result in a single add operation when not in recursive mode.',
			false,
		];

		$old = ['a' => ['b' => 42]];
		$new = ['a' => ['b' => 7201010]];
		$expected = ['a' => new Diff(['b' => new DiffOpChange(42, 7201010)], true)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Recursion should work for nested associative diffs',
			true,
		];

		$old = ['a' => ['foo']];
		$new = ['a' => ['foo']];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Comparing equal sub-structures with recursion should return nothing.',
			true,
		];

		$old = ['stuff' => ['a' => 0, 'b' => 1]];
		$new = ['stuff' => ['b' => 1, 'a' => 0]];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Changing the order of associative elements in a substructure should have no effect.',
			true,
		];

		$old = [];
		$new = ['stuff' => ['b' => 1, 'a' => 0]];
		$expected = ['stuff' => new Diff(['b' => new DiffOpAdd(1), 'a' => new DiffOpAdd(0)])];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding a substructure should be reported as adding *to* a substructure when in recursive mode.',
			true,
		];

		$old = ['a' => [42, 9001], 1];
		$new = ['a' => [42, 7201010], 1];
		$expected = ['a' => new Diff([new DiffOpAdd(7201010), new DiffOpRemove(9001)], false)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Recursion should work for nested non-associative diffs',
			true,
		];

		$old = [[42], 1];
		$new = [[42, 42], 1];
		$expected = [new Diff([new DiffOpAdd(42)], false)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Nested non-associative diffs should behave as the default ListDiffer',
			true,
		];

		$old = [[42], 1];
		$new = [[42, 42, 1], 1];
		$expected = [new Diff([new DiffOpAdd(1)], false)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Setting a non-default Differ for non-associative diffs should work',
			true,
			new ListDiffer(new NativeArrayComparer()),
		];

		$old = ['a' => [42], 1, ['a' => 'b', 5], 'bah' => ['foo' => 'bar']];
		$new = ['a' => [42], 1, ['a' => 'b', 5], 'bah' => ['foo' => 'baz']];
		$expected = ['bah' => new Diff(['foo' => new DiffOpChange('bar', 'baz')], true)];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Nested structures with no differences should not result '
			. 'in nested empty diffs (these empty diffs should be omitted)',
			true,
		];

		$old = [
			'links' => [
				'enwiki' => [
					'page' => 'Foo',
					'badges' => [],
				],
			],
		];
		$new = [
			'links' => [
				'enwiki' => [
					'page' => 'Foo',
					'badges' => [],
				],
			],
		];
		$expected = [];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Comparing identical nested structures should not result in diff operations',
			true,
		];

		$old = [
			'links' => [
			],
		];
		$new = [
			'links' => [
				'enwiki' => [
					'page' => 'Foo',
					'badges' => [],
				],
			],
		];
		$expected = [
			'links' => new Diff(
				[
					'enwiki' => new Diff([
											 'page' => new DiffOpAdd('Foo'),
										 ]),
				],
				true
			),
		];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding a sitelink with no badges',
			true,
		];

		$old = [
			'links' => [
			],
		];
		$new = [
			'links' => [
				'enwiki' => [
					'page' => 'Foo',
					'badges' => ['Bar', 'Baz'],
				],
			],
		];
		$expected = [
			'links' => new Diff(
				[
					'enwiki' => new Diff(
						[
							'page' => new DiffOpAdd('Foo'),
							'badges' => new Diff(
								[
									new DiffOpAdd('Bar'),
									new DiffOpAdd('Baz'),
								],
								false
							),
						],
						true
					),
				],
				true
			),
		];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding a sitelink with badges',
			true,
		];

		$old = [
			'links' => [
				'enwiki' => [
					'page' => 'Foo',
					'badges' => [],
				],
			],
		];
		$new = [
			'links' => [
				'enwiki' => [
					'page' => 'Foo',
					'badges' => ['Bar', 'Baz'],
				],
			],
		];
		$expected = [
			'links' => new Diff(
				[
					'enwiki' => new Diff(
						[
							'badges' => new Diff(
								[
									new DiffOpAdd('Bar'),
									new DiffOpAdd('Baz'),
								],
								false
							),
						], true
					),
				],
				true
			),
		];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding bagdes to a sitelink',
			true,
		];

		$old = [];
		$new = [
			'enwiki' => [
				'page' => 'Foo',
				'badges' => ['Bar', 'Baz'],
			],
		];
		$expected = [
			'enwiki' => new DiffOpAdd(
				[
					'page' => 'Foo',
					'badges' => ['Bar', 'Baz'],
				]
			),
		];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding a sitelink with non-recursive mode',
			false,
		];

		$old = [
			'enwiki' => [
				'page' => 'Foo',
				'badges' => [],
			],
		];
		$new = [
			'enwiki' => [
				'page' => 'Foo',
				'badges' => ['Bar', 'Baz'],
			],
		];
		$expected = [
			'enwiki' => new DiffOpChange(
				[
					'page' => 'Foo',
					'badges' => [],
				],
				[
					'page' => 'Foo',
					'badges' => ['Bar', 'Baz'],
				]
			),
		];

		$argLists[] = [
			$old,
			$new,
			$expected,
			'Adding badges to a sitelink with non-recursive mode',
			false,
		];

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff(
		$old,
		$new,
		$expected,
		$message = '',
		$recursively = false,
		Differ $listDiffer = null
	): void {
		$differ = new MapDiffer($recursively, $listDiffer);

		$actual = $differ->doDiff($old, $new);

		$this->assertArrayEquals($expected, $actual, false, true, $message);
	}

	public function testCallbackComparisonReturningFalse(): void {
		$differ = new MapDiffer(false, null, new StubValueComparer(false));

		$actual = $differ->doDiff([1, '2', 3], [1, '2', 4, 'foo']);

		$expected = [
			new DiffOpChange(1, 1),
			new DiffOpChange('2', '2'),
			new DiffOpChange(3, 4),
			new DiffOpAdd('foo'),
		];

		$this->assertArrayEquals(
			$expected, $actual, false, true,
			'Identical elements should result in change ops when comparison callback always returns false'
		);
	}

	public function testCallbackComparisonReturningTrue(): void {
		$differ = new MapDiffer(false, null, new StubValueComparer(true));

		$actual = $differ->doDiff([1, '2', 'baz'], [1, 'foo', '2']);

		$expected = [];

		$this->assertArrayEquals(
			$expected, $actual, false, true,
			'No change ops should be created when the arrays have '
			. 'the same length and the comparison callback always returns true'
		);
	}

}
