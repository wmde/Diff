<?php

declare( strict_types = 1 );

namespace Diff\Tests\Comparer;

use Diff\Comparer\CallbackComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Comparer\CallbackComparer
 *
 * @group Diff
 * @group Comparer
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackComparerTest extends DiffTestCase {

	private function newComparerInstance() {
		return new CallbackComparer( function( $firstValue, $secondValue ) {
			return $firstValue === 1 || $firstValue === $secondValue;
		} );
	}

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual( $firstValue, $secondValue ) {
		$comparer = $this->newComparerInstance();

		$this->assertTrue( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function equalProvider() {
		return array(
			array( 1, 0 ),
			array( 1, 1 ),
			array( 1, 2 ),
			array( 2, 2 ),
		);
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual( $firstValue, $secondValue ) {
		$comparer = $this->newComparerInstance();

		$this->assertFalse( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function unequalProvider() {
		return array(
			array( 0, 1 ),
			array( 0, 2 ),
			array( 0, '0' ),
		);
	}

}
