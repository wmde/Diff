<?php

namespace Diff\Tests\Comparer;

use Diff\Comparer\CallbackComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Comparer\CallbackComparer
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
class CallbackComparerTest extends DiffTestCase {

	protected function newComparerInstance() {
		return new CallbackComparer( function( $firstValue, $secondValue ) {
			return $firstValue === 1 || $firstValue === $secondValue;
		} );
	}

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual( $firstValue, $secondValue ) {
		$comparer = $this->newComparerInstance();

		$this->assertTrue( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function equalProvider() {
		return array(
			array( 1, 0 ),
			array( 1, 1 ),
			array( 1, 2 ),
			array( 2, 2 ),
		);
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual( $firstValue, $secondValue ) {
		$comparer = $this->newComparerInstance();

		$this->assertFalse( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function unequalProvider() {
		return array(
			array( 0, 1 ),
			array( 0, 2 ),
			array( 0, '0' ),
		);
	}

}
