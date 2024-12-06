<?php

declare( strict_types = 1 );

namespace Diff\Tests\Patcher;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Patcher\ListPatcher;
use Diff\Patcher\Patcher;
use Diff\Tests\DiffTestCase;
use stdClass;

/**
 * @covers \Diff\Patcher\ListPatcher
 * @covers \Diff\Patcher\ThrowingPatcher
 *
 * @group Diff
 * @group DiffPatcher
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListPatcherTest extends DiffTestCase {

	public function patchProvider() {
		$argLists = [];

		$patcher = new ListPatcher();
		$base = [];
		$diff = new Diff();
		$expected = [];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [ 4, 2 ];
		$diff = new Diff();
		$expected = [ 4, 2 ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [];
		$diff = new Diff( [
			new DiffOpAdd( 9001 )
		] );
		$expected = [ 9001 ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [ 4, 2 ];
		$diff = new Diff( [
			new DiffOpAdd( 9001 )
		] );
		$expected = [ 4, 2, 9001 ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [ 4, 2 ];
		$diff = new Diff( [
			new DiffOpAdd( 9001 ),
			new DiffOpAdd( 9002 ),
			new DiffOpAdd( 2 )
		] );
		$expected = [ 4, 2, 9001, 9002, 2 ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [ 0, 1, 2, 3, 4 ];
		$diff = new Diff( [
			new DiffOpRemove( 2 ),
			new DiffOpRemove( 3 ),
		] );
		$expected = [ 0, 1, 4 ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [ 0, 1, 2, 2, 2, 3, 4 ];
		$diff = new Diff( [
			new DiffOpRemove( 2 ),
			new DiffOpRemove( 3 ),
			new DiffOpAdd( 6 ),
			new DiffOpRemove( 2 ),
		] );
		$expected = [ 0, 1, 2, 4, 6 ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		$patcher = new ListPatcher();
		$base = [
			$this->newObject( 'foo' ),
			$this->newObject( 'bar' ),
		];
		$diff = new Diff( [
			new DiffOpRemove( $this->newObject( 'foo' ) ),
			new DiffOpAdd( $this->newObject( 'baz' ) ),
		] );
		$expected = [ $this->newObject( 'bar' ), $this->newObject( 'baz' ) ];

		$argLists[] = [ $patcher, $base, $diff, $expected ];

		return $argLists;
	}

	private function newObject( $value ): stdClass {
		$object = new stdClass();
		$object->element = $value;
		return $object;
	}

	/**
	 * @dataProvider patchProvider
	 *
	 * @param Patcher $patcher
	 * @param array $base
	 * @param Diff $diff
	 * @param array $expected
	 */
	public function testPatch( Patcher $patcher, array $base, Diff $diff, array $expected ) {
		$actual = $patcher->patch( $base, $diff );

		$this->assertArrayEquals( $expected, $actual );
	}

	/**
	 * @dataProvider getApplicableDiffProvider
	 *
	 * @param Diff $diff
	 * @param array $currentObject
	 * @param Diff $expected
	 * @param string|null $message
	 */
	public function testGetApplicableDiff( Diff $diff, array $currentObject, Diff $expected, $message = null ) {
		$patcher = new ListPatcher();
		$actual = $patcher->getApplicableDiff( $currentObject, $diff );

		$this->assertEquals( $expected->getOperations(), $actual->getOperations(), $message );
	}

	public function getApplicableDiffProvider() {
		// Diff, current object, expected
		$argLists = [];

		$diff = new Diff( [], false );
		$currentObject = [];
		$expected = clone $diff;

		$argLists[] = [ $diff, $currentObject, $expected, 'Empty diff should remain empty on empty base' ];

		$diff = new Diff( [], false );

		$currentObject = [ 'foo' => 0, 'bar' => 1 ];

		$expected = clone $diff;

		$argLists[] = [ $diff, $currentObject, $expected, 'Empty diff should remain empty on non-empty base' ];

		$diff = new Diff( [
			new DiffOpRemove( 9001 ),
		], false );

		$currentObject = [];

		$expected = new Diff( [], false );

		$argLists[] = [ $diff, $currentObject, $expected, 'Remove ops should be removed on empty base' ];

		$diff = new Diff( [
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		], false );

		$currentObject = [];

		$expected = new Diff( [
			new DiffOpAdd( 42 ),
		], true );

		$argLists[] = [
			$diff,
			$currentObject,
			$expected,
			'Add ops not present in the base should be retained (ListDiff)'
		];

		$diff = new Diff( [
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		], false );

		$currentObject = [ 1, 42, 9001 ];

		$expected = new Diff( [
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		], false );

		$argLists[] = [
			$diff,
			$currentObject,
			$expected,
			'Add ops with values present in the base should be retained in ListDiff'
		];

		$diff = new Diff( [
			new DiffOpAdd( 42 ),
		], false );

		$currentObject = [];

		$expected = clone $diff;

		$argLists[] = [
			$diff,
			$currentObject,
			$expected,
			'list diffs containing only add ops should be retained even when not in the base'
		];

		$diff = new Diff( [
			new DiffOpRemove( 42 ),
			new DiffOpRemove( 9001 ),
		], false );

		$currentObject = [
			42,
			72010,
			9001,
		];

		$expected = clone $diff;

		$argLists[] = [
			$diff,
			$currentObject,
			$expected,
			'list diffs containing only remove ops should be retained when present in the base'
		];

		return $argLists;
	}

	public function testPatchMapRaisesError() {
		$patcher = new ListPatcher();

		$patcher->patch( [], new Diff( [], true ) );

		$patcher->throwErrors();
		$this->expectException( 'Diff\Patcher\PatcherException' );

		$patcher->patch( [], new Diff( [], true ) );
	}

}
