<?php

namespace Diff\Tests\Comparer;

use Diff\Comparer\StrictComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Comparer\StrictComparer
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
 * @since 0.6
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group Comparer
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictComparerTest extends DiffTestCase {

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual( $firstValue, $secondValue ) {
		$comparer = new StrictComparer();

		$this->assertTrue( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function equalProvider() {
		return array(
			array( 1, 1 ),
			array( '', '' ),
			array( '1', '1' ),
			array( 'foo bar ', 'foo bar ' ),
			array( 4.2, 4.2 ),
			array( null, null ),
			array( false, false ),
			array( true, true ),
			array( array(), array() ),
			array( array( 1 ), array( 1 ) ),
			array( array( 1, 2, 'a' ), array( 1, 2, 'a' ) ),
			array( array( 'a' => 1, 'b' => 2, null ), array( 'a' => 1, 'b' => 2, null ) ),
		);
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual( $firstValue, $secondValue ) {
		$comparer = new StrictComparer();

		$this->assertFalse( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function unequalProvider() {
		return array(
			array( 1, 2 ),
			array( '', '0' ),
			array( '', ' ' ),
			array( '', 0 ),
			array( '', false ),
			array( null, false ),
			array( null, 0 ),
			array( '1', '01' ),
			array( 'foo bar', 'foo bar ' ),
			array( 4, 4.0 ),
			array( 4.2, 4.3 ),
			array( false, true ),
			array( true, '1' ),
			array( array(), array( 1 ) ),
			array( array( 1 ), array( 2 ) ),
			array( array( 1, 2, 'b' ), array( 1, 2, 'c' ) ),
			array( array( 'a' => 1, 'b' => 2 ), array( 'a' => 1, 'b' => 2, null ) ),
			array( new \stdClass(), new \stdClass() ),
			array( (object)array( 'a' => 1, 'b' => 2, null ), (object)array( 'a' => 1, 'b' => 3, null ) ),
		);
	}

}
