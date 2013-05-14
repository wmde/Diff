<?php

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\ArrayComparer\NativeArrayComparer
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
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NativeArrayComparerTest extends DiffTestCase {

	public function testCanConstruct() {
		new NativeArrayComparer();
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider diffInputProvider
	 */
	public function testDiffArraysReturnsTheNativeValue( array $arrayOne, array $arrayTwo ) {
		$differ = new NativeArrayComparer();

		$this->assertEquals(
			array_diff( $arrayOne, $arrayTwo ),
			$differ->diffArrays( $arrayOne, $arrayTwo )
		);
	}

	public function diffInputProvider() {
		$argLists = array();

		$argLists[] = array(
			array(),
			array(),
		);

		$argLists[] = array(
			array( 'foo', 1 ),
			array( 'foo', 1 ),
		);

		$argLists[] = array(
			array( 'bar', 2 ),
			array( 'foo', 1 ),
		);

		$argLists[] = array(
			array( 1, 'bar', 2, 1 ),
			array( 'foo', 1, 3 ),
		);

		$argLists[] = array(
			array( '', null, 2, false , 0 ),
			array( '0', true, 1, ' ', '' ),
		);

		return $argLists;
	}

}
