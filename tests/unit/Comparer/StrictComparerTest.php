<?php

declare( strict_types = 1 );

namespace Diff\Tests\Comparer;

use Diff\Comparer\StrictComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Comparer\StrictComparer
 *
 * @group Diff
 * @group Comparer
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictComparerTest extends DiffTestCase {

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual( $firstValue, $secondValue ) {
		$comparer = new StrictComparer();

		$this->assertTrue( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function equalProvider() {
		return array(
			array( 1, 1 ),
			array( '', '' ),
			array( '1', '1' ),
			array( 'foo bar ', 'foo bar ' ),
			array( 4.2, 4.2 ),
			array( null, null ),
			array( false, false ),
			array( true, true ),
			array( array(), array() ),
			array( array( 1 ), array( 1 ) ),
			array( array( 1, 2, 'a' ), array( 1, 2, 'a' ) ),
			array( array( 'a' => 1, 'b' => 2, null ), array( 'a' => 1, 'b' => 2, null ) ),
		);
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual( $firstValue, $secondValue ) {
		$comparer = new StrictComparer();

		$this->assertFalse( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function unequalProvider() {
		return array(
			array( 1, 2 ),
			array( '', '0' ),
			array( '', ' ' ),
			array( '', 0 ),
			array( '', false ),
			array( null, false ),
			array( null, 0 ),
			array( '1', '01' ),
			array( 'foo bar', 'foo bar ' ),
			array( 4, 4.0 ),
			array( 4.2, 4.3 ),
			array( false, true ),
			array( true, '1' ),
			array( array(), array( 1 ) ),
			array( array( 1 ), array( 2 ) ),
			array( array( 1, 2, 'b' ), array( 1, 2, 'c' ) ),
			array( array( 'a' => 1, 'b' => 2 ), array( 'a' => 1, 'b' => 2, null ) ),
			array( new \stdClass(), new \stdClass() ),
			array( (object)array( 'a' => 1, 'b' => 2, null ), (object)array( 'a' => 1, 'b' => 3, null ) ),
		);
	}

}
