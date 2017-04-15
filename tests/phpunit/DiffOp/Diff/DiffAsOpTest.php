<?php

declare( strict_types = 1 );

namespace Diff\Tests\DiffOp\Diff;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffOp\DiffOpTest;

/**
 * @covers Diff\DiffOp\Diff\Diff
 *
 * @group Diff
 * @group DiffOp
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffAsOpTest extends DiffOpTest {

	/**
	 * @see DiffOpTest::getClass
	 *
	 * @since 0.5
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOp\Diff\Diff';
	}

	/**
	 * @see DiffOpTest::constructorProvider
	 *
	 * @since 0.5
	 */
	public function constructorProvider() {
		$argLists = array(
			array( true, array() ),
			array( true, array( new DiffOpAdd( 42 ) ) ),
			array( true, array( new DiffOpRemove( new DiffOpRemove( 'spam' ) ) ) ),
			array( true, array( new Diff( array( new DiffOpRemove( new DiffOpRemove( 'spam' ) ) ) ) ) ),
			array( true, array( new DiffOpAdd( 42 ), new DiffOpAdd( 42 ) ) ),
			array( true, array( 'a' => new DiffOpAdd( 42 ), 'b' => new DiffOpAdd( 42 ) ) ),
			array( true, array( new DiffOpAdd( 42 ), 'foo bar baz' => new DiffOpAdd( 42 ) ) ),
			array( true, array( 42 => new DiffOpRemove( 42 ), '9001' => new DiffOpAdd( 42 ) ) ),
			array( true, array( 42 => new DiffOpRemove( new \stdClass() ), '9001' => new DiffOpAdd( new \stdClass() ) ) ),
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

		$this->assertArrayHasKey( 'isassoc', $array );

		$this->assertTrue(
			is_bool( $array['isassoc'] ) || $array['isassoc'] === null,
			'The isassoc element needs to be a boolean or null'
		);
	}

}
