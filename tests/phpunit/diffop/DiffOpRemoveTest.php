<?php

namespace Diff\Tests;

use Diff\DiffOpAdd;
use Diff\DiffOpRemove;

/**
 * @covers Diff\DiffOpRemove
 * @covers Diff\AtomicDiffOp
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpRemoveTest extends DiffOpTest {

	/**
	 * @see DiffOpTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOpRemove';
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
	public function testGetNewValue( DiffOpRemove $diffOp, array $constructorArgs ) {
		$this->assertEquals( $constructorArgs[0], $diffOp->getOldValue() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayMore( DiffOpRemove $diffOp ) {
		$array = $diffOp->toArray();
		$this->assertArrayHasKey( 'oldvalue', $array );
	}

}
