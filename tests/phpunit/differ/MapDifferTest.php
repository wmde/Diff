<?php

namespace Diff\Tests;

use Diff\DiffOpAdd;
use Diff\DiffOpChange;
use Diff\DiffOpRemove;
use Diff\MapDiffer;

/**
 * @covers Diff\MapDiffer
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.4
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group Differ
 *
 * @licence GNU GPL v2+
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


		$old = array( 'a' => array( 'b' => 42 ) );
		$new = array( 'a' => array( 'b' => 7201010 ) );
		$expected = array( 'a' => new \Diff\Diff( array( 'b' => new DiffOpChange( 42, 7201010 ) ), true ) );

		$argLists[] = array( $old, $new, $expected,
			'Recursion should work for nested associative diffs', true );


		$old = array( 'a' => array( 42, 9001 ), 1 );
		$new = array( 'a' => array( 42, 7201010 ), 1 );
		$expected = array( 'a' => new \Diff\Diff( array( new DiffOpAdd( 7201010 ), new DiffOpRemove( 9001 ) ), false ) );

		$argLists[] = array( $old, $new, $expected,
			'Recursion should work for nested non-associative diffs', true );


		$old = array( array( 42 ), 1 );
		$new = array( array( 42, 42 ), 1 );
		$expected = array( new \Diff\Diff( array( new DiffOpAdd( 42 ) ), false ) );

		$argLists[] = array( $old, $new, $expected,
			'Nested non-associative diffs should behave as the default ListDiffer', true );


		$old = array( array( 42 ), 1 );
		$new = array( array( 42, 42, 1 ), 1 );
		$expected = array( new \Diff\Diff( array( new DiffOpAdd( 1 ) ), false ) );

		$argLists[] = array( $old, $new, $expected,
			'Setting a non-default Differ for non-associative diffs should work',
			true, new \Diff\ListDiffer( \Diff\ListDiffer::MODE_NATIVE ) );


		$old = array( 'a' => array( 42 ), 1, array( 'a' => 'b', 5 ), 'bah' => array( 'foo' => 'bar' ) );
		$new = array( 'a' => array( 42 ), 1, array( 'a' => 'b', 5 ), 'bah' => array( 'foo' => 'baz' ) );
		$expected = array( 'bah' => new \Diff\Diff( array( 'foo' => new DiffOpChange( 'bar', 'baz' ) ), true ) );

		$argLists[] = array( $old, $new, $expected,
			'Nested structures with no differences should not result in nested empty diffs (these empty diffs should be omitted)', true );

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff( $old, $new, $expected, $message = '', $recursively = false, \Diff\Differ $listDiffer = null ) {
		$differ = new MapDiffer( $recursively, $listDiffer );

		$actual = $differ->doDiff( $old, $new );

		$this->assertArrayEquals( $expected, $actual, false, true, $message );
	}

	public function testCallbackComparisonReturningFalse() {
		$differ = new MapDiffer();

		$differ->setComparisonCallback( function( $foo, $bar ) {
			return false;
		} );

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
		$differ = new MapDiffer();

		$differ->setComparisonCallback( function( $foo, $bar ) {
			return true;
		} );

		$actual = $differ->doDiff( array( 1, '2', 'baz' ), array( 1, 'foo', '2' ) );

		$expected = array();

		$this->assertArrayEquals(
			$expected, $actual, false, true,
			'No change ops should be created when the arrays have the same length and the comparison callback always returns true'
		);
	}

	public function testCallbackComparisonReturningNyanCat() {
		$differ = new MapDiffer();

		$differ->setComparisonCallback( function( $foo, $bar ) {
			return '~=[,,_,,]:3';
		} );

		$this->setExpectedException( 'Exception' );

		$differ->doDiff( array( 1, '2', 'baz' ), array( 1, 'foo', '2' ) );
	}

}
