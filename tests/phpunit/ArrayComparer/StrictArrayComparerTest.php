<?php

declare( strict_types = 1 );

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\StrictArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\ArrayComparer\StrictArrayComparer
 *
 * @group Diff
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictArrayComparerTest extends DiffTestCase {

	public function testCanConstruct() {
		new StrictArrayComparer();
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider diffInputProvider
	 */
	public function testDiffReturnsExpectedValue( array $arrayOne, array $arrayTwo, array $expected, $message = '' ) {
		$differ = new StrictArrayComparer();

		$this->assertEquals(
			$expected,
			$differ->diffArrays( $arrayOne, $arrayTwo ),
			$message
		);
	}

	public function diffInputProvider() {
		$argLists = array();

		$argLists[] = array(
			array(),
			array(),
			array(),
			'The diff between empty arrays should be empty'
		);

		$argLists[] = array(
			array( 1 ),
			array( 1 ),
			array(),
			'The diff between identical arrays should be empty'
		);

		$argLists[] = array(
			array( 1, 2 , 1 ),
			array( 1, 1, 2 ),
			array(),
			'The diff between arrays with the same values but different orders should be empty'
		);

		$argLists[] = array(
			array( 1, 1 ),
			array( 1 ),
			array( 1 ),
			'The diff between an array with an element twice and an array that has it once should contain the element once'
		);

		$argLists[] = array(
			array( '0' ),
			array( 0 ),
			array( '0' ),
			'Comparison should be strict'
		);

		$argLists[] = array(
			array( false ),
			array( null ),
			array( false ),
			'Comparison should be strict'
		);

		$argLists[] = array(
			array( array( 1 ) ),
			array( array( 2 ) ),
			array( array( 1 ) ),
			'Arrays are compared properly'
		);

		$argLists[] = array(
			array( array( 1 ) ),
			array( array( 1 ) ),
			array(),
			'Arrays are compared properly'
		);

		$argLists[] = array(
			array( new \stdClass() ),
			array( new \stdClass() ),
			array(),
			'Objects are compared based on value, not identity'
		);

		$argLists[] = array(
			array( (object)array( 'foo' => 'bar' ) ),
			array( (object)array( 'foo' => 'baz' ) ),
			array( (object)array( 'foo' => 'bar' ) ),
			'Differences between objects are detected'
		);

		return $argLists;
	}

}
