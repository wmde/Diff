<?php

declare( strict_types = 1 );

namespace Diff\Tests\DiffOp;

use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;

/**
 * @covers \Diff\DiffOp\DiffOpRemove
 * @covers \Diff\DiffOp\AbstractAtomicDiffOp
 *
 * @group Diff
 * @group DiffOp
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpRemoveTest extends AbstractDiffOpTest {

	/**
	 * @see AbstractDiffOpTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOp\DiffOpRemove';
	}

	/**
	 * @see AbstractDiffOpTest::constructorProvider
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
