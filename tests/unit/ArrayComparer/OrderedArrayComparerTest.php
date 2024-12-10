<?php

declare( strict_types = 1 );

namespace Diff\Tests\ArrayComparer;

use Diff\ArrayComparer\ArrayComparer;
use Diff\ArrayComparer\OrderedArrayComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers \Diff\ArrayComparer\OrderedArrayComparer
 *
 * @since 0.9
 *
 * @group Diff
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Tobias Gritschacher < tobias.gritschacher@wikimedia.de >
 */
class OrderedArrayComparerTest extends DiffTestCase {

	public function testCanConstruct() {
		new OrderedArrayComparer( $this->createMock( 'Diff\Comparer\ValueComparer' ) );
		$this->assertTrue( true );
	}

	public function testDiffArraysWithComparerThatAlwaysReturnsTrue() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->willReturn( true );

		$arrayComparer = new OrderedArrayComparer( $valueComparer );

		$this->assertNoDifference(
			$arrayComparer,
			[ 0, 2, 4 ],
			[ 1, 2, 9 ]
		);

		$this->assertNoDifference(
			$arrayComparer,
			[ 1, 2, 3 ],
			[ 1, 2, 3 ]
		);

		$this->assertNoDifference(
			$arrayComparer,
			[ 'bah' ],
			[ 'foo', 'bar', 'baz' ]
		);

		$this->assertNoDifference(
			$arrayComparer,
			[],
			[ 'foo', 'bar', 'baz' ]
		);

		$this->assertNoDifference(
			$arrayComparer,
			[],
			[]
		);
	}

	private function assertNoDifference( ArrayComparer $arrayComparer, array $arrayOne, array $arrayTwo ) {
		$this->assertEquals(
			[],
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
			->willReturn( false );

		$arrayComparer = new OrderedArrayComparer( $valueComparer );

		$this->assertAllDifferent(
			$arrayComparer,
			[],
			[]
		);

		$this->assertAllDifferent(
			$arrayComparer,
			[ 1, 2, 3 ],
			[]
		);

		$this->assertAllDifferent(
			$arrayComparer,
			[ 1, 2, 3 ],
			[ 1, 2, 3 ]
		);

		$this->assertAllDifferent(
			$arrayComparer,
			[],
			[ 1, 2, 3 ]
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
			->willReturn( true );

		$arrayComparer = new OrderedArrayComparer( $valueComparer );

		$this->assertEquals(
			[ 1, 1, 1 ],
			$arrayComparer->diffArrays(
				[ 1, 1, 1, 1 ],
				[ 1 ]
			)
		);

		$this->assertEquals(
			[ 1 ],
			$arrayComparer->diffArrays(
				[ 1, 1, 1, 1 ],
				[ 1, 1, 1 ]
			)
		);
	}

	public function testQuantityMattersWithSimpleComparison() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->willReturnCallback( static function ( $firstValue, $secondValue ) {
				return $firstValue == $secondValue;
			} );

		$arrayComparer = new OrderedArrayComparer( $valueComparer );

		$this->assertEquals(
			[ 1, 2, 3, 2, 5 ],
			$arrayComparer->diffArrays(
				[ 1, 1, 2, 3, 2, 5 ],
				[ 1, 2, 3, 4 ]
			)
		);

		$this->assertEquals(
			[ 1, 2 ],
			$arrayComparer->diffArrays(
				[ 1, 1, 1, 2, 2, 3 ],
				[ 1, 1, 2, 2, 3, 3, 3 ]
			)
		);

		$this->assertEquals(
			[ 3, 1, 2, 1, 1, 2 ],
			$arrayComparer->diffArrays(
				[ 3, 1, 2, 1, 1, 2 ],
				[ 1, 3, 3, 2, 2, 3, 1 ]
			)
		);
	}

	public function testOrderMattersWithSimpleComparison() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->any() )
			->method( 'valuesAreEqual' )
			->willReturnCallback( static function ( $firstValue, $secondValue ) {
				return $firstValue == $secondValue;
			} );

		$arrayComparer = new OrderedArrayComparer( $valueComparer );

		$this->assertEquals(
			[],
			$arrayComparer->diffArrays(
				[ 1, 2, 3, 4, 5 ],
				[ 1, 2, 3, 4, 5 ]
			)
		);

		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			$arrayComparer->diffArrays(
				[ 1, 2, 3, 4, 5 ],
				[ 2, 1, 4, 3, 5 ]
			)
		);

		$this->assertEquals(
			[ 1, 5 ],
			$arrayComparer->diffArrays(
				[ 1, 2, 3, 4, 5 ],
				[ 5, 2, 3, 4, 1 ]
			)
		);

		$this->assertEquals(
			[ 1, 2, 3, 4, 5 ],
			$arrayComparer->diffArrays(
				[ 1, 2, 3, 4, 5 ],
				[ 2, 3, 4, 5 ]
			)
		);

		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			$arrayComparer->diffArrays(
				[ 1, 2, 3, 4 ],
				[ 5, 1, 2, 3, 4 ]
			)
		);
	}

	public function testValueComparerGetsCalledWithCorrectValues() {
		$valueComparer = $this->createMock( 'Diff\Comparer\ValueComparer' );

		$valueComparer->expects( $this->once() )
			->method( 'valuesAreEqual' )
			->with(
				1,
				2
			)
			->willReturn( true );

		$arrayComparer = new OrderedArrayComparer( $valueComparer );

		$arrayComparer->diffArrays(
			[ 1 ],
			[ 2 ]
		);
	}

}
