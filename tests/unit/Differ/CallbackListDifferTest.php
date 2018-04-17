<?php

declare( strict_types = 1 );

namespace Diff\Tests\Differ;

use Diff\Differ\CallbackListDiffer;
use Diff\Differ\Differ;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;

/**
 * @covers Diff\Differ\CallbackListDiffer
 *
 * @group Diff
 * @group Differ
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackListDifferTest extends DiffTestCase {

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
			'Two arrays with a single different element should differ by an add '
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
		$callback = function( $foo, $bar ) {
			return is_object( $foo ) ? $foo == $bar : $foo === $bar;
		};

		$this->doTestDiff( new CallbackListDiffer( $callback ), $old, $new, $expected, $message );
	}

	private function doTestDiff( Differ $differ, $old, $new, $expected, $message ) {
		$actual = $differ->doDiff( $old, $new );

		$this->assertArrayEquals( $expected, $actual, false, false, $message );
	}

	public function testCallbackComparisonReturningFalse() {
		$differ = new CallbackListDiffer( function( $foo, $bar ) {
			return false;
		} );

		$actual = $differ->doDiff( array( 1, '2' ), array( 1, '2', 'foo' ) );

		$expected = array(
			new DiffOpAdd( 1 ),
			new DiffOpAdd( '2' ),
			new DiffOpAdd( 'foo' ),
			new DiffOpRemove( 1 ),
			new DiffOpRemove( '2' ),
		);

		$this->assertArrayEquals(
			$expected, $actual, false, false,
			'All elements should be removed and added when comparison callback always returns false'
		);
	}

	public function testCallbackComparisonReturningTrue() {
		$differ = new CallbackListDiffer( function( $foo, $bar ) {
			return true;
		} );

		$actual = $differ->doDiff( array( 1, '2', 'baz' ), array( 1, 'foo', '2' ) );

		$expected = array();

		$this->assertArrayEquals(
			$expected, $actual, false, false,
			'No elements should be removed or added when comparison callback always returns true'
		);
	}

	public function testCallbackComparisonReturningNyanCat() {
		$differ = new CallbackListDiffer( function( $foo, $bar ) {
			return '~=[,,_,,]:3';
		} );

		$this->expectException( 'RuntimeException' );

		$differ->doDiff( array( 1, '2', 'baz' ), array( 1, 'foo', '2' ) );
	}

}
