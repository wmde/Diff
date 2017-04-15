<?php

declare( strict_types = 1 );

namespace Diff\Tests\DiffOp\Diff;

use Closure;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffTestCase;
use stdClass;

/**
 * @covers Diff\DiffOp\Diff\Diff
 *
 * @group Diff
 * @group DiffOp
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class DiffTest extends DiffTestCase {

	public function elementInstancesProvider() {
		return array(
			array( array(
			) ),
			array( array(
				new DiffOpAdd( 'ohi' )
			) ),
			array( array(
				new DiffOpRemove( 'ohi' )
			) ),
			array( array(
				new DiffOpAdd( 'ohi' ),
				new DiffOpRemove( 'there' )
			) ),
			array( array(
			) ),
			array( array(
				new DiffOpAdd( 'ohi' ),
				new DiffOpRemove( 'there' ),
				new DiffOpChange( 'ohi', 'there' )
			) ),
			array( array(
				'1' => new DiffOpAdd( 'ohi' ),
				'33' => new DiffOpRemove( 'there' ),
				'7' => new DiffOpChange( 'ohi', 'there' )
			) ),
		);
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testGetAdditions( array $operations ) {
		$diff = new Diff( $operations, true );

		$additions = array();

		foreach ( $operations as $operation ) {
			if ( $operation->getType() == 'add' ) {
				$additions[] = $operation;
			}
		}

		$this->assertArrayEquals( $additions, $diff->getAdditions() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testGetRemovals( array $operations ) {
		$diff = new Diff( $operations, true );

		$removals = array();

		foreach ( $operations as $operation ) {
			if ( $operation->getType() == 'remove' ) {
				$removals[] = $operation;
			}
		}

		$this->assertArrayEquals( $removals, $diff->getRemovals() );
	}

	public function testGetType() {
		$diff = new Diff();
		$this->assertInternalType( 'string', $diff->getType() );
	}

	public function testPreSetElement() {
		$this->expectException( 'Exception' );

		$diff = new Diff( array(), false );
		$diff[] = new DiffOpChange( 0, 1 );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testAddOperations( array $operations ) {
		$diff = new Diff();

		$diff->addOperations( $operations );

		$this->assertArrayEquals( $operations, $diff->getOperations() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 * @param DiffOp[] $operations
	 */
	public function testStuff( array $operations ) {
		$diff = new Diff( $operations );

		$this->assertInstanceOf( 'Diff\DiffOp\Diff\Diff', $diff );
		$this->assertInstanceOf( 'ArrayObject', $diff );

		$types = array();

		$this->assertContainsOnlyInstancesOf( 'Diff\DiffOp\DiffOp', $diff );

		/**
		 * @var DiffOp $operation
		 */
		foreach ( $diff as $operation ) {
			if ( !in_array( $operation->getType(), $types ) ) {
				$types[] = $operation->getType();
			}
		}

		$count = 0;

		foreach ( $types as $type ) {
			$count += count( $diff->getTypeOperations( $type ) );
		}

		$this->assertEquals( $count, $diff->count() );
	}

	public function instanceProvider() {
		$instances = array();

		foreach ( $this->elementInstancesProvider() as $args ) {
			$diffOps = $args[0];
			$instances[] = array( new Diff( $diffOps ) );
		}

		return $instances;
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetOperations( Diff $diff ) {
		$ops = $diff->getOperations();

		$this->assertInternalType( 'array', $ops );
		$this->assertContainsOnlyInstancesOf( 'Diff\DiffOp\DiffOp', $ops );
		$this->assertArrayEquals( $ops, $diff->getOperations() );
	}

	public function testRemoveEmptyOperations() {
		$diff = new Diff( array() );

		$diff['foo'] = new DiffOpAdd( 1 );
		$diff['bar'] = new Diff( array( new DiffOpAdd( 1 ) ), true );
		$diff['baz'] = new Diff( array( new DiffOpAdd( 1 ) ), false );
		$diff['bah'] = new Diff( array(), false );
		$diff['spam'] = new Diff( array(), true );

		$diff->removeEmptyOperations();

		$this->assertTrue( $diff->offsetExists( 'foo' ) );
		$this->assertTrue( $diff->offsetExists( 'bar' ) );
		$this->assertTrue( $diff->offsetExists( 'baz' ) );
		$this->assertFalse( $diff->offsetExists( 'bah' ) );
		$this->assertFalse( $diff->offsetExists( 'spam' ) );
	}

	public function looksAssociativeProvider() {
		return array(
			array( new Diff(), false ),
			array( new Diff( array(), false ), false ),
			array( new Diff( array(), true ), true ),
			array( new Diff( array( new DiffOpAdd( '' ) ) ), false ),
			array( new Diff( array( new DiffOpRemove( '' ) ) ), false ),
			array( new Diff( array( new DiffOpRemove( '' ), new DiffOpAdd( '' ) ) ), false ),
			array( new Diff( array( new DiffOpRemove( '' ) ), true ), true ),
			array( new Diff( array( 'onoez' => new DiffOpChange( '', 'spam' ) ) ), true ),
			array( new Diff( array( new Diff() ) ), true ),
		);
	}

	/**
	 * @dataProvider looksAssociativeProvider
	 */
	public function testLooksAssociative( Diff $diff, $looksAssoc ) {
		$this->assertEquals( $looksAssoc, $diff->looksAssociative() );

		if ( !$diff->looksAssociative() ) {
			$this->assertFalse( $diff->hasAssociativeOperations() );
		}
	}

	public function isAssociativeProvider() {
		return array(
			array( new Diff(), null ),
			array( new Diff( array(), false ), false ),
			array( new Diff( array(), true ), true ),
			array( new Diff( array( new DiffOpAdd( '' ) ) ), null ),
			array( new Diff( array( new DiffOpRemove( '' ) ), false ), false ),
			array( new Diff( array( new DiffOpRemove( '' ), new DiffOpAdd( '' ) ) ), null ),
			array( new Diff( array( new DiffOpRemove( '' ) ), true ), true ),
			array( new Diff( array( 'onoez' => new DiffOpChange( '', 'spam' ) ) ), null ),
			array( new Diff( array( new Diff() ) ), null ),
		);
	}

	/**
	 * @dataProvider isAssociativeProvider
	 */
	public function testIsAssociative( Diff $diff, $isAssoc ) {
		$this->assertEquals( $isAssoc, $diff->isAssociative() );
	}

	public function hasAssociativeOperationsProvider() {
		return array(
			array( new Diff(), false ),
			array( new Diff( array(), false ), false ),
			array( new Diff( array(), true ), false ),
			array( new Diff( array( new DiffOpAdd( '' ) ) ), false ),
			array( new Diff( array( new DiffOpRemove( '' ) ), false ), false ),
			array( new Diff( array( new DiffOpRemove( '' ), new DiffOpAdd( '' ) ), true ), false ),
			array( new Diff( array( new DiffOpRemove( '' ) ), true ), false ),
			array( new Diff( array( 'onoez' => new DiffOpChange( '', 'spam' ) ) ), true ),
			array( new Diff( array( new Diff() ) ), true ),
		);
	}

	/**
	 * @dataProvider hasAssociativeOperationsProvider
	 */
	public function testHasAssociativeOperations( Diff $diff, $hasAssocOps ) {
		$this->assertEquals( $hasAssocOps, $diff->hasAssociativeOperations() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @since 0.6
	 *
	 * @param DiffOp[] $elements
	 */
	public function testConstructor( array $elements ) {
		$arrayObject = new Diff( $elements );

		$this->assertEquals( count( $elements ), $arrayObject->count() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @since 0.6
	 *
	 * @param DiffOp[] $elements
	 */
	public function testIsEmpty( array $elements ) {
		$arrayObject = new Diff( $elements );

		$this->assertEquals( $elements === array(), $arrayObject->isEmpty() );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @since 0.6
	 *
	 * @param Diff $list
	 */
	public function testUnset( Diff $list ) {
		if ( $list->isEmpty() ) {
			$this->assertTrue( true ); // We cannot test unset if there are no elements
		} else {
			$offset = $list->getIterator()->key();
			$count = $list->count();
			$list->offsetUnset( $offset );
			$this->assertEquals( $count - 1, $list->count() );
		}

		if ( !$list->isEmpty() ) {
			$offset = $list->getIterator()->key();
			$count = $list->count();
			unset( $list[$offset] );
			$this->assertEquals( $count - 1, $list->count() );
		}
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @since 0.6
	 *
	 * @param DiffOp[] $elements
	 */
	public function testAppend( array $elements ) {
		$list = new Diff();

		$listSize = count( $elements );

		foreach ( $elements as $element ) {
			$list->append( $element );
		}

		$this->assertEquals( $listSize, $list->count() );

		$list = new Diff();

		foreach ( $elements as $element ) {
			$list[] = $element;
		}

		$this->assertEquals( $listSize, $list->count() );

		$this->checkTypeChecks( function ( Diff $list, $element ) {
			$list->append( $element );
		} );
	}

	/**
	 * @param Closure $function
	 */
	private function checkTypeChecks( Closure $function ) {
		$excption = null;
		$list = new Diff();

		foreach ( array( 42, 'foo', array(), new stdClass(), 4.2 ) as $element ) {
			$this->assertInvalidArgument( $function, $list, $element );
		}
	}

	/**
	 * Asserts that an InvalidArgumentException gets thrown when calling the provided
	 * callable. Extra arguments specified to the method are also provided to the callable.
	 *
	 * @param Closure $function
	 */
	private function assertInvalidArgument( Closure $function ) {
		$this->expectException( 'InvalidArgumentException' );

		$arguments = func_get_args();
		array_shift( $arguments );

		call_user_func_array( $function, $arguments );
	}

	public function testGetAddedValues() {
		$diff = new Diff( array(
			new DiffOpAdd( 0 ),
			new DiffOpRemove( 1 ),
			new DiffOpAdd( 2 ),
			new DiffOpRemove( 3 ),
			new DiffOpAdd( 4 ),
			new DiffOpChange( 7, 5 ),
			new Diff( array( new DiffOpAdd( 9 ) ) ),
		) );

		$addedValues = $diff->getAddedValues();

		$this->assertInternalType( 'array', $addedValues );

		$this->assertArrayEquals( array( 0, 2, 4 ), $addedValues );

		$diff = new Diff();
		$this->assertArrayEquals( array(), $diff->getAddedValues() );
	}

	public function testGetRemovedValues() {
		$diff = new Diff( array(
			new DiffOpAdd( 0 ),
			new DiffOpRemove( 1 ),
			new DiffOpAdd( 2 ),
			new DiffOpRemove( 3 ),
			new DiffOpAdd( 4 ),
			new DiffOpChange( 6, 4 ),
			new Diff( array( new DiffOPRemove( 8 ) ) ),
		) );

		$removedValues = $diff->getRemovedValues();

		$this->assertInternalType( 'array', $removedValues );

		$this->assertArrayEquals( array( 1, 3 ), $removedValues );

		$diff = new Diff();
		$this->assertArrayEquals( array(), $diff->getRemovedValues() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @since 0.6
	 *
	 * @param DiffOp[] $elements
	 */
	public function testOffsetSet( array $elements ) {
		if ( $elements === array() ) {
			$this->assertTrue( true );
			return;
		}

		$list = new Diff();

		$element = reset( $elements );
		$list->offsetSet( 42, $element );
		$this->assertEquals( $element, $list->offsetGet( 42 ) );

		$list = new Diff();

		$element = reset( $elements );
		$list['oHai'] = $element;
		$this->assertEquals( $element, $list['oHai'] );

		$list = new Diff();

		$element = reset( $elements );
		$list->offsetSet( 9001, $element );
		$this->assertEquals( $element, $list[9001] );

		$list = new Diff();

		$element = reset( $elements );
		$list->offsetSet( null, $element );
		$this->assertEquals( $element, $list[0] );

		$list = new Diff();
		$offset = 0;

		foreach ( $elements as $element ) {
			$list->offsetSet( null, $element );
			$this->assertEquals( $element, $list[$offset++] );
		}

		$this->assertEquals( count( $elements ), $list->count() );

		$this->checkTypeChecks( function ( Diff $list, $element ) {
			$list->offsetSet( mt_rand(), $element );
		} );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @since 0.6
	 *
	 * @param Diff $list
	 */
	public function testSerialization( Diff $list ) {
		$serialization = serialize( $list );
		$copy = unserialize( $serialization );

		$this->assertSame( $serialization, serialize( $copy ) );
		$this->assertSame( $list->count(), $copy->count() );

		$list = $list->getArrayCopy();
		$copy = $copy->getArrayCopy();

		$this->assertArrayEquals( $list, $copy, true, true );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @since 0.6
	 *
	 * @param Diff $list
	 */
	public function testAddInvalidDiffOp( Diff $list ) {
		$invalidDiffOp = $this->createMock( 'Diff\DiffOp\DiffOp' );

		$invalidDiffOp->expects( $this->atLeastOnce() )
			->method( 'getType' )
			->will( $this->returnValue( '~=[,,_,,]:3' ) );

		$this->expectException( 'Exception' );

		$list->append( $invalidDiffOp );
	}

	/**
	 * @dataProvider invalidIsAssociativeProvider
	 */
	public function testConstructWithInvalidIsAssociative( $isAssociative ) {
		$this->expectException( 'InvalidArgumentException' );
		new Diff( array(), $isAssociative );
	}

	public function invalidIsAssociativeProvider() {
		return array(
			array( 1 ),
			array( '1' ),
			array( 'null' ),
			array( 0 ),
			array( array() ),
			array( 'foobar' ),
		);
	}

	/**
	 * @dataProvider invalidDiffOpsProvider
	 */
	public function testConstructorWithInvalidDiffOps( array $diffOps ) {
		$this->expectException( 'InvalidArgumentException' );
		new Diff( $diffOps );
	}

	public function invalidDiffOpsProvider() {
		return array(
			array( array(
				'foo',
			) ),
			array( array(
				null,
			) ),
			array( array(
				false,
				true,
				array(),
			) ),
			array( array(
				new DiffOpAdd( 42 ),
				'in your list',
				new DiffOpAdd( 9001 ),
			) )
		);
	}

	/**
	 * @dataProvider equalsProvider
	 */
	public function testEquals( Diff $diff, Diff $target ) {
		$this->assertTrue( $diff->equals( $target ) );
		$this->assertTrue( $target->equals( $diff ) );
	}

	public function equalsProvider() {
		$empty = new Diff();

		return array(
			// Identity
			array( $empty, $empty ),

			// Empty diffs
			array( $empty, new Diff() ),
			array( $empty, new Diff( array(), null ) ),

			// Simple diffs
			array( new Diff( array( new DiffOpAdd( 1 ) ) ), new Diff( array( new DiffOpAdd( 1 ) ) ) ),
			array( new Diff( array( new DiffOpAdd( 1 ) ) ), new Diff( array( new DiffOpAdd( '1' ) ) ) ),
		);
	}

	/**
	 * @dataProvider notEqualsProvider
	 */
	public function testNotEquals( Diff $diff, $target ) {
		$this->assertFalse( $diff->equals( $target ) );
	}

	public function notEqualsProvider() {
		return array(
			// Not an instance or subclass of Diff
			array( new Diff(), null ),
			array( new Diff(), new DiffOpAdd( 1 ) ),

			// Empty diffs
			array( new Diff(), new Diff( array(), false ) ),
			array( new Diff(), new Diff( array(), true ) ),

			// Simple diffs
			array( new Diff(), new Diff( array( new DiffOpAdd( 1 ) ) ) ),
			array( new Diff( array( new DiffOpAdd( 1 ) ) ), new Diff( array( new DiffOpRemove( 1 ) ) ) ),
			array( new Diff( array( new DiffOpAdd( 1 ) ) ), new Diff( array( new DiffOpAdd( 2 ) ) ) ),
		);
	}

}
