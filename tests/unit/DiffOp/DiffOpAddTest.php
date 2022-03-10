<?php

declare(strict_types=1);

namespace Diff\Tests\DiffOp;

use Diff\DiffOp\DiffOpAdd;

/**
 * @covers  \Diff\DiffOp\DiffOpAdd
 * @covers  \Diff\DiffOp\AtomicDiffOp
 *
 * @group   Diff
 * @group   DiffOp
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpAddTest extends DiffOpTest {

	/**
	 * @return string
	 * @since 0.1
	 *
	 * @see   DiffOpTest::getClass
	 *
	 */
	public function getClass(): string {
		return '\Diff\DiffOp\DiffOpAdd';
	}

	/**
	 * @see   DiffOpTest::constructorProvider
	 *
	 * @since 0.1
	 */
	public function constructorProvider(): array {
		return [
			[true, 'foo'],
			[true, []],
			[true, true],
			[true, 42],
			[true, new DiffOpAdd('spam')],
		];
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetNewValue(DiffOpAdd $diffOp, array $constructorArgs): void {
		$this->assertEquals($constructorArgs[0], $diffOp->getNewValue());
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore(DiffOpAdd $diffOp): void {
		$array = $diffOp->toArray();
		$this->assertArrayHasKey('newvalue', $array);
	}

}
