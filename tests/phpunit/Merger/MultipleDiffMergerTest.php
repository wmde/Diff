<?php

namespace Diff\Tests\Merger;

use Diff\Diff;
use Diff\DiffOpAdd;
use Diff\DiffOpRemove;
use Diff\Merger\MultipleDiffMerger;

/**
 * @covers Diff\Merger\MultipleDiffMerger
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
class MultipleDiffMergerTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$this->newMergerWithMocks();
		$this->assertTrue( true );
	}

	protected function newMergerWithMocks() {
		$mergingStrategy = $this->getMock( 'Diff\Merger\DiffMerger' );

		$mergingStrategy->expects( $this->any() )
			->method( 'merge' )
			->will( $this->returnValue( new Diff() ) );

		return new MultipleDiffMerger(
			$mergingStrategy,
			$mergingStrategy
		);
	}

	public function testMergeNone() {
		$merger = $this->newMergerWithMocks();

		$mergedDiff = $merger->merge();

		$this->assertInstanceOf( 'Diff\Diff', $mergedDiff );
		$this->assertCount( 0, $mergedDiff );
	}

	public function testMergeOneEmpty() {
		$merger = $this->newMergerWithMocks();

		$inputDiff = new Diff();

		$mergedDiff = $merger->merge( $inputDiff );

		$this->assertEquals( $inputDiff, $mergedDiff );

		$this->assertFalse(
			$inputDiff === $mergedDiff,
			'When providing only one diff, the result should still be a new diff instance'
		);
	}

	/**
	 * @dataProvider invalidInputProvider
	 */
	public function testOnlyAcceptsDiffs() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$merger = $this->newMergerWithMocks();

		$merger->merge( func_get_args() );
	}

	public function invalidInputProvider() {
		$argLists = array();

		$argLists[] = array( 1 );

		$argLists[] = array( 'foo' );

		$argLists[] = array( array() );

		$argLists[] = array( new DiffOpRemove( 42 ) );

		$argLists[] = array( new Diff(), 42 );

		return $argLists;
	}

	/**
	 * @dataProvider mergeDiffsProvider
	 */
	public function testMerge() {
		$diffs = func_get_args();

		$mergingStrategy = $this->getMock( 'Diff\Merger\DiffMerger' );

		$mergingStrategy->expects( $this->exactly( count( $diffs ) ) )
			->method( 'merge' )
			->will( $this->returnValue( new Diff() ) );;

		$merger = new MultipleDiffMerger(
			$mergingStrategy,
			$mergingStrategy
		);

		call_user_func_array( array( $merger, 'merge' ), $diffs );
	}

	public function mergeDiffsProvider() {
		$argLists[] = array(
			new Diff( array( new DiffOpAdd( 42 ) ) ),
			new Diff( array( new DiffOpAdd( 9001 ) ) ),
		);

		$argLists[] = array(
			new Diff( array( new DiffOpAdd( 42 ) ) ),
			new Diff( array( new DiffOpAdd( 9001 ) ) ),
			new Diff( array( new DiffOpAdd( 1 ), new DiffOpAdd( 2 ) ) ),
		);

		$argLists[] = array(
			new Diff( array( new DiffOpAdd( 42 ) ) ),
			new Diff( array( new DiffOpAdd( 9001 ) ) ),
			new Diff( array( new DiffOpAdd( 1 ), new DiffOpAdd( 2 ) ) ),
			new Diff( array( new DiffOpAdd( 42 ) ) ),
			new Diff( array( new DiffOpAdd( 9001 ) ) ),
			new Diff( array( new DiffOpAdd( 1 ), new DiffOpAdd( 2 ) ) ),
		);

		return $argLists;
	}

}
