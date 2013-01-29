<?php

namespace Diff\Test;
use \Diff\DiffOp as DiffOp;

/**
 * Base test class for the Diff\DiffOp deriving classes.
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
 * @author Daniel Kinzler
 */
abstract class DiffOpTest extends \AbstractTestCase {

	/**
	 * @dataProvider instanceProvider
	 */
	public function testIsAtomic( DiffOp $diffOp ) {
		$this->assertInternalType( 'boolean', $diffOp->isAtomic() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetType( DiffOp $diffOp ) {
		$this->assertInternalType( 'string', $diffOp->getType() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSerialization( DiffOp $diffOp ) {
		$serialization = serialize( $diffOp );
		$unserialization = unserialize( $serialization );
		$this->assertEquals( $diffOp, $unserialization );
		$this->assertEquals( serialize( $diffOp ), serialize( $unserialization ) );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testCount( DiffOp $diffOp ) {
		if ( $diffOp->isAtomic() ) {
			$this->assertEquals( 1, count( $diffOp ) );
		}
		else {
			$count = 0;

			foreach ( $diffOp as $childOp ) {
				$count += $childOp->count();
			}

			$this->assertEquals( $count, count( $diffOp ) );
		}
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArray( DiffOp $diffOp ) {
		$array = $diffOp->toArray();

		$this->assertInternalType( 'array', $array );
		$this->assertArrayHasKey( 'type', $array );
		$this->assertInternalType( 'string', $array['type'] );
		$this->assertEquals( $diffOp->getType(), $array['type'] );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayWithConversion( DiffOp $diffOp ) {
		$array = $diffOp->toArray( 'Diff\Test\DiffOpTestDummy::arrayalize' );

		$this->assertNoObjectsRecursive( $array );
	}

	/**
	 * Asserts that $data is not an object, and contains no objects.
	 * This is useful for testing if a conversion from an object to an array
	 * structure is complete.
	 *
	 * @param mixed $data
	 * @param int   $depth max recursion depth (optional)
	 */
	protected function assertNoObjectsRecursive( $data, $depth = PHP_INT_MAX ) {
		if ( is_object( $data ) ) {
			$this->fail( "Found object: instance of " . get_class( $data ) );
		}

		if ( $depth > 0 ) {
			$depth -= 1;

			if ( is_array( $data ) ) {
				foreach ( $data as $value ) {
					$this->assertNoObjectsRecursive( $value, $depth );
				}
			}
		}

		$this->assertTrue( true ); // just a dummy, to supress warnings when there's nothing to check.
	}
}