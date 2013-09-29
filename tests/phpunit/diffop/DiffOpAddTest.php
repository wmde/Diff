<?php

namespace Diff\Tests;

use Diff\DiffOpAdd;

/**
 * @covers Diff\DiffOpAdd
 * @covers Diff\AtomicDiffOp
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
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
		return '\Diff\DiffOpAdd';
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
			array( true, 'foo' ),
			array( true, array() ),
			array( true, true ),
			array( true, 42 ),
			array( true, new DiffOpAdd( "spam" ) ),
			array( false ),
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
