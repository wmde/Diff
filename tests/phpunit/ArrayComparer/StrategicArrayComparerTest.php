<?php

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\StrategicArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\ArrayComparer\StrategicArrayComparer
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
class StrategicArrayComparerTest extends DiffTestCase {

	public function testCanConstruct() {
		new StrategicArrayComparer( $this->getMock( 'Diff\Comparer\ValueComparer' ) );
		$this->assertTrue( true );
	}

	public function testDiffArrays() {
		$valueComparer = $this->getMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->will( $this->returnCallback( function( $firstValue, $secondValue ) {
				return true;
			} ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$this->assertEquals(
			array(),
			$arrayComparer->diffArrays(
				array( 0, 2, 4 ),
				array( 1, 2, 9 )
			)
		);

		// TODO: implement
	}

}
