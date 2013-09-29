<?php

namespace Diff\Tests\Comparer;

use Diff\Comparer\ComparableComparer;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Comparer\ComparableComparer
 *
 * @group Diff
 * @group Comparer
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ComparableComparerTest extends DiffTestCase {

	/**
	 * @dataProvider equalProvider
	 */
	public function testEqualValuesAreEqual( $firstValue, $secondValue ) {
		$comparer = new ComparableComparer();

		$this->assertTrue( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function equalProvider() {
		return array(
			array(
				new StubComparable( 100 ),
				new StubComparable( 100 ),
			),
			array(
				new StubComparable( 'abc' ),
				new StubComparable( 'abc' ),
			),
			array(
				new StubComparable( null ),
				new StubComparable( null ),
			),
		);
	}

	/**
	 * @dataProvider unequalProvider
	 */
	public function testDifferentValuesAreNotEqual( $firstValue, $secondValue ) {
		$comparer = new ComparableComparer();

		$this->assertFalse( $comparer->valuesAreEqual( $firstValue, $secondValue ) );
	}

	public function unequalProvider() {
		return array(
			array(
				new StubComparable( 1 ),
				new StubComparable( 2 ),
			),
			array(
				new StubComparable( 1 ),
				new StubComparable( '1' ),
			),
			array(
				new StubComparable( null ),
				new StubComparable( false ),
			),
		);
	}

}

class StubComparable {

	protected $field;

	public function __construct( $field ) {
		$this->field = $field;
	}

	public function equals( $otherComparable ) {
		return $otherComparable instanceof StubComparable
			&& $otherComparable->getField() === $this->field;
	}

	public function getField() {
		return $this->field;
	}

}
