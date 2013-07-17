<?php

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\StrategicArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\ArrayComparer\StrategicArrayComparer
 *
 * @file
 * @since 0.7
 *
 * @ingroup DiffTest
 *
 * @group Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrategicArrayComparerTest extends DiffTestCase {

	public function testCanConstruct() {
		new StrategicArrayComparer( $this->getMock( 'Diff\Comparer\ValueComparer' ) );
		$this->assertTrue( true );
	}

	public function testDiffArrays() {
		$valueComparer = $this->getMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->will( $this->returnCallback( function( $firstValue, $secondValue ) {
				return true;
			} ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$this->assertEquals(
			array(),
			$arrayComparer->diffArrays(
				array( 0, 2, 4 ),
				array( 1, 2, 9 )
			)
		);

		// TODO: implement
	}

}
