<?php

namespace Diff\Tests;

use Diff\Diff;
use Diff\DiffOp;
use Diff\DiffOpAdd;
use Diff\DiffOpChange;
use Diff\DiffOpRemove;
use stdClass;

/**
 * @covers Diff\Diff
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
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
	 */
	public function testGetAdditions( array $operations ) {
		$diff = new Diff( $operations, true );

		$additions = array();

		/**
		 * @var DiffOp $operation
		 */
		foreach ( $operations as $operation ) {
			if ( $operation->getType() == 'add' ) {
				$additions[] = $operation;
			}
		}

		$this->assertArrayEquals( $additions, $diff->getAdditions() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testGetRemovals( array $operations ) {
		$diff = new Diff( $operations, true );

		$removals = array();

		/**
		 * @var DiffOp $operation
		 */
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
		$pokemons = null;

		$diff = new Diff( array(), false );

		try {
			$diff[] = new DiffOpChange( 0, 1 );
		}
		catch( \Exception $pokemons ) {}

		$this->assertInstanceOf( '\Exception', $pokemons );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testAddOperations( array $operations ) {
		$diff = new Diff();

		$diff->addOperations( $operations );

		$this->assertArrayEquals( $operations, $diff->getOperations() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testStuff( array $operations ) {
		$diff = new Diff( $operations );

		$this->assertInstanceOf( '\Diff\Diff', $diff );
		$this->assertInstanceOf( '\ArrayObject', $diff );

		$types = array();

		$this->assertContainsOnlyInstancesOf( '\Diff\DiffOp', $diff );

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

	public function getInstanceClass() {
		return '\Diff\Diff';
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetOperations( Diff $diff ) {
		$ops = $diff->getOperations();

		$this->assertInternalType( 'array', $ops );
		$this->assertContainsOnlyInstancesOf( '\Diff\DiffOp', $ops );
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
		$argLists = array();

		$diff = new Diff();

		$argLists[] = array( $diff, false );


		$diff = new Diff( array(), false );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array(), true );

		$argLists[] = array( $diff, true );


		$diff = new Diff( array( new DiffOpAdd( '' ) ) );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ) ) );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ), new DiffOpAdd( '' ) ) );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ) ), true );

		$argLists[] = array( $diff, true );


		$diff = new Diff( array( 'onoez' => new DiffOpChange( '', 'spam' ) ) );

		$argLists[] = array( $diff, true );


		$diff = new Diff( array( new Diff() ) );

		$argLists[] = array( $diff, true );

		return $argLists;
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
		$argLists = array();

		$diff = new Diff();

		$argLists[] = array( $diff, null );


		$diff = new Diff( array(), false );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array(), true );

		$argLists[] = array( $diff, true );


		$diff = new Diff( array( new DiffOpAdd( '' ) ) );

		$argLists[] = array( $diff, null );


		$diff = new Diff( array( new DiffOpRemove( '' ) ), false );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ), new DiffOpAdd( '' ) ) );

		$argLists[] = array( $diff, null );


		$diff = new Diff( array( new DiffOpRemove( '' ) ), true );

		$argLists[] = array( $diff, true );


		$diff = new Diff( array( 'onoez' => new DiffOpChange( '', 'spam' ) ) );

		$argLists[] = array( $diff, null );


		$diff = new Diff( array( new Diff() ) );

		$argLists[] = array( $diff, null );

		return $argLists;
	}

	/**
	 * @dataProvider isAssociativeProvider
	 */
	public function testIsAssociative( Diff $diff, $isAssoc ) {
		$this->assertEquals( $isAssoc, $diff->isAssociative() );
	}

	public function hasAssociativeOperationsProvider() {
		$argLists = array();

		$diff = new Diff();

		$argLists[] = array( $diff, false );


		$diff = new Diff( array(), false );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array(), true );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpAdd( '' ) ) );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ) ), false );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ), new DiffOpAdd( '' ) ), true );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( new DiffOpRemove( '' ) ), true );

		$argLists[] = array( $diff, false );


		$diff = new Diff( array( 'onoez' => new DiffOpChange( '', 'spam' ) ) );

		$argLists[] = array( $diff, true );


		$diff = new Diff( array( new Diff() ) );

		$argLists[] = array( $diff, true );

		return $argLists;
	}

	/**
	 * @dataProvider hasAssociativeOperationsProvider
	 */
	public function testHasAssociativeOperations( Diff $diff, $hasAssocOps ) {
		$this->assertEquals( $hasAssocOps, $diff->hasAssociativeOperations() );
	}

	public function testSerializationCompat() {
//		$expected = new \Diff\MapDiff( array(
//			new \Diff\DiffOpAdd( 'add' ),
//			new \Diff\DiffOpRemove( 'rem' ),
//			new \Diff\DiffOpChange( 'a', 'b' ),
//			new \Diff\ListDiff( array( new \Diff\DiffOpRemove( 'rem' ) ) )
//		) );

		$v03serialization = 'C:12:"Diff\MapDiff":569:{a:4:{s:4:"data";a:4:{i:0;C:14:"Diff\DiffOpAdd":10:{s:3:"add";}i:1;C:17:"Diff\DiffOpRemove":10:{s:3:"rem";}i:2;C:17:"Diff\DiffOpChange":30:{a:2:{i:0;s:1:"b";i:1;s:1:"a";}}i:3;C:13:"Diff\ListDiff":170:{a:4:{s:4:"data";a:1:{i:0;C:17:"Diff\DiffOpRemove":10:{s:3:"rem";}}s:5:"index";i:0;s:12:"typePointers";a:2:{s:3:"add";a:0:{}s:6:"remove";a:1:{i:0;i:0;}}s:9:"parentKey";N;}}}s:5:"index";i:0;s:12:"typePointers";a:6:{s:3:"add";a:1:{i:0;i:0;}s:6:"remove";a:1:{i:0;i:1;}s:6:"change";a:1:{i:0;i:2;}s:4:"list";a:1:{i:0;i:3;}s:3:"map";a:0:{}s:4:"diff";a:0:{}}s:9:"parentKey";N;}}';

		/**
		 * @var Diff $diff
		 */
		$diff = unserialize( $v03serialization );

		$this->assertInstanceOf( '\Diff\Diff', $diff );
		$this->assertTrue( $diff->isAssociative() );
		$this->assertEquals( 4, count( $diff ) );
		$this->assertEquals( 1, count( $diff->getAdditions() ) );
		$this->assertEquals( 1, count( $diff->getRemovals() ) );
		$this->assertEquals( 1, count( $diff->getChanges() ) );
	}

	/**
	 * @since 0.6
	 *
	 * @param array $elements
	 *
	 * @return Diff
	 */
	protected function getNew( array $elements = array() ) {
		$class = $this->getInstanceClass();
		return new $class( $elements );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @since 0.6
	 *
	 * @param array $elements
	 */
	public function testConstructor( array $elements ) {
		$arrayObject = $this->getNew( $elements );

		$this->assertEquals( count( $elements ), $arrayObject->count() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 *
	 * @since 0.6
	 *
	 * @param array $elements
	 */
	public function testIsEmpty( array $elements ) {
		$arrayObject = $this->getNew( $elements );

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
	 * @param array $elements
	 */
	public function testAppend( array $elements ) {
		$list = $this->getNew();

		$listSize = count( $elements );

		foreach ( $elements as $element ) {
			$list->append( $element );
		}

		$this->assertEquals( $listSize, $list->count() );

		$list = $this->getNew();

		foreach ( $elements as $element ) {
			$list[] = $element;
		}

		$this->assertEquals( $listSize, $list->count() );

		$this->checkTypeChecks( function ( Diff $list, $element ) {
			$list->append( $element );
		} );
	}

	/**
	 * @since 0.6
	 *
	 * @param callback $function
	 */
	protected function checkTypeChecks( $function ) {
		$excption = null;
		$list = $this->getNew();


		foreach ( array( 42, 'foo', array(), new stdClass(), 4.2 ) as $element ) {
			$this->assertInvalidArgument( $function, $list, $element );
		}
	}

	/**
	 * Asserts that an InvalidArgumentException gets thrown when calling the provided
	 * callable. Extra arguments specified to the method are also provided to the callable.
	 *
	 * @since 0.6
	 *
	 * @param callable $function
	 */
	protected function assertInvalidArgument( $function ) {
		$this->setExpectedException( 'InvalidArgumentException' );

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
	 * @param array $elements
	 */
	public function testOffsetSet( array $elements ) {
		if ( $elements === array() ) {
			$this->assertTrue( true );
			return;
		}

		$list = $this->getNew();

		$element = reset( $elements );
		$list->offsetSet( 42, $element );
		$this->assertEquals( $element, $list->offsetGet( 42 ) );

		$list = $this->getNew();

		$element = reset( $elements );
		$list['oHai'] = $element;
		$this->assertEquals( $element, $list['oHai'] );

		$list = $this->getNew();

		$element = reset( $elements );
		$list->offsetSet( 9001, $element );
		$this->assertEquals( $element, $list[9001] );

		$list = $this->getNew();

		$element = reset( $elements );
		$list->offsetSet( null, $element );
		$this->assertEquals( $element, $list[0] );

		$list = $this->getNew();
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

		$this->assertEquals( $serialization, serialize( $copy ) );
		$this->assertEquals( count( $list ), count( $copy ) );

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
		$invalidDiffOp = $this->getMock( 'Diff\DiffOp' );

		$invalidDiffOp->expects( $this->atLeastOnce() )
			->method( 'getType' )
			->will( $this->returnValue( '~=[,,_,,]:3' ) );

		$this->setExpectedException( 'Exception' );

		$list->append( $invalidDiffOp );
	}

	/**
	 * @dataProvider invalidIsAssociativeProvider
	 */
	public function testConstructWithInvalidIsAssociative( $isAssociative ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new Diff( array(), $isAssociative );
	}

	public function invalidIsAssociativeProvider() {
		return $this->arrayWrap( array(
			1,
			'1',
			'null',
			0,
			array(),
			'foobar'
		) );
	}

	/**
	 * @dataProvider invalidDiffOpsProvider
	 */
	public function testConstructorWithInvalidDiffOps( array $diffOps ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new Diff( $diffOps );
	}

	public function invalidDiffOpsProvider() {
		$diffOpLists = array();

		$diffOpLists[] = array(
			'foo'
		);

		$diffOpLists[] = array(
			null
		);

		$diffOpLists[] = array(
			false,
			true,
			array()
		);

		$diffOpLists[] = array(
			new DiffOpAdd( 42 ),
			'in your list',
			new DiffOpAdd( 9001 ),
		);

		return $this->arrayWrap( $diffOpLists );
	}

}
	
