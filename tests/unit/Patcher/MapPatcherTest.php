<?php

declare(strict_types=1);

namespace Diff\Tests\Patcher;

use Diff\Comparer\CallbackComparer;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Diff\Patcher\MapPatcher;
use Diff\Patcher\Patcher;
use Diff\Tests\DiffTestCase;

/**
 * @covers  \Diff\Patcher\MapPatcher
 * @covers  \Diff\Patcher\ThrowingPatcher
 *
 * @group   Diff
 * @group   DiffPatcher
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author  Daniel Kinzler
 */
class MapPatcherTest extends DiffTestCase {

	public function patchProvider(): array {
		$argLists = [];

		$patcher = new MapPatcher();
		$base = [];
		$diff = new Diff();
		$expected = [];

		$argLists['all empty'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = ['foo', 'bar' => ['baz']];
		$diff = new Diff();
		$expected = ['foo', 'bar' => ['baz']];

		$argLists['empty patch'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = ['foo', 'bar' => ['baz']];
		$diff = new Diff(['bah' => new DiffOpAdd('blah')]);
		$expected = ['foo', 'bar' => ['baz'], 'bah' => 'blah'];

		$argLists['add'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = ['foo', 'bar' => ['baz']];
		$diff = new Diff(['bah' => new DiffOpAdd('blah')]);
		$expected = ['foo', 'bar' => ['baz'], 'bah' => 'blah'];

		$argLists['add2'] = [$patcher, $base, $diff, $expected]; //FIXME: dupe?

		$patcher = new MapPatcher();
		$base = [];
		$diff = new Diff(
			[
				'foo' => new DiffOpAdd('bar'),
				'bah' => new DiffOpAdd('blah'),
			]
		);
		$expected = [
			'foo' => 'bar',
			'bah' => 'blah',
		];

		$argLists['add to empty base'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = [
			'enwiki' => [
				'name' => 'Nyan Cat',
				'badges' => ['FA'],
			],
		];
		$diff = new Diff(
			[
				'nlwiki' => new Diff(
					[
						'name' => new DiffOpAdd('Nyan Cat'),
						'badges' => new Diff(
							[
								new DiffOpAdd('J approves'),
							],
							false
						),
					],
					true
				),
			],
			true
		);
		$expected = [
			'enwiki' => [
				'name' => 'Nyan Cat',
				'badges' => ['FA'],
			],

			'nlwiki' => [
				'name' => 'Nyan Cat',
				'badges' => ['J approves'],
			],
		];

		$argLists['add to non-existent key'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = [
			'foo' => 'bar',
			'nyan' => 'cat',
			'bah' => 'blah',
		];
		$diff = new Diff(
			[
				'foo' => new DiffOpRemove('bar'),
				'bah' => new DiffOpRemove('blah'),
			]
		);
		$expected = [
			'nyan' => 'cat',
		];

		$argLists['remove'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = [
			'foo' => 'bar',
			'nyan' => 'cat',
			'spam' => 'blah',
			'bah' => 'blah',
		];
		$diff = new Diff(
			[
				'foo' => new DiffOpChange('bar', 'baz'),
				'bah' => new DiffOpRemove('blah'),
				'oh' => new DiffOpAdd('noez'),
			]
		);
		$expected = [
			'foo' => 'baz',
			'nyan' => 'cat',
			'spam' => 'blah',
			'oh' => 'noez',
		];

		$argLists['change/add/remove'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = [
			'foo' => 'bar',
		];
		$diff = new Diff(
			[
				'baz' => new Diff([new DiffOpAdd('ny'), new DiffOpAdd('an')], false),
			]
		);
		$expected = [
			'foo' => 'bar',
			'baz' => ['ny', 'an'],
		];

		$argLists['add to substructure'] = [$patcher, $base, $diff, $expected];

		// ---- conflicts ----

		$patcher = new MapPatcher();
		$base = [];
		$diff = new Diff(
			[
				'baz' => new DiffOpRemove('X'),
			]
		);
		$expected = $base;

		$argLists['conflict: remove missing'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = ['baz' => 'Y'];
		$diff = new Diff(
			[
				'baz' => new DiffOpRemove('X'),
			]
		);
		$expected = $base;

		$argLists['conflict: remove mismatching value'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = ['baz' => 'Y'];
		$diff = new Diff(
			[
				'baz' => new DiffOpAdd('X'),
			]
		);
		$expected = $base;

		$argLists['conflict: add existing'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = ['baz' => 'Y'];
		$diff = new Diff(
			[
				'baz' => new DiffOpChange('X', 'Z'),
			]
		);
		$expected = $base;

		$argLists['conflict: change mismatching value'] = [$patcher, $base, $diff, $expected];

		$patcher = new MapPatcher();
		$base = [
			'foo' => 'bar',
			'nyan' => 'cat',
			'spam' => 'blah',
			'bah' => 'blah',
		];
		$diff = new Diff(
			[
				'foo' => new DiffOpChange('bar', 'var'),
				'nyan' => new DiffOpRemove('fat'),
				'bah' => new DiffOpChange('blubb', 'clubb'),
				'yea' => new DiffOpAdd('stuff'),
			]
		);
		$expected = [
			'foo' => 'var',
			'nyan' => 'cat',
			'spam' => 'blah',
			'bah' => 'blah',
			'yea' => 'stuff',
		];

		$argLists['some mixed conflicts'] = [$patcher, $base, $diff, $expected];

		return $argLists;
	}

	/**
	 * @dataProvider patchProvider
	 *
	 * @param Patcher $patcher
	 * @param array   $base
	 * @param Diff    $diff
	 * @param array   $expected
	 */
	public function testPatch(Patcher $patcher, array $base, Diff $diff, array $expected): void {
		$actual = $patcher->patch($base, $diff);

		$this->assertArrayEquals($expected, $actual, true, true);
	}

	public function getApplicableDiffProvider(): array {
		// Diff, current object, expected
		$argLists = [];

		$diff = new Diff([], true);
		$currentObject = [];
		$expected = clone $diff;

		$argLists[] = [$diff, $currentObject, $expected, 'Empty diff should remain empty on empty base'];

		$diff = new Diff([], true);

		$currentObject = ['foo' => 0, 'bar' => 1];

		$expected = clone $diff;

		$argLists[] = [$diff, $currentObject, $expected, 'Empty diff should remain empty on non-empty base'];

		$diff = new Diff(
			[
				'foo' => new DiffOpChange(0, 42),
				'bar' => new DiffOpChange(1, 9001),
			],
			true
		);

		$currentObject = ['foo' => 0, 'bar' => 1];

		$expected = clone $diff;

		$argLists[] = [$diff, $currentObject, $expected, 'Diff should not be altered on matching base'];

		$diff = new Diff(
			[
				'foo' => new DiffOpChange(0, 42),
				'bar' => new DiffOpChange(1, 9001),
			],
			true
		);
		$currentObject = [];

		$expected = new Diff([], true);

		$argLists[] = [$diff, $currentObject, $expected, 'Diff with only change ops should be empty on empty base'];

		$diff = new Diff(
			[
				'foo' => new DiffOpChange(0, 42),
				'bar' => new DiffOpChange(1, 9001),
			],
			true
		);

		$currentObject = ['foo' => 'something else', 'bar' => 1, 'baz' => 'o_O'];

		$expected = new Diff(
			[
				'bar' => new DiffOpChange(1, 9001),
			],
			true
		);

		$argLists[] = [$diff, $currentObject, $expected, 'Only change ops present in the base should be retained'];

		$diff = new Diff(
			[
				'bar' => new DiffOpRemove(9001),
			],
			true
		);

		$currentObject = [];

		$expected = new Diff([], true);

		$argLists[] = [$diff, $currentObject, $expected, 'Remove ops should be removed on empty base'];

		$diff = new Diff(
			[
				'foo' => new DiffOpAdd(42),
				'bar' => new DiffOpRemove(9001),
			],
			true
		);

		$currentObject = ['foo' => 'bar'];

		$expected = new Diff([], true);

		$argLists[] = [
			$diff,
			$currentObject,
			$expected,
			'Mismatching add ops and remove ops not present in base should be removed',
		];

		$diff = new Diff(
			[
				'foo' => new DiffOpAdd(42),
				'bar' => new DiffOpRemove(9001),
			],
			true
		);

		$currentObject = ['foo' => 42, 'bar' => 9001];

		$expected = new Diff(
			[
				'bar' => new DiffOpRemove(9001),
			],
			true
		);

		$argLists[] = [$diff, $currentObject, $expected, 'Remove ops present in base should be retained'];

		$diff = new Diff(
			[
				'foo' => new DiffOpAdd(42),
				'bar' => new DiffOpRemove(9001),
			],
			true
		);

		$currentObject = [];

		$expected = new Diff(
			[
				'foo' => new DiffOpAdd(42),
			],
			true
		);

		$argLists[] = [
			$diff,
			$currentObject,
			$expected,
			'Add ops not present in the base should be retained (MapDiff)',
		];

		$diff = new Diff(
			[
				'foo' => new Diff(['bar' => new DiffOpChange(0, 1)], true),
				'le-non-existing-element' => new Diff(['bar' => new DiffOpChange(0, 1)], true),
				'spam' => new Diff([new DiffOpAdd(42)], false),
				new DiffOpAdd(9001),
			],
			true
		);

		$currentObject = [
			'foo' => ['bar' => 0, 'baz' => 'O_o'],
			'spam' => [23, 'ohi'],
		];

		$expected = new Diff(
			[
				'foo' => new Diff(['bar' => new DiffOpChange(0, 1)], true),
				'spam' => new Diff([new DiffOpAdd(42)], false),
				new DiffOpAdd(9001),
			],
			true
		);

		$argLists[] = [$diff, $currentObject, $expected, 'Recursion should work properly'];

		return $argLists;
	}

	/**
	 * @dataProvider getApplicableDiffProvider
	 *
	 * @param Diff        $diff
	 * @param array       $currentObject
	 * @param Diff        $expected
	 * @param string|null $message
	 */
	public function testGetApplicableDiff(
		Diff $diff,
		array $currentObject,
		Diff $expected,
		?string $message = null
	): void {
		$patcher = new MapPatcher();
		$actual = $patcher->getApplicableDiff($currentObject, $diff);

		$this->assertEquals($expected->getOperations(), $actual->getOperations(), $message);
	}

	public function testSetValueComparerToAlwaysFalse(): void {
		$patcher = new MapPatcher();

		$patcher->setValueComparer(
			new CallbackComparer(function () {
				return false;
			})
		);

		$baseMap = [
			'foo' => 42,
			'bar' => 9001,
		];

		$patch = new Diff(
			[
				'foo' => new DiffOpChange(42, 1337),
				'bar' => new DiffOpChange(9001, 1337),
			]
		);

		$patchedMap = $patcher->patch($baseMap, $patch);

		$this->assertEquals($baseMap, $patchedMap);
	}

	public function testSetValueComparerToAlwaysTrue(): void {
		$patcher = new MapPatcher();

		$patcher->setValueComparer(
			new CallbackComparer(function () {
				return true;
			})
		);

		$baseMap = [
			'foo' => 42,
			'bar' => 9001,
		];

		$patch = new Diff(
			[
				'foo' => new DiffOpChange(3, 1337),
				'bar' => new DiffOpChange(3, 1337),
			]
		);

		$expectedMap = [
			'foo' => 1337,
			'bar' => 1337,
		];

		$patchedMap = $patcher->patch($baseMap, $patch);

		$this->assertEquals($expectedMap, $patchedMap);
	}

	public function testErrorOnUnknownDiffOpType(): void {
		$patcher = new MapPatcher();

		$diffOp = $this->createMock('Diff\DiffOp\DiffOp');

		$diffOp->expects($this->any())
			->method('getType')
			->will($this->returnValue('diff'));

		$diff = new Diff([$diffOp], true);

		$patcher->patch([], $diff);

		$patcher->throwErrors();
		$this->expectException('Diff\Patcher\PatcherException');

		$patcher->patch([], $diff);
	}

}
