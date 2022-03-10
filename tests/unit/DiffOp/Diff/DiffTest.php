<?php

declare(strict_types=1);

namespace Diff\Tests\DiffOp\Diff;

use Closure;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;
use Generator;
use stdClass;

/**
 * @covers  \Diff\DiffOp\Diff\Diff
 *
 * @group   Diff
 * @group   DiffOp
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author  Thiemo Kreuz
 */
class DiffTest extends DiffTestCase {

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testGetAdditions(array $operations): void {
		$diff = new Diff($operations, true);

		$additions = [];

		foreach ($operations as $operation) {
			if ($operation->getType() == 'add') {
				$additions[] = $operation;
			}
		}

		$this->assertArrayEquals($additions, $diff->getAdditions());
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testGetRemovals(array $operations): void {
		$diff = new Diff($operations, true);

		$removals = [];

		foreach ($operations as $operation) {
			if ($operation->getType() == 'remove') {
				$removals[] = $operation;
			}
		}

		$this->assertArrayEquals($removals, $diff->getRemovals());
	}

	public function testGetType(): void {
		$diff = new Diff();
		$this->assertIsString($diff->getType());
	}

	public function testPreSetElement(): void {
		$this->expectException('Exception');

		$diff = new Diff([], false);
		$diff[] = new DiffOpChange(0, 1);
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testAddOperations(array $operations): void {
		$diff = new Diff();

		$diff->addOperations($operations);

		$this->assertArrayEquals($operations, $diff->getOperations());
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testStuff(array $operations): void {
		$diff = new Diff($operations);

		$this->assertInstanceOf('Diff\DiffOp\Diff\Diff', $diff);
		$this->assertInstanceOf('ArrayObject', $diff);

		$types = [];

		$this->assertContainsOnlyInstancesOf('Diff\DiffOp\DiffOp', $diff);

		/**
		 * @var DiffOp $operation
		 */
		foreach ($diff as $operation) {
			if (!in_array($operation->getType(), $types)) {
				$types[] = $operation->getType();
			}
		}

		$count = 0;

		foreach ($types as $type) {
			$count += count($diff->getTypeOperations($type));
		}

		$this->assertEquals($count, $diff->count());
	}

	public function instanceProvider(): Generator {
		foreach ($this->elementInstancesProvider() as $args) {
			yield [new Diff($args[0])];
		}
	}

	public function elementInstancesProvider(): array {
		return [
			[
				[
				],
			],
			[
				[
					new DiffOpAdd('ohi'),
				],
			],
			[
				[
					new DiffOpRemove('ohi'),
				],
			],
			[
				[
					new DiffOpAdd('ohi'),
					new DiffOpRemove('there'),
				],
			],
			[
				[
				],
			],
			[
				[
					new DiffOpAdd('ohi'),
					new DiffOpRemove('there'),
					new DiffOpChange('ohi', 'there'),
				],
			],
			[
				[
					'1' => new DiffOpAdd('ohi'),
					'33' => new DiffOpRemove('there'),
					'7' => new DiffOpChange('ohi', 'there'),
				],
			],
		];
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetOperations(Diff $diff): void {
		$ops = $diff->getOperations();

		$this->assertIsArray($ops);
		$this->assertContainsOnlyInstancesOf('Diff\DiffOp\DiffOp', $ops);
		$this->assertArrayEquals($ops, $diff->getOperations());
	}

	public function testRemoveEmptyOperations(): void {
		$diff = new Diff([]);

		$diff['foo'] = new DiffOpAdd(1);
		$diff['bar'] = new Diff([new DiffOpAdd(1)], true);
		$diff['baz'] = new Diff([new DiffOpAdd(1)], false);
		$diff['bah'] = new Diff([], false);
		$diff['spam'] = new Diff([], true);

		$diff->removeEmptyOperations();

		$this->assertTrue($diff->offsetExists('foo'));
		$this->assertTrue($diff->offsetExists('bar'));
		$this->assertTrue($diff->offsetExists('baz'));
		$this->assertFalse($diff->offsetExists('bah'));
		$this->assertFalse($diff->offsetExists('spam'));
	}

	public function looksAssociativeProvider(): array {
		return [
			[new Diff(), false],
			[new Diff([], false), false],
			[new Diff([], true), true],
			[new Diff([new DiffOpAdd('')]), false],
			[new Diff([new DiffOpRemove('')]), false],
			[new Diff([new DiffOpRemove(''), new DiffOpAdd('')]), false],
			[new Diff([new DiffOpRemove('')], true), true],
			[new Diff(['onoez' => new DiffOpChange('', 'spam')]), true],
			[new Diff([new Diff()]), true],
		];
	}

	/**
	 * @dataProvider looksAssociativeProvider
	 */
	public function testLooksAssociative(Diff $diff, $looksAssoc): void {
		$this->assertEquals($looksAssoc, $diff->looksAssociative());

		if (!$diff->looksAssociative()) {
			$this->assertFalse($diff->hasAssociativeOperations());
		}
	}

	public function isAssociativeProvider(): array {
		return [
			[new Diff(), null],
			[new Diff([], false), false],
			[new Diff([], true), true],
			[new Diff([new DiffOpAdd('')]), null],
			[new Diff([new DiffOpRemove('')], false), false],
			[new Diff([new DiffOpRemove(''), new DiffOpAdd('')]), null],
			[new Diff([new DiffOpRemove('')], true), true],
			[new Diff(['onoez' => new DiffOpChange('', 'spam')]), null],
			[new Diff([new Diff()]), null],
		];
	}

	/**
	 * @dataProvider isAssociativeProvider
	 */
	public function testIsAssociative(Diff $diff, $isAssoc): void {
		$this->assertEquals($isAssoc, $diff->isAssociative());
	}

	public function hasAssociativeOperationsProvider(): array {
		return [
			[new Diff(), false],
			[new Diff([], false), false],
			[new Diff([], true), false],
			[new Diff([new DiffOpAdd('')]), false],
			[new Diff([new DiffOpRemove('')], false), false],
			[new Diff([new DiffOpRemove(''), new DiffOpAdd('')], true), false],
			[new Diff([new DiffOpRemove('')], true), false],
			[new Diff(['onoez' => new DiffOpChange('', 'spam')]), true],
			[new Diff([new Diff()]), true],
		];
	}

	/**
	 * @dataProvider hasAssociativeOperationsProvider
	 */
	public function testHasAssociativeOperations(Diff $diff, $hasAssocOps): void {
		$this->assertEquals($hasAssocOps, $diff->hasAssociativeOperations());
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @param DiffOp[] $elements
	 * @since        0.6
	 *
	 */
	public function testConstructor(array $elements): void {
		$arrayObject = new Diff($elements);

		$this->assertEquals(count($elements), $arrayObject->count());
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @param DiffOp[] $elements
	 * @since        0.6
	 *
	 */
	public function testIsEmpty(array $elements): void {
		$arrayObject = new Diff($elements);

		$this->assertEquals($elements === [], $arrayObject->isEmpty());
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param Diff $list
	 * @since        0.6
	 *
	 */
	public function testUnset(Diff $list): void {
		if ($list->isEmpty()) {
			$this->assertTrue(true); // We cannot test unset if there are no elements
		} else {
			$offset = $list->getIterator()->key();
			$count = $list->count();
			$list->offsetUnset($offset);
			$this->assertEquals($count - 1, $list->count());
		}

		if (!$list->isEmpty()) {
			$offset = $list->getIterator()->key();
			$count = $list->count();
			unset($list[$offset]);
			$this->assertEquals($count - 1, $list->count());
		}
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @param DiffOp[] $elements
	 * @since        0.6
	 *
	 */
	public function testAppend(array $elements): void {
		$list = new Diff();

		$listSize = count($elements);

		foreach ($elements as $element) {
			$list->append($element);
		}

		$this->assertEquals($listSize, $list->count());

		$list = new Diff();

		foreach ($elements as $element) {
			$list[] = $element;
		}

		$this->assertEquals($listSize, $list->count());

		$this->checkTypeChecks(function (Diff $list, $element) {
			$list->append($element);
		});
	}

	/**
	 * @param Closure $function
	 */
	private function checkTypeChecks(Closure $function): void {
		$excption = null;
		$list = new Diff();

		foreach ([42, 'foo', [], new stdClass(), 4.2] as $element) {
			$this->assertInvalidArgument($function, $list, $element);
		}
	}

	/**
	 * Asserts that an InvalidArgumentException gets thrown when calling the provided
	 * callable. Extra arguments specified to the method are also provided to the callable.
	 *
	 * @param Closure $function
	 */
	private function assertInvalidArgument(Closure $function): void {
		$this->expectException('InvalidArgumentException');

		$arguments = func_get_args();
		array_shift($arguments);

		call_user_func_array($function, $arguments);
	}

	public function testGetAddedValues(): void {
		$diff = new Diff(
			[
				new DiffOpAdd(0),
				new DiffOpRemove(1),
				new DiffOpAdd(2),
				new DiffOpRemove(3),
				new DiffOpAdd(4),
				new DiffOpChange(7, 5),
				new Diff([new DiffOpAdd(9)]),
			]
		);

		$addedValues = $diff->getAddedValues();

		$this->assertIsArray($addedValues);

		$this->assertArrayEquals([0, 2, 4], $addedValues);

		$diff = new Diff();
		$this->assertArrayEquals([], $diff->getAddedValues());
	}

	public function testGetRemovedValues(): void {
		$diff = new Diff(
			[
				new DiffOpAdd(0),
				new DiffOpRemove(1),
				new DiffOpAdd(2),
				new DiffOpRemove(3),
				new DiffOpAdd(4),
				new DiffOpChange(6, 4),
				new Diff([new DiffOPRemove(8)]),
			]
		);

		$removedValues = $diff->getRemovedValues();

		$this->assertIsArray($removedValues);

		$this->assertArrayEquals([1, 3], $removedValues);

		$diff = new Diff();
		$this->assertArrayEquals([], $diff->getRemovedValues());
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @param DiffOp[] $elements
	 * @since        0.6
	 *
	 */
	public function testOffsetSet(array $elements): void {
		if ($elements === []) {
			$this->assertTrue(true);

			return;
		}

		$list = new Diff();

		$element = reset($elements);
		$list->offsetSet(42, $element);
		$this->assertEquals($element, $list->offsetGet(42));

		$list = new Diff();

		$element = reset($elements);
		$list['oHai'] = $element;
		$this->assertEquals($element, $list['oHai']);

		$list = new Diff();

		$element = reset($elements);
		$list->offsetSet(9001, $element);
		$this->assertEquals($element, $list[9001]);

		$list = new Diff();

		$element = reset($elements);
		$list->offsetSet(null, $element);
		$this->assertEquals($element, $list[0]);

		$list = new Diff();
		$offset = 0;

		foreach ($elements as $element) {
			$list->offsetSet(null, $element);
			$this->assertEquals($element, $list[$offset++]);
		}

		$this->assertEquals(count($elements), $list->count());

		$this->checkTypeChecks(function (Diff $list, $element) {
			$list->offsetSet(mt_rand(), $element);
		});
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param Diff $list
	 * @since        0.6
	 *
	 */
	public function testSerialization(Diff $list): void {
		$serialization = serialize($list);
		$copy = unserialize($serialization);

		$this->assertSame($serialization, serialize($copy));
		$this->assertSame($list->count(), $copy->count());

		$list = $list->getArrayCopy();
		$copy = $copy->getArrayCopy();

		$this->assertArrayEquals($list, $copy, true, true);
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param Diff $list
	 * @since        0.6
	 *
	 */
	public function testAddInvalidDiffOp(Diff $list): void {
		$invalidDiffOp = $this->createMock('Diff\DiffOp\DiffOp');

		$invalidDiffOp->expects($this->atLeastOnce())
			->method('getType')
			->will($this->returnValue('~=[,,_,,]:3'));

		$this->expectException('Exception');

		$list->append($invalidDiffOp);
	}

	/**
	 * @dataProvider invalidIsAssociativeProvider
	 */
	public function testConstructWithInvalidIsAssociative($isAssociative): void {
		$this->expectException('InvalidArgumentException');
		new Diff([], $isAssociative);
	}

	public function invalidIsAssociativeProvider() {
		return [
			[1],
			['1'],
			['null'],
			[0],
			[[]],
			['foobar'],
		];
	}

	/**
	 * @dataProvider invalidDiffOpsProvider
	 */
	public function testConstructorWithInvalidDiffOps(array $diffOps): void {
		$this->expectException('InvalidArgumentException');
		new Diff($diffOps);
	}

	public function invalidDiffOpsProvider(): array {
		return [
			[
				[
					'foo',
				],
			],
			[
				[
					null,
				],
			],
			[
				[
					false,
					true,
					[],
				],
			],
			[
				[
					new DiffOpAdd(42),
					'in your list',
					new DiffOpAdd(9001),
				],
			],
		];
	}

	/**
	 * @dataProvider equalsProvider
	 */
	public function testEquals(Diff $diff, Diff $target): void {
		$this->assertTrue($diff->equals($target));
		$this->assertTrue($target->equals($diff));
	}

	public function equalsProvider(): array {
		$empty = new Diff();

		return [
			// Identity
			[$empty, $empty],

			// Empty diffs
			[$empty, new Diff()],
			[$empty, new Diff([], null)],

			// Simple diffs
			[new Diff([new DiffOpAdd(1)]), new Diff([new DiffOpAdd(1)])],
			[new Diff([new DiffOpAdd(1)]), new Diff([new DiffOpAdd('1')])],
		];
	}

	/**
	 * @dataProvider notEqualsProvider
	 */
	public function testNotEquals(Diff $diff, $target): void {
		$this->assertFalse($diff->equals($target));
	}

	public function notEqualsProvider(): array {
		return [
			// Not an instance or subclass of Diff
			[new Diff(), null],
			[new Diff(), new DiffOpAdd(1)],

			// Empty diffs
			[new Diff(), new Diff([], false)],
			[new Diff(), new Diff([], true)],

			// Simple diffs
			[new Diff(), new Diff([new DiffOpAdd(1)])],
			[new Diff([new DiffOpAdd(1)]), new Diff([new DiffOpRemove(1)])],
			[new Diff([new DiffOpAdd(1)]), new Diff([new DiffOpAdd(2)])],
		];
	}

	public function testWhenThereAreNoChangeOperations_getChangesReturnsEmptyArray(): void {
		$this->assertSame(
			[],
			(new Diff(
				[
					new DiffOpAdd(1),
					new DiffOpRemove(2),
				]
			))->getChanges()
		);
	}

	public function testWhenThereAreChangeOperations_getChangesReturnsThem(): void {
		$changeOperations = [
			new DiffOpChange(1, 2),
			new DiffOpChange(3, 4),
		];

		$this->assertEquals(
			$changeOperations,
			(new Diff($changeOperations))->getChanges()
		);
	}

}
