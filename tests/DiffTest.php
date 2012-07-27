<?php

namespace Diff\Test;
use Diff\Diff as Diff;
use Diff\DiffOpAdd as DiffOpAdd;
use Diff\DiffOpRemove as DiffOpRemove;
use Diff\DiffOpChange as DiffOpChange;

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
 * @since 0.1
 *
 * @ingroup Diff
 * @ingroup Test
 * @group Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffTest extends GenericArrayObjectTest {

	public function elementInstancesProvider() {
		return array(
			array( array(
			) ),
			array( array(
				new DiffOpAdd( 'ohi' )
			) ),
			array( array(
				new DiffOpRemove( 'ohi' )
			) ),
			array( array(
				new DiffOpAdd( 'ohi' ),
				new DiffOpRemove( 'there' )
			) ),
			array( array(
			) ),
			array( array(
				new DiffOpAdd( 'ohi' ),
				new DiffOpRemove( 'there' ),
				new DiffOpChange( 'ohi', 'there' )
			) ),
			array( array(
				'1' => new DiffOpAdd( 'ohi' ),
				'33' => new DiffOpRemove( 'there' ),
				'7' => new DiffOpChange( 'ohi', 'there' )
			) ),
		);
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testStuff( array $operations ) {
		$diff = new Diff( $operations );

		$this->assertInstanceOf( '\Diff\IDiff', $diff );
		$this->assertInstanceOf( '\ArrayObject', $diff );

		$types = array();

		foreach ( $diff as $operation ) {
			$this->assertInstanceOf( '\Diff\IDiffOp', $operation );
			if ( !in_array( $operation->getType(), $types ) ) {
				$types[] = $operation->getType();
			}
		}

		$count = 0;

		foreach ( $types as $type ) {
			$count += count( $diff->getTypeOperations( $type ) );
		}

		$this->assertEquals( $count, $diff->count() );
	}

	public function instanceProvider() {
		$instances = array();

		foreach ( $this->stuffProvider() as $args ) {
			$diffOps = $args[0];
			$instances[] = new Diff( $diffOps );
		}

		return $instances;
	}

	public function getInstanceClass() {
		return '\Diff\Diff';
	}

}
	
