<?php

namespace Diff\Test;
use Diff\Diff;

/**
 * Tests for the Diff\Diff class.
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
 * @since 0.5
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
class DiffAsOpTest extends DiffOpTest {

	/**
	 * @see AbstractTestCase::getClass
	 *
	 * @since 0.5
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\Diff';
	}

	/**
	 * @see AbstractTestCase::constructorProvider
	 *
	 * @since 0.5
	 *
	 * @return array
	 */
	public function constructorProvider() {
		$argLists = array(
			array( true, array() ),
			array( true, array( new \Diff\DiffOpAdd( 42 ) ) ),
			array( true, array( new \Diff\DiffOpRemove( new \Diff\Test\DiffOpTestDummy( "spam" ) ) ) ),
			array( true, array( new \Diff\Diff( array( new \Diff\DiffOpRemove( new \Diff\Test\DiffOpTestDummy( "spam" ) ) ) ) ) ),
			array( true, array( new \Diff\DiffOpAdd( 42 ), new \Diff\DiffOpAdd( 42 ) ) ),
			array( true, array( 'a' => new \Diff\DiffOpAdd( 42 ), 'b' => new \Diff\DiffOpAdd( 42 ) ) ),
			array( true, array( new \Diff\DiffOpAdd( 42 ), 'foo bar baz' => new \Diff\DiffOpAdd( 42 ) ) ),
			array( true, array( 42 => new \Diff\DiffOpRemove( 42 ), '9001' => new \Diff\DiffOpAdd( 42 ) ) ),
		);

		$allArgLists = $argLists;

		foreach ( $argLists as $argList ) {
			foreach ( array( true, false, null ) as $isAssoc ) {
				$argList[] = $isAssoc;
				$allArgLists[] = $argList;
			}
		}

		return $allArgLists;
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore( Diff $diffOp ) {
		$array = $diffOp->toArray();

		$this->assertArrayHasKey( 'operations', $array );
		$this->assertInternalType( 'array', $array['operations'] );

		// $array['operations'] must not directly contain objects
		$this->assertNoObjectsRecursive( $array['operations'], 1 );

		$this->assertArrayHasKey( 'isassoc', $array );
		$this->assertTypeOrValue( 'boolean', $array['isassoc'], null );
	}

}
