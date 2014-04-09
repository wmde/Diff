<?php

namespace Diff\Tests;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Diff\DiffOpFactory;

/**
 * @covers Diff\DiffOpFactory
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
		$diffOps[] = new DiffOpAdd( new DiffOpChange( 'spam', 'moar spam' ) );

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
		$unserializationFunction = function( $array ) {
			if ( is_array( $array ) && isset( $array['type'] ) && $array['type'] === 'Change' ) {
				return new DiffOpChange( $array['teh_old'], $array['teh_new'] );
			}

			return $array;
		};

		$factory = new DiffOpFactory( $unserializationFunction );

		$serializationFunction = function( $obj ) {
			if ( $obj instanceof DiffOpChange ) {
				return array(
					'type' => 'Change',
					'teh_old' => $obj->getOldValue(),
					'teh_new' => $obj->getNewValue(),
				);
			}

			return $obj;
		};

		// try with conversion callback
		$array = $diffOp->toArray( $serializationFunction );

		$newInstance = $factory->newFromArray( $array );

		// If an equality method is implemented in DiffOp, it should be used here
		$this->assertEquals( $diffOp, $newInstance );
		$this->assertEquals( $diffOp->getType(), $newInstance->getType() );
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
