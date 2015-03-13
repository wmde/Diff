<?php

namespace Diff\Tests\DiffOp\Diff;

use Diff\ArrayComparer\NativeArrayComparer;
use Diff\Differ\ListDiffer;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffOp\DiffOpTest;

/**
 * @covers Diff\DiffOp\Diff\ListDiff
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffTest extends DiffOpTest {

	/**
	 * @see DiffOpTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOp\Diff\ListDiff';
	}

	/**
	 * @see DiffOpTest::constructorProvider
	 *
	 * @since 0.1
	 */
	public function constructorProvider() {
		$operationLists = array();

		$operationLists[] = array();

		$operationLists[] = array(
			new DiffOpAdd( 42 ),
		);

		$operationLists[] = array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 1 ),
		);

		$operationLists[] = array(
			new DiffOpRemove( 'spam' ),
			new DiffOpAdd( 42 ),
			new DiffOpAdd( 42 ),
			new DiffOpAdd( 9001 ),
			new DiffOpRemove( 1 ),
		);

		$argLists = array();

		foreach ( $operationLists as $operationList ) {
			$argLists[] = array( true, $operationList );
			$argLists[] = array( true, $operationList, 'foobar' );
		}

		$argLists[] = array( false, 42 );
		$argLists[] = array( false, new DiffOpAdd( 42 ) );
		$argLists[] = array( false, '~=[,,_,,]:3' );

		return $argLists;
	}

	public function newFromArraysProvider() {
		return array(
			array(
				array(),
				array(),
				array(),
				array(),
			),
			array(
				array( 'foo' ),
				array(),
				array(),
				array( 'foo' ),
			),
			array(
				array(),
				array( 'foo' ),
				array( 'foo' ),
				array(),
			),
			array(
				array( 'foo' ),
				array( 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo', 'foo' ),
				array( 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo' ),
				array( 'foo', 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo', 'bar' ),
				array( 'bar', 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo', 'bar', 42, 'baz' ),
				array( 42, 1, 2, 3 ),
				array( 1, 2, 3 ),
				array( 'foo', 'bar', 'baz' ),
			),
			array(
				array( false, null ),
				array( 0, '0' ),
				array( 0, '0' ),
				array( false, null ),
			),
		);
	}

	/**
	 * @dataProvider newFromArraysProvider
	 */
	public function testNewFromArrays( array $from, array $to, array $additions, array $removals ) {
		$differ = new ListDiffer( new NativeArrayComparer() );

		$diff = new Diff( $differ->doDiff( $from, $to ), false );

		$this->assertInstanceOf( '\Diff\DiffOp\DiffOp', $diff );
		$this->assertInstanceOf( '\Diff\DiffOp\Diff\Diff', $diff );
		$this->assertInstanceOf( '\ArrayObject', $diff );

		// array_values because we only care about the values, not promises are made about the keys.
		$resultAdditions = array_values( $diff->getAddedValues() );
		$resultRemovals = array_values( $diff->getRemovedValues() );

		// Sort everything since no promises are made about ordering.
		asort( $resultAdditions );
		asort( $resultRemovals );
		asort( $additions );
		asort( $removals );

		$this->assertEquals( $additions, $resultAdditions, 'additions mismatch' );
		$this->assertEquals( $removals, $resultRemovals, 'removals mismatch' );

		$this->assertEquals(
			$additions === array() && $removals === array(),
			$diff->isEmpty()
		);
	}

}

