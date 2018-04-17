<?php

declare( strict_types = 1 );

namespace Diff\Tests\DiffOp;

use Diff\DiffOp\DiffOpAdd;

/**
 * @covers Diff\DiffOp\DiffOpAdd
 * @covers Diff\DiffOp\AtomicDiffOp
 *
 * @group Diff
 * @group DiffOp
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpAddTest extends DiffOpTest {

	/**
	 * @see DiffOpTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOp\DiffOpAdd';
	}

	/**
	 * @see DiffOpTest::constructorProvider
	 *
	 * @since 0.1
	 */
	public function constructorProvider() {
		return array(
			array( true, 'foo' ),
			array( true, array() ),
			array( true, true ),
			array( true, 42 ),
			array( true, new DiffOpAdd( 'spam' ) ),
		);
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetNewValue( DiffOpAdd $diffOp, array $constructorArgs ) {
		$this->assertEquals( $constructorArgs[0], $diffOp->getNewValue() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore( DiffOpAdd $diffOp ) {
		$array = $diffOp->toArray();
		$this->assertArrayHasKey( 'newvalue', $array );
	}

}
