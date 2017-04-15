<?php

declare( strict_types = 1 );

namespace Diff\Tests\Differ;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\ArrayComparer\StrictArrayComparer;
use Diff\Differ\Differ;
use Diff\Differ\ListDiffer;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Differ\ListDiffer
 *
 * @group Diff
 * @group Differ
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDifferTest extends DiffTestCase {

	public function arrayComparerProvider() {
		$add = array( new DiffOpAdd( 1 ) );

		return array(
			'null' => array( null, $add ),
			'native object' => array( new NativeArrayComparer(), array() ),
			'strict object' => array( new StrictArrayComparer(), $add ),
		);
	}

	/**
	 * @dataProvider arrayComparerProvider
	 */
	public function testConstructor( $arrayComparer, array $expected ) {
		$differ = new ListDiffer( $arrayComparer );
		$diff = $differ->doDiff( array( 1 ), array( 1, 1 ) );
		$this->assertEquals( $expected, $diff );
	}

	/**
	 * Returns those that both work for native and strict mode.
	 */
	private function getCommonArgLists() {
		$argLists = array();

		$old = array();
		$new = array();
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between empty arrays' );

		$old = array( 42 );
		$new = array( 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between arrays with the same element' );

		$old = array( 42, 'ohi', 4.2, false );
		$new = array( 42, 'ohi', 4.2, false );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between arrays with the same elements' );

		$old = array( 42, 'ohi', 4.2, false );
		$new = array( false, 4.2, 'ohi', 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'There should be no difference between arrays with the same elements even when not ordered the same' );

		$old = array();
		$new = array( 42 );
		$expected = array( new DiffOpAdd( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'An array with a single element should be an add operation different from an empty array' );

		$old = array( 42 );
		$new = array();
		$expected = array( new DiffOpRemove( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'An empty array should be a remove operation different from an array with one element' );

		$old = array( 1 );
		$new = array( 2 );
		$expected = array( new DiffOpRemove( 1 ), new DiffOpAdd( 2 ) );

		$argLists[] = array( $old, $new, $expected,
			'Two arrays with a single different element should differ by an add and a remove op' );

		$old = array( 9001, 42, 1, 0 );
		$new = array( 9001, 2, 0, 42 );
		$expected = array( new DiffOpRemove( 1 ), new DiffOpAdd( 2 ) );

		$argLists[] = array(
			$old,
			$new,
			$expected,
			'Two arrays with a single different element should differ by an add'
				. 'and a remove op even when they share identical elements'
		);

		return $argLists;
	}

	public function toDiffProvider() {
		$argLists = $this->getCommonArgLists();

		$old = array( 42, 42 );
		$new = array( 42 );
		$expected = array( new DiffOpRemove( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'[42, 42] to [42] should [rem(42)]' );

		$old = array( 42 );
		$new = array( 42, 42 );
		$expected = array( new DiffOpAdd( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'[42] to [42, 42] should [add(42)]' );

		$old = array( '42' );
		$new = array( 42 );
		$expected = array( new DiffOpRemove( '42' ), new DiffOpAdd( 42 ) );

		$argLists[] = array( $old, $new, $expected,
			'["42"] to [42] should [rem("42"), add(42)]' );

		$old = array( array( 1 ) );
		$new = array( array( 2 ) );
		$expected = array( new DiffOpRemove( array( 1 ) ), new DiffOpAdd( array( 2 ) ) );

		$argLists[] = array( $old, $new, $expected,
			'[[1]] to [[2]] should [rem([1]), add([2])]' );

		$old = array( array( 2 ) );
		$new = array( array( 2 ) );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'[[2]] to [[2]] should result in an empty diff' );

		// test "soft" object comparison
		$obj1 = new \stdClass();
		$obj2 = new \stdClass();
		$objX = new \stdClass();

		$obj1->test = 'Test';
		$obj2->test = 'Test';
		$objX->xest = 'Test';

		$old = array( $obj1 );
		$new = array( $obj2 );
		$expected = array( );

		$argLists[] = array( $old, $new, $expected,
			'Two arrays containing equivalent objects should result in an empty diff' );

		$old = array( $obj1 );
		$new = array( $objX );
		$expected = array( new DiffOpRemove( $obj1 ), new DiffOpAdd( $objX )  );

		$argLists[] = array( $old, $new, $expected,
			'Two arrays containing different objects of the same type should result in an add and a remove op.' );

		return $argLists;
	}

	/**
	 * @dataProvider toDiffProvider
	 */
	public function testDoDiff( $old, $new, $expected, $message = '' ) {
		$this->doTestDiff( new ListDiffer(), $old, $new, $expected, $message );
	}

	public function toDiffNativeProvider() {
		$argLists = $this->getCommonArgLists();

		$old = array( '42' );
		$new = array( 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'["42"] to [42] should result in an empty diff' );

		$old = array( 42, 42 );
		$new = array( 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'[42, 42] to [42] should result in an empty diff' );

		$old = array( 42 );
		$new = array( 42, 42 );
		$expected = array();

		$argLists[] = array( $old, $new, $expected,
			'[42] to [42, 42] should result in an empty diff' );

		// TODO: test toString()-based object comparison

		return $argLists;
	}

	/**
	 * @dataProvider toDiffNativeProvider
	 */
	public function testDoNativeDiff( $old, $new, $expected, $message = '' ) {
		$this->doTestDiff( new ListDiffer( new NativeArrayComparer() ), $old, $new, $expected, $message );
	}

	private function doTestDiff( Differ $differ, $old, $new, $expected, $message ) {
		$actual = $differ->doDiff( $old, $new );

		$this->assertArrayEquals( $expected, $actual, false, false, $message );
	}

	public function testDiffCallsArrayComparatorCorrectly() {
		$arrayComparer = $this->createMock( 'Diff\ArrayComparer\ArrayComparer' );

		$arrayComparer->expects( $this->exactly( 2 ) )
			->method( 'diffArrays' )
			->with(
				$this->equalTo( array( 42 ) ),
				$this->equalTo( array( 42 ) )
			)
			->will( $this->returnValue( array() ) );

		$differ = new ListDiffer( $arrayComparer );

		$differ->doDiff( array( 42 ), array( 42 ) );
	}

}
