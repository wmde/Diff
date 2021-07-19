<?php

declare( strict_types = 1 );

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\Tests\AbstractDiffTestCase;

/**
 * @covers \Diff\ArrayComparer\NativeArrayComparer
 *
 * @group Diff
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NativeArrayComparerTest extends AbstractDiffTestCase {

	public function testCanConstruct() {
		new NativeArrayComparer();
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider diffInputProvider
	 */
	public function testDiffArraysReturnsTheNativeValue( array $arrayOne, array $arrayTwo ) {
		$differ = new NativeArrayComparer();

		$this->assertEquals(
			array_diff( $arrayOne, $arrayTwo ),
			$differ->diffArrays( $arrayOne, $arrayTwo )
		);
	}

	public function diffInputProvider() {
		$argLists = array();

		$argLists[] = array(
			array(),
			array(),
		);

		$argLists[] = array(
			array( 'foo', 1 ),
			array( 'foo', 1 ),
		);

		$argLists[] = array(
			array( 'bar', 2 ),
			array( 'foo', 1 ),
		);

		$argLists[] = array(
			array( 1, 'bar', 2, 1 ),
			array( 'foo', 1, 3 ),
		);

		$argLists[] = array(
			array( '', null, 2, false , 0 ),
			array( '0', true, 1, ' ', '' ),
		);

		return $argLists;
	}

}
