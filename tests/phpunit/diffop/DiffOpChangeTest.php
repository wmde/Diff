<?php

namespace Diff\Tests;

use Diff\DiffOpAdd;
use Diff\DiffOpChange;

/**
 * @covers Diff\DiffOpChange
 * @covers Diff\AtomicDiffOp
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
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpChangeTest extends DiffOpTest {

	/**
	 * @see DiffOpTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOpChange';
	}

	/**
	 * @see DiffOpTest::constructorProvider
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function constructorProvider() {
		return array(
			array( true, 'foo', 'bar' ),
			array( true, array( 9001 ), array( 4, 2 ) ),
			array( true, true, false ),
			array( true, true, true ),
			array( true, 42, 4.2 ),
			array( true, 42, 42 ),
			array( true, 'foo', array( 'foo' ) ),
			array( true, 'foo', null ),
			array( true, new DiffOpAdd( "ham" ), new DiffOpAdd( "spam" ) ),
			array( true, null, null ),
			array( false ),
		);
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetNewValue( DiffOpChange $diffOp, array $constructorArgs ) {
		$this->assertEquals( $constructorArgs[0], $diffOp->getOldValue() );
		$this->assertEquals( $constructorArgs[1], $diffOp->getNewValue() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore( DiffOpChange $diffOp ) {
		$array = $diffOp->toArray();
		$this->assertArrayHasKey( 'newvalue', $array );
		$this->assertArrayHasKey( 'oldvalue', $array );
	}

}
