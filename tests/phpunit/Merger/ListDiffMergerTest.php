<?php

namespace Diff\Tests\Merger;

use Diff\Diff;
use Diff\DiffOpAdd;
use Diff\DiffOpRemove;
use Diff\Merger\ListDiffMerger;

/**
 * @covers Diff\Merger\ListDiffMerger
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
 * @since 0.7
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group DiffMerger
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffMergerTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct() {
		new ListDiffMerger();
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider mergeProvider
	 */
	public function testMerge( Diff $firstInput, Diff $secondInput, Diff $expectedDiff, $message ) {
		$merger = new ListDiffMerger();

		$mergedDiff = $merger->merge( $firstInput, $secondInput );

		$this->assertEquals( $expectedDiff, $mergedDiff,$message );
	}

	public function mergeProvider() {
		$argLists = array();

		$firstInput = new Diff();
		$secondInput = new Diff();
		$expected = new Diff();

		$argLists[] = array(
			$firstInput, $secondInput, $expected,
			'Two empty diffs merged together result into one empty diff'
		);


		$firstInput = new Diff();
		$secondInput = new Diff( array( new DiffOpAdd( 42 ), new DiffOpRemove( 9001 ) ) );
		$expected = $secondInput;

		$argLists[] = array(
			$firstInput, $secondInput, $expected,
			'An empty diff plus a non-empty diff results in the non-empty diff'
		);


		$firstInput = new Diff( array( new DiffOpAdd( 42 ), new DiffOpRemove( 9001 ) ) );
		$secondInput = new Diff();
		$expected = $firstInput;

		$argLists[] = array(
			$firstInput, $secondInput, $expected,
			'A non-empty diff plus an empty diff results in the non-empty diff'
		);


		$firstInput = new Diff( array( new DiffOpAdd( 42 ) ) );
		$secondInput = new Diff( array( new DiffOpAdd( 9001 ) ) );
		$expected = new Diff( array( new DiffOpAdd( 42 ), new DiffOpAdd( 9001 ) ) );

		$argLists[] = array(
			$firstInput, $secondInput, $expected,
			'Merging diffs with a single add operation together should result in'
				. 'a single non-associative diff with two add operations'
		);


		$firstInput = new Diff( array( new DiffOpAdd( 42 ), new DiffOpAdd( 42 ) ) );
		$secondInput = new Diff( array( new DiffOpAdd( 42 ), new DiffOpAdd( 42 ) ) );
		$expected = new Diff( array(
			new DiffOpAdd( 42 ),
			new DiffOpAdd( 42 ),
			new DiffOpAdd( 42 ),
			new DiffOpAdd( 42 ),
		) );

		$argLists[] = array(
			$firstInput, $secondInput, $expected,
			'Merging diffs with the same add operations should result in the '
				. 'add operations being there multiple times'
		);


		$firstInput = new Diff( array( new DiffOpRemove( 42 ), new DiffOpRemove( 42 ) ) );
		$secondInput = new Diff( array( new DiffOpRemove( 42 ), new DiffOpRemove( 42 ) ) );
		$expected = new Diff( array(
			new DiffOpRemove( 42 ),
			new DiffOpRemove( 42 ),
			new DiffOpRemove( 42 ),
			new DiffOpRemove( 42 ),
		) );

		$argLists[] = array(
			$firstInput, $secondInput, $expected,
			'Merging diffs with the same remove operations should result in the '
				. 'remove operations being there multiple times'
		);


//		$firstInput = new Diff( array( new DiffOpAdd( 42 ) ) );
//		$secondInput = new Diff( array( new DiffOpRemove( 42 ) ) );
//		$expected = new Diff( array() );
//
//		$argLists[] = array(
//			$firstInput, $secondInput, $expected,
//			'A diff with add(x) merged with rem(x) should result in an empty diff'
//		);

		return $argLists;
	}

	public function testInputIsNotModified() {
		$merger = new ListDiffMerger();

		$firstInput = new Diff( array( new DiffOpAdd( 23 ) ) );
		$secondInput = new Diff( array( new DiffOpAdd( 42 ) ) );

		$originalFirstInput = clone $firstInput;
		$originalSecondInput = clone $secondInput;

		$merger->merge( $firstInput, $secondInput );

		$this->assertEquals( $originalFirstInput, $firstInput );
		$this->assertEquals( $originalSecondInput, $secondInput );
	}

}
