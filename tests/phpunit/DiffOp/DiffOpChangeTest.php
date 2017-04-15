<?php

declare( strict_types = 1 );

namespace Diff\Tests\DiffOp;

use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;

/**
 * @covers Diff\DiffOp\DiffOpChange
 * @covers Diff\DiffOp\AtomicDiffOp
 *
 * @group Diff
 * @group DiffOp
 *
 * @license GPL-2.0+
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
		return '\Diff\DiffOp\DiffOpChange';
	}

	/**
	 * @see DiffOpTest::constructorProvider
	 *
	 * @since 0.1
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
			array( true, new DiffOpAdd( 'ham' ), new DiffOpAdd( 'spam' ) ),
			array( true, null, null ),
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
