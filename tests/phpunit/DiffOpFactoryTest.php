<?php

namespace Diff\Tests;

use Diff\Diff;
use Diff\DiffOpRemove;
use Diff\DiffOpAdd;
use Diff\DiffOpChange;
use Diff\DiffOp;
use Diff\DiffOpFactory;

/**
 * Tests for the Diff\DiffOpFactory class.
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
 * @group DiffOpFactory
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
class DiffOpFactoryTest extends DiffTestCase {

	public function diffOpProvider() {
		$diffOps = array();

		$diffOps[] = new DiffOpAdd( 42 );
		$diffOps['foo bar'] = new DiffOpAdd( '42' );
		$diffOps[9001] = new DiffOpAdd( 4.2 );
		$diffOps['42'] = new DiffOpAdd( array( 42, array( 9001 ) ) );
		$diffOps[] = new DiffOpRemove( 42 );
		$diffOps[] = new DiffOpAdd( new DiffOpTestDummy( "spam" ) );

		$atomicDiffOps = $diffOps;

		foreach ( array( true, false, null ) as $isAssoc ) {
			$diffOps[] = new Diff( $atomicDiffOps, $isAssoc );
		}

		$diffOps[] = new DiffOpChange( 42, '9001' );

		$diffOps[] = new Diff( $diffOps );

		return $this->arrayWrap( $diffOps );
	}

	/**
	 * @dataProvider diffOpProvider
	 *
	 * @param DiffOp $diffOp
	 */
	public function testNewFromArray( DiffOp $diffOp ) {
		$factory = new DiffOpFactory();

		// try without conversion callback
		$array = $diffOp->toArray();
		$newInstance = $factory->newFromArray( $array );

		// If an equality method is implemented in DiffOp, it should be used here
		$this->assertEquals( $diffOp, $newInstance );
		$this->assertEquals( $diffOp->getType(), $newInstance->getType() );
	}

	/**
	 * @dataProvider diffOpProvider
	 *
	 * @param DiffOp $diffOp
	 */
	public function testNewFromArrayWithConversion( DiffOp $diffOp ) {
		$factory = new DiffOpFactory( 'Diff\Tests\DiffOpTestDummy::objectify' );

		// try with conversion callback
		$array = $diffOp->toArray( 'Diff\Tests\DiffOpTestDummy::arrayalize' );
		$newInstance = $factory->newFromArray( $array );

		// If an equality method is implemented in DiffOp, it should be used here
		$this->assertEquals( $diffOp, $newInstance );
		$this->assertEquals( $diffOp->getType(), $newInstance->getType() );
	}

	public static function dummyToArray( $obj ) {
		if ( $obj instanceof DiffOpTestDummy ) {
			return array(
				'type' => 'Dummy',
				'text' => $obj->text,
			);
		}

		return $obj;
	}

	public static function arrayToDummy( $array ) {
		if ( is_array( $array ) && isset( $array['type'] ) && $array['type'] === 'Dummy' ) {
			return new DiffOpTestDummy( $array['text'] );
		}

		return $array;
	}

	public function invalidArrayFromArrayProvider() {
		$arrays = array();

		$arrays[] = array();

		$arrays[] = array( '~=[,,_,,]:3' );

		$arrays[] = array( '~=[,,_,,]:3' => '~=[,,_,,]:3' );

		$arrays[] = array( 'type' => '~=[,,_,,]:3' );

		$arrays[] = array( 'type' => 'add', 'oldvalue' => 'foo' );

		$arrays[] = array( 'type' => 'remove', 'newvalue' => 'foo' );

		$arrays[] = array( 'type' => 'change', 'newvalue' => 'foo' );

		$arrays[] = array( 'diff' => 'remove', 'newvalue' => 'foo' );

		$arrays[] = array( 'diff' => 'remove', 'operations' => array() );

		$arrays[] = array( 'diff' => 'remove', 'isassoc' => true );

		return $this->arrayWrap( $arrays );
	}

	/**
	 * @dataProvider invalidArrayFromArrayProvider
	 *
	 * @param array $array
	 */
	public function testNewFromArrayInvalid( array $array ) {
		$this->setExpectedException( 'InvalidArgumentException' );

		$factory = new DiffOpFactory();
		$factory->newFromArray( $array );
	}

}
