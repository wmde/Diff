<?php

declare(strict_types=1);

namespace Diff\Tests\DiffOp\Diff;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffOp\DiffOpTest;
use stdClass;

/**
 * @covers  \Diff\DiffOp\Diff\Diff
 *
 * @group   Diff
 * @group   DiffOp
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffAsOpTest extends DiffOpTest {

	/**
	 * @return string
	 * @since 0.5
	 *
	 * @see   DiffOpTest::getClass
	 *
	 */
	public function getClass(): string {
		return '\Diff\DiffOp\Diff\Diff';
	}

	/**
	 * @see   DiffOpTest::constructorProvider
	 *
	 * @since 0.5
	 */
	public function constructorProvider(): array {
		$argLists = [
			[true, []],
			[true, [new DiffOpAdd(42)]],
			[true, [new DiffOpRemove(new DiffOpRemove('spam'))]],
			[true, [new Diff([new DiffOpRemove(new DiffOpRemove('spam'))])]],
			[true, [new DiffOpAdd(42), new DiffOpAdd(42)]],
			[true, ['a' => new DiffOpAdd(42), 'b' => new DiffOpAdd(42)]],
			[true, [new DiffOpAdd(42), 'foo bar baz' => new DiffOpAdd(42)]],
			[true, [42 => new DiffOpRemove(42), '9001' => new DiffOpAdd(42)]],
			[true, [42 => new DiffOpRemove(new stdClass()), '9001' => new DiffOpAdd(new stdClass())]],
		];

		$allArgLists = $argLists;

		foreach ($argLists as $argList) {
			foreach ([true, false, null] as $isAssoc) {
				$argList[] = $isAssoc;
				$allArgLists[] = $argList;
			}
		}

		return $allArgLists;
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore(Diff $diffOp): void {
		$array = $diffOp->toArray();

		$this->assertArrayHasKey('operations', $array);
		$this->assertIsArray($array['operations']);

		$this->assertArrayHasKey('isassoc', $array);

		$this->assertTrue(
			is_bool($array['isassoc']) || $array['isassoc'] === null,
			'The isassoc element needs to be a boolean or null'
		);
	}

}
