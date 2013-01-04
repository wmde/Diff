<?php

namespace Diff\Test;
use Diff\ListDiffer;
use Diff\DiffOpAdd;
use Diff\DiffOpRemove;
use Diff\Differ;

/**
 * Tests for the Diff\ListDiffer class.
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
 * @since 0.1
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group Differ
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDifferTest extends \MediaWikiTestCase {

	/**
	 * Returns those that both work for native and strict mode.
	 */
	protected function getCommonArgLists() {
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
			'There should be no difference between arrays with the same element' );


		$old = array( 42, 'ohi', 4.2, false );
		$new = array( 42, 'ohi', 4.2, false );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between arrays with the same elements' );


		$old = array( 42, 'ohi', 4.2, false );
		$new = array( false, 4.2, 'ohi', 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between arrays with the same elements even when not ordered the same' );


		$old = array();
		$new = array( 42 );
		$expected = array( new DiffOpAdd( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'An array with a single element should be an add operation different from an empty array' );


		$old = array( 42 );
		$new = array();
		$expected = array( new DiffOpRemove( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'An empty array should be a remove operation different from an array with one element' );


		$old = array( 1 );
		$new = array( 2 );
		$expected = array( new DiffOpRemove( 1 ), new DiffOpAdd( 2 ) );

		$argLists[] = array( $old, $new, $expected,
			'Two arrays with a single different element should differ by an add and a remove op' );


		$old = array( 9001, 42, 1, 0 );
		$new = array( 9001, 2, 0, 42 );
		$expected = array( new DiffOpRemove( 1 ), new DiffOpAdd( 2 ) );

		$argLists[] = array( $old, $new, $expected,
			'Two arrays with a single different element should differ by an add and a remove op even when they share identical elements' );

		return $argLists;
	}

	public function toDiffProvider() {
		$argLists = $this->getCommonArgLists();

		$old = array( 42, 42 );
		$new = array( 42 );
		$expected = array( new DiffOpRemove( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'[42, 42] to [42] should [rem(42)]' );


		$old = array( 42 );
		$new = array( 42, 42 );
		$expected = array( new DiffOpAdd( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'[42] to [42, 42] should [add(42)]' );


		$old = array( '42' );
		$new = array( 42 );
		$expected = array( new DiffOpRemove( '42' ), new DiffOpAdd( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'["42"] to [42] should [rem("42"), add(42)]' );


		$old = array( array( 1 ) );
		$new = array( array( 2 ) );
		$expected = array( new DiffOpRemove( array( 1 ) ), new DiffOpAdd( array( 2 ) ) );

		$argLists[] = array( $old, $new, $expected,
			'[[1]] to [[2]] should [rem([1]), add([2])]' );


		$old = array( array( 2 ) );
		$new = array( array( 2 ) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'[[2]] to [[2]] should result in an empty diff' );

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff( $old, $new, $expected, $message = '' ) {
		$this->doTestDiff( new ListDiffer(), $old, $new, $expected, $message );
	}

	public function toDiffNativeProvider() {
		$argLists = $this->getCommonArgLists();

		$old = array( '42' );
		$new = array( 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'["42"] to [42] should result in an empty diff' );


		$old = array( 42, 42 );
		$new = array( 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'[42, 42] to [42] should result in an empty diff' );


		$old = array( 42 );
		$new = array( 42, 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'[42] to [42, 42] should result in an empty diff' );

		return $argLists;
	}

	/**
	 * @dataProvider toDiffNativeProvider
	 */
	public function testDoNativeDiff( $old, $new, $expected, $message = '' ) {
		$this->doTestDiff( new ListDiffer( ListDiffer::MODE_NATIVE ), $old, $new, $expected, $message );
	}

	protected function doTestDiff( Differ $differ, $old, $new, $expected, $message ) {
		$actual = $differ->doDiff( $old, $new );

		$this->assertArrayEquals( $expected, $actual, false, false, $message );
	}

}