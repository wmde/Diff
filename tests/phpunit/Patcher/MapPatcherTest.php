<?php

declare( strict_types = 1 );

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
 * @covers Diff\Patcher\MapPatcher
 * @covers Diff\Patcher\ThrowingPatcher
 *
 * @group Diff
 * @group DiffPatcher
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
class MapPatcherTest extends DiffTestCase {

	public function patchProvider() {
		$argLists = array();

		$patcher = new MapPatcher();
		$base = array();
		$diff = new Diff();
		$expected = array();

		$argLists['all empty'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array( 'foo', 'bar' => array( 'baz' ) );
		$diff = new Diff();
		$expected = array( 'foo', 'bar' => array( 'baz' ) );

		$argLists['empty patch'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array( 'foo', 'bar' => array( 'baz' ) );
		$diff = new Diff( array( 'bah' => new DiffOpAdd( 'blah' ) ) );
		$expected = array( 'foo', 'bar' => array( 'baz' ), 'bah' => 'blah' );

		$argLists['add'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array( 'foo', 'bar' => array( 'baz' ) );
		$diff = new Diff( array( 'bah' => new DiffOpAdd( 'blah' ) ) );
		$expected = array( 'foo', 'bar' => array( 'baz' ), 'bah' => 'blah' );

		$argLists['add2'] = array( $patcher, $base, $diff, $expected ); //FIXME: dupe?

		$patcher = new MapPatcher();
		$base = array();
		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 'bar' ),
			'bah' => new DiffOpAdd( 'blah' )
		) );
		$expected = array(
			'foo' => 'bar',
			'bah' => 'blah'
		);

		$argLists['add to empty base'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array(
			'enwiki' => array(
				'name'   => 'Nyan Cat',
				'badges' => array( 'FA' )
			)
		);
		$diff = new Diff( array(
			'nlwiki' => new Diff( array(
				'name'   => new DiffOpAdd( 'Nyan Cat' ),
				'badges' => new Diff( array(
					new DiffOpAdd( 'J approves' ),
				), false ),
			), true ),
		), true );
		$expected = array(
			'enwiki' => array(
				'name'   => 'Nyan Cat',
				'badges' => array( 'FA' )
			),

			'nlwiki' => array(
				'name'   => 'Nyan Cat',
				'badges' => array( 'J approves' )
			)
		);

		$argLists['add to non-existent key'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array(
			'foo' => 'bar',
			'nyan' => 'cat',
			'bah' => 'blah',
		);
		$diff = new Diff( array(
			'foo' => new DiffOpRemove( 'bar' ),
			'bah' => new DiffOpRemove( 'blah' ),
		) );
		$expected = array(
			'nyan' => 'cat'
		);

		$argLists['remove'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array(
			'foo' => 'bar',
			'nyan' => 'cat',
			'spam' => 'blah',
			'bah' => 'blah',
		);
		$diff = new Diff( array(
			'foo' => new DiffOpChange( 'bar', 'baz' ),
			'bah' => new DiffOpRemove( 'blah' ),
			'oh' => new DiffOpAdd( 'noez' ),
		) );
		$expected = array(
			'foo' => 'baz',
			'nyan' => 'cat',
			'spam' => 'blah',
			'oh' => 'noez',
		);

		$argLists['change/add/remove'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array(
			'foo' => 'bar',
		);
		$diff = new Diff( array(
			'baz' => new Diff( array( new DiffOpAdd( 'ny' ), new DiffOpAdd( 'an' ) ), false ),
		) );
		$expected = array(
			'foo' => 'bar',
			'baz' => array( 'ny', 'an' ),
		);

		$argLists['add to substructure'] = array( $patcher, $base, $diff, $expected );

		// ---- conflicts ----

		$patcher = new MapPatcher();
		$base = array();
		$diff = new Diff( array(
			'baz' => new DiffOpRemove( 'X' ),
		) );
		$expected = $base;

		$argLists['conflict: remove missing'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array( 'baz' => 'Y' );
		$diff = new Diff( array(
			'baz' => new DiffOpRemove( 'X' ),
		) );
		$expected = $base;

		$argLists['conflict: remove mismatching value'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array( 'baz' => 'Y' );
		$diff = new Diff( array(
			'baz' => new DiffOpAdd( 'X' ),
		) );
		$expected = $base;

		$argLists['conflict: add existing'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array( 'baz' => 'Y' );
		$diff = new Diff( array(
			'baz' => new DiffOpChange( 'X', 'Z' ),
		) );
		$expected = $base;

		$argLists['conflict: change mismatching value'] = array( $patcher, $base, $diff, $expected );

		$patcher = new MapPatcher();
		$base = array(
			'foo' => 'bar',
			'nyan' => 'cat',
			'spam' => 'blah',
			'bah' => 'blah',
		);
		$diff = new Diff( array(
			'foo' => new DiffOpChange( 'bar', 'var' ),
			'nyan' => new DiffOpRemove( 'fat' ),
			'bah' => new DiffOpChange( 'blubb', 'clubb' ),
			'yea' => new DiffOpAdd( 'stuff' ),
		) );
		$expected = array(
			'foo' => 'var',
			'nyan' => 'cat',
			'spam' => 'blah',
			'bah' => 'blah',
			'yea' => 'stuff',
		);

		$argLists['some mixed conflicts'] = array( $patcher, $base, $diff, $expected );

		return $argLists;
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

		$this->assertArrayEquals( $expected, $actual, true, true );
	}

	public function getApplicableDiffProvider() {
		// Diff, current object, expected
		$argLists = array();

		$diff = new Diff( array(), true );
		$currentObject = array();
		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Empty diff should remain empty on empty base' );

		$diff = new Diff( array(), true );

		$currentObject = array( 'foo' => 0, 'bar' => 1 );

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Empty diff should remain empty on non-empty base' );

		$diff = new Diff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );

		$currentObject = array( 'foo' => 0, 'bar' => 1 );

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Diff should not be altered on matching base' );

		$diff = new Diff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );
		$currentObject = array();

		$expected = new Diff( array(), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Diff with only change ops should be empty on empty base' );

		$diff = new Diff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );

		$currentObject = array( 'foo' => 'something else', 'bar' => 1, 'baz' => 'o_O' );

		$expected = new Diff( array(
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Only change ops present in the base should be retained' );

		$diff = new Diff( array(
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array();

		$expected = new Diff( array(), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Remove ops should be removed on empty base' );

		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array( 'foo' => 'bar' );

		$expected = new Diff( array(), true );

		$argLists[] = array(
			$diff,
			$currentObject,
			$expected,
			'Mismatching add ops and remove ops not present in base should be removed'
		);

		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array( 'foo' => 42, 'bar' => 9001 );

		$expected = new Diff( array(
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Remove ops present in base should be retained' );

		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array();

		$expected = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
		), true );

		$argLists[] = array(
			$diff,
			$currentObject,
			$expected,
			'Add ops not present in the base should be retained (MapDiff)'
		);

		$diff = new Diff( array(
			'foo' => new Diff( array( 'bar' => new DiffOpChange( 0, 1 ) ), true ),
			'le-non-existing-element' => new Diff( array( 'bar' => new DiffOpChange( 0, 1 ) ), true ),
			'spam' => new Diff( array( new DiffOpAdd( 42 ) ), false ),
			new DiffOpAdd( 9001 ),
		), true );

		$currentObject = array(
			'foo' => array( 'bar' => 0, 'baz' => 'O_o' ),
			'spam' => array( 23, 'ohi' )
		);

		$expected = new Diff( array(
			'foo' => new Diff( array( 'bar' => new DiffOpChange( 0, 1 ) ), true ),
			'spam' => new Diff( array( new DiffOpAdd( 42 ) ), false ),
			new DiffOpAdd( 9001 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Recursion should work properly' );

		return $argLists;
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
		$patcher = new MapPatcher();
		$actual = $patcher->getApplicableDiff( $currentObject, $diff );

		$this->assertEquals( $expected->getOperations(), $actual->getOperations(), $message );
	}

	public function testSetValueComparerToAlwaysFalse() {
		$patcher = new MapPatcher();

		$patcher->setValueComparer( new CallbackComparer( function( $firstValue, $secondValue ) {
			return false;
		} ) );

		$baseMap = array(
			'foo' => 42,
			'bar' => 9001,
		);

		$patch = new Diff( array(
			'foo' => new DiffOpChange( 42, 1337 ),
			'bar' => new DiffOpChange( 9001, 1337 ),
		) );

		$patchedMap = $patcher->patch( $baseMap, $patch );

		$this->assertEquals( $baseMap, $patchedMap );
	}

	public function testSetValueComparerToAlwaysTrue() {
		$patcher = new MapPatcher();

		$patcher->setValueComparer( new CallbackComparer( function( $firstValue, $secondValue ) {
			return true;
		} ) );

		$baseMap = array(
			'foo' => 42,
			'bar' => 9001,
		);

		$patch = new Diff( array(
			'foo' => new DiffOpChange( 3, 1337 ),
			'bar' => new DiffOpChange( 3, 1337 ),
		) );

		$expectedMap = array(
			'foo' => 1337,
			'bar' => 1337,
		);

		$patchedMap = $patcher->patch( $baseMap, $patch );

		$this->assertEquals( $expectedMap, $patchedMap );
	}

	public function testErrorOnUnknownDiffOpType() {
		$patcher = new MapPatcher();

		$diffOp = $this->createMock( 'Diff\DiffOp\DiffOp' );

		$diffOp->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( 'diff' ) );

		$diff = new Diff( array( $diffOp ), true );

		$patcher->patch( array(), $diff );

		$patcher->throwErrors();
		$this->expectException( 'Diff\Patcher\PatcherException' );

		$patcher->patch( array(), $diff );
	}

}
