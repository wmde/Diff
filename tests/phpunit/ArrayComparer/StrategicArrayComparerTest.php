<?php

declare( strict_types = 1 );

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\ArrayComparer;
use Diff\ArrayComparer\StrategicArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\ArrayComparer\StrategicArrayComparer
 *
 * @group Diff
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrategicArrayComparerTest extends DiffTestCase {

	public function testCanConstruct() {
		new StrategicArrayComparer( $this->createMock( 'Diff\Comparer\ValueComparer' ) );
		$this->assertTrue( true );
	}

	public function testDiffArraysWithComparerThatAlwaysReturnsTrue() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->will( $this->returnValue( true ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$this->assertNoDifference(
			$arrayComparer,
			array( 0, 2, 4 ),
			array( 1, 2, 9 )
		);

		$this->assertNoDifference(
			$arrayComparer,
			array( 1, 2, 3 ),
			array( 1, 2, 3 )
		);

		$this->assertNoDifference(
			$arrayComparer,
			array( 'bah' ),
			array( 'foo', 'bar', 'baz' )
		);

		$this->assertNoDifference(
			$arrayComparer,
			array(),
			array( 'foo', 'bar', 'baz' )
		);

		$this->assertNoDifference(
			$arrayComparer,
			array(),
			array()
		);
	}

	private function assertNoDifference( ArrayComparer $arrayComparer, array $arrayOne, array $arrayTwo ) {
		$this->assertEquals(
			array(),
			$arrayComparer->diffArrays(
				$arrayOne,
				$arrayTwo
			)
		);
	}

	public function testDiffArraysWithComparerThatAlwaysReturnsFalse() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->will( $this->returnValue( false ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$this->assertAllDifferent(
			$arrayComparer,
			array(),
			array()
		);

		$this->assertAllDifferent(
			$arrayComparer,
			array( 1, 2, 3 ),
			array()
		);

		$this->assertAllDifferent(
			$arrayComparer,
			array( 1, 2, 3 ),
			array( 1, 2, 3 )
		);

		$this->assertAllDifferent(
			$arrayComparer,
			array(),
			array( 1, 2, 3 )
		);
	}

	private function assertAllDifferent( ArrayComparer $arrayComparer, array $arrayOne, array $arrayTwo ) {
		$this->assertEquals(
			$arrayOne,
			$arrayComparer->diffArrays(
				$arrayOne,
				$arrayTwo
			)
		);
	}

	public function testQuantityMattersWithReturnTrue() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->will( $this->returnValue( true ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$this->assertEquals(
			array( 1, 1, 1 ),
			$arrayComparer->diffArrays(
				array( 1, 1, 1, 1 ),
				array( 1 )
			)
		);

		$this->assertEquals(
			array( 1 ),
			$arrayComparer->diffArrays(
				array( 1, 1, 1, 1 ),
				array( 1, 1, 1  )
			)
		);
	}

	public function testQuantityMattersWithSimpleComparison() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->will( $this->returnCallback( function( $firstValue, $secondValue ) {
				return $firstValue == $secondValue;
			} ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$this->assertEquals(
			array( 1, 2, 5 ),
			$arrayComparer->diffArrays(
				array( 1, 1, 2, 3, 2, 5 ),
				array( 1, 2, 3, 4  )
			)
		);

		$this->assertEquals(
			array( 1 ),
			$arrayComparer->diffArrays(
				array( 1, 1, 1, 2, 2, 3 ),
				array( 1, 1, 2, 2, 3, 3, 3 )
			)
		);

		$this->assertEquals(
			array( 1 ),
			$arrayComparer->diffArrays(
				array( 3, 1, 2, 1, 1, 2 ),
				array( 1, 3, 3, 2, 2, 3, 1 )
			)
		);
	}

	public function testValueComparerGetsCalledWithCorrectValues() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->once() )
			->method( 'valuesAreEqual' )
			->with(
				$this->equalTo( 1 ),
				$this->equalTo( 2 )
			)
			->will( $this->returnValue( true ) );

		$arrayComparer = new StrategicArrayComparer( $valueComparer );

		$arrayComparer->diffArrays(
			array( 1 ),
			array( 2 )
		);
	}

}
