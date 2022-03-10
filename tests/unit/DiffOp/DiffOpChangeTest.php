<?php

declare(strict_types=1);

namespace Diff\Tests\DiffOp;

use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;

/**
 * @covers  \Diff\DiffOp\DiffOpChange
 * @covers  \Diff\DiffOp\AtomicDiffOp
 *
 * @group   Diff
 * @group   DiffOp
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpChangeTest extends DiffOpTest {

	/**
	 * @return string
	 * @since 0.1
	 *
	 * @see   DiffOpTest::getClass
	 *
	 */
	public function getClass(): string {
		return '\Diff\DiffOp\DiffOpChange';
	}

	/**
	 * @see   DiffOpTest::constructorProvider
	 *
	 * @since 0.1
	 */
	public function constructorProvider(): array {
		return [
			[true, 'foo', 'bar'],
			[true, [9001], [4, 2]],
			[true, true, false],
			[true, true, true],
			[true, 42, 4.2],
			[true, 42, 42],
			[true, 'foo', ['foo']],
			[true, 'foo', null],
			[true, new DiffOpAdd('ham'), new DiffOpAdd('spam')],
			[true, null, null],
		];
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetNewValue(DiffOpChange $diffOp, array $constructorArgs): void {
		$this->assertEquals($constructorArgs[0], $diffOp->getOldValue());
		$this->assertEquals($constructorArgs[1], $diffOp->getNewValue());
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore(DiffOpChange $diffOp): void {
		$array = $diffOp->toArray();
		$this->assertArrayHasKey('newvalue', $array);
		$this->assertArrayHasKey('oldvalue', $array);
	}

}
