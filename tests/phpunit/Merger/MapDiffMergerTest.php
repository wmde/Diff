<?php

namespace Diff\Tests\Merger;

use Diff\Diff;
use Diff\DiffOpAdd;
use Diff\DiffOpRemove;
use Diff\Merger\MapDiffMerger;

/**
 * @covers Diff\Merger\MapDiffMerger
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
class MapDiffMergerTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct() {
		new MapDiffMerger();
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider mergeProvider
	 */
	public function testMerge( Diff $firstInput, Diff $secondInput, Diff $expectedDiff, $message ) {
		$merger = new MapDiffMerger();

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

		return $argLists;
	}

}
