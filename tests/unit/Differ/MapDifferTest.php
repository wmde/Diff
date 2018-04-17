<?php

declare( strict_types = 1 );

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
 * @covers Diff\Differ\MapDiffer
 *
 * @group Diff
 * @group Differ
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapDifferTest extends DiffTestCase {

	public function toDiffProvider() {
		$argLists = array();

		$old = array();
		$new = array();
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between empty arrays' );

		$old = array( 42 );
		$new = array( 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between two arrays with the same element' );

		$old = array( 42, 10, 'ohi', false, null, array( '.', 4.2 ) );
		$new = array( 42, 10, 'ohi', false, null, array( '.', 4.2 ) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between two arrays with the same elements' );

		$old = array( 42, 42, 42 );
		$new = array( 42, 42, 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between two arrays with the same elements' );

		$old = array( 1, 2 );
		$new = array( 2, 1 );
		$expected = array( new DiffOpChange( 1, 2 ), new DiffOpChange( 2, 1 ) );

		$argLists[] = array( $old, $new, $expected,
			'Switching position should cause a diff' );

		$old = array( 0, 1, 2, 3 );
		$new = array( 0, 2, 1, 3 );
		$expected = array( 1 => new DiffOpChange( 1, 2 ), 2 => new DiffOpChange( 2, 1 ) );

		$argLists[] = array( $old, $new, $expected,
			'Switching position should cause a diff' );

		$old = array( 'a' => 0, 'b' => 1, 'c' => 0 );
		$new = array( 'a' => 42, 'b' => 1, 'c' => 42 );
		$expected = array( 'a' => new DiffOpChange( 0, 42 ), 'c' => new DiffOpChange( 0, 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'Doing the same change to two different elements should result in two identical change ops' );

		$old = array( 'a' => 0, 'b' => 1 );
		$new = array( 'a' => 0, 'c' => 1 );
		$expected = array( 'b' => new DiffOpRemove( 1 ), 'c' => new DiffOpAdd( 1 ) );

		$argLists[] = array( $old, $new, $expected,
			'Changing the key of an element should result in a remove and an add op' );

		$old = array( 'a' => 0, 'b' => 1 );
		$new = array( 'b' => 1, 'a' => 0 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'Changing the order of associative elements should have no effect.' );

		$old = array( 'a' => array( 'foo' ) );
		$new = array( 'a' => array( 'foo' ) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'Comparing equal substructures without recursion should return nothing.', false );

		$old = array( );
		$new = array( 'a' => array( 'foo', 'bar' ) );
		$expected = array( 'a' => new DiffOpAdd( array( 'foo', 'bar' ) ) );

		$argLists[] = array( $old, $new, $expected,
			'Adding a substructure should result in a single add operation when not in recursive mode.', false );

		$old = array( 'a' => array( 'b' => 42 ) );
		$new = array( 'a' => array( 'b' => 7201010 ) );
		$expected = array( 'a' => new Diff( array( 'b' => new DiffOpChange( 42, 7201010 ) ), true ) );

		$argLists[] = array( $old, $new, $expected,
			'Recursion should work for nested associative diffs', true );

		$old = array( 'a' => array( 'foo' ) );
		$new = array( 'a' => array( 'foo' ) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'Comparing equal sub-structures with recursion should return nothing.', true );

		$old = array( 'stuff' => array( 'a' => 0, 'b' => 1 ) );
		$new = array( 'stuff' => array( 'b' => 1, 'a' => 0 ) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'Changing the order of associative elements in a substructure should have no effect.', true );

		$old = array();
		$new = array( 'stuff' => array( 'b' => 1, 'a' => 0 ) );
		$expected = array( 'stuff' => new Diff( array( 'b' => new DiffOpAdd( 1 ), 'a' => new DiffOpAdd( 0 ) ) ) );

		$argLists[] = array( $old, $new, $expected,
			'Adding a substructure should be reported as adding *to* a substructure when in recursive mode.', true );

		$old = array( 'a' => array( 42, 9001 ), 1 );
		$new = array( 'a' => array( 42, 7201010 ), 1 );
		$expected = array( 'a' => new Diff( array( new DiffOpAdd( 7201010 ), new DiffOpRemove( 9001 ) ), false ) );

		$argLists[] = array( $old, $new, $expected,
			'Recursion should work for nested non-associative diffs', true );

		$old = array( array( 42 ), 1 );
		$new = array( array( 42, 42 ), 1 );
		$expected = array( new Diff( array( new DiffOpAdd( 42 ) ), false ) );

		$argLists[] = array( $old, $new, $expected,
			'Nested non-associative diffs should behave as the default ListDiffer', true );

		$old = array( array( 42 ), 1 );
		$new = array( array( 42, 42, 1 ), 1 );
		$expected = array( new Diff( array( new DiffOpAdd( 1 ) ), false ) );

		$argLists[] = array( $old, $new, $expected,
			'Setting a non-default Differ for non-associative diffs should work',
			true, new ListDiffer( new NativeArrayComparer() ) );

		$old = array( 'a' => array( 42 ), 1, array( 'a' => 'b', 5 ), 'bah' => array( 'foo' => 'bar' ) );
		$new = array( 'a' => array( 42 ), 1, array( 'a' => 'b', 5 ), 'bah' => array( 'foo' => 'baz' ) );
		$expected = array( 'bah' => new Diff( array( 'foo' => new DiffOpChange( 'bar', 'baz' ) ), true ) );

		$argLists[] = array(
			$old,
			$new,
			$expected,
			'Nested structures with no differences should not result '
				. 'in nested empty diffs (these empty diffs should be omitted)',
			true
		);

		$old = array( 'links' => array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array(),
			)
		) );
		$new = array( 'links' => array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array(),
			)
		) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'Comparing identical nested structures should not result in diff operations',
			true );

		$old = array( 'links' => array(
		) );
		$new = array( 'links' => array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array(),
			)
		) );
		$expected = array( 'links' => new Diff( array(
			'enwiki' => new Diff( array(
				'page' => new DiffOpAdd( 'Foo' )
			) )
		), true ) );

		$argLists[] = array( $old, $new, $expected,
			'Adding a sitelink with no badges',
			true );

		$old = array( 'links' => array(
		) );
		$new = array( 'links' => array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array( 'Bar', 'Baz' ),
			)
		) );
		$expected = array( 'links' => new Diff( array(
			'enwiki' => new Diff( array(
				'page' => new DiffOpAdd( 'Foo' ),
				'badges' => new Diff( array(
					new DiffOpAdd( 'Bar' ),
					new DiffOpAdd( 'Baz' ),
				), false )
			), true )
		), true ) );

		$argLists[] = array( $old, $new, $expected,
			'Adding a sitelink with badges',
			true );

		$old = array( 'links' => array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array(),
			)
		) );
		$new = array( 'links' => array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array( 'Bar', 'Baz' ),
			)
		) );
		$expected = array( 'links' => new Diff( array(
			'enwiki' => new Diff( array(
				'badges' => new Diff( array(
					new DiffOpAdd( 'Bar' ),
					new DiffOpAdd( 'Baz' ),
				), false )
			), true )
		), true ) );

		$argLists[] = array( $old, $new, $expected,
			'Adding bagdes to a sitelink',
			true );

		$old = array();
		$new = array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array( 'Bar', 'Baz' ),
			)
		);
		$expected = array(
			'enwiki' => new DiffOpAdd(
				array(
					'page' => 'Foo',
					'badges' => array( 'Bar', 'Baz' ),
				)
			)
		);

		$argLists[] = array( $old, $new, $expected,
			'Adding a sitelink with non-recursive mode',
			false );

		$old = array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array(),
			)
		);
		$new = array(
			'enwiki' => array(
				'page' => 'Foo',
				'badges' => array( 'Bar', 'Baz' ),
			)
		);
		$expected = array(
			'enwiki' => new DiffOpChange(
				array(
					'page' => 'Foo',
					'badges' => array(),
				),
				array(
					'page' => 'Foo',
					'badges' => array( 'Bar', 'Baz' ),
				)
			)
		);

		$argLists[] = array( $old, $new, $expected,
			'Adding badges to a sitelink with non-recursive mode',
			false );

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff( $old, $new, $expected, $message = '', $recursively = false, Differ $listDiffer = null ) {
		$differ = new MapDiffer( $recursively, $listDiffer );

		$actual = $differ->doDiff( $old, $new );

		$this->assertArrayEquals( $expected, $actual, false, true, $message );
	}

	public function testCallbackComparisonReturningFalse() {
		$differ = new MapDiffer( false, null, new StubValueComparer( false ) );

		$actual = $differ->doDiff( array( 1, '2', 3 ), array( 1, '2', 4, 'foo' ) );

		$expected = array(
			new DiffOpChange( 1, 1 ),
			new DiffOpChange( '2', '2' ),
			new DiffOpChange( 3, 4 ),
			new DiffOpAdd( 'foo' ),
		);

		$this->assertArrayEquals(
			$expected, $actual, false, true,
			'Identical elements should result in change ops when comparison callback always returns false'
		);
	}

	public function testCallbackComparisonReturningTrue() {
		$differ = new MapDiffer( false, null, new StubValueComparer( true ) );

		$actual = $differ->doDiff( array( 1, '2', 'baz' ), array( 1, 'foo', '2' ) );

		$expected = array();

		$this->assertArrayEquals(
			$expected, $actual, false, true,
			'No change ops should be created when the arrays have '
				. 'the same length and the comparison callback always returns true'
		);
	}

}
