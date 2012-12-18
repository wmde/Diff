<?php

namespace Diff\Test;
use Diff\MapDiffer;
use Diff\DiffOpChange;
use Diff\DiffOpRemove;
use Diff\DiffOpAdd;

/**
 * Tests for the Diff\MapDiffer class.
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
class MapDifferTest extends \MediaWikiTestCase {

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

		// TODO: test recursion
		// TODO: test with alternate ListDiffer

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff( $old, $new, $expected, $message = '' ) {
		$differ = new MapDiffer();

		$actual = $differ->doDiff( $old, $new );

		$this->assertArrayEquals( $expected, $actual, false, true, $message );
	}

}
