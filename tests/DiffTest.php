<?php

namespace Diff\Test;
use Diff\Diff as Diff;
use Diff\IDiff as IDiff;
use Diff\IDiffOp as IDiffOp;
use Diff\MapDiff as MapDiff;
use Diff\ListDiff as ListDiff;
use Diff\DiffOpAdd as DiffOpAdd;
use Diff\DiffOpRemove as DiffOpRemove;
use Diff\DiffOpChange as DiffOpChange;

/**
 * Tests for the Diff\Diff class.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.1
 *
 * @ingroup Diff
 * @ingroup Test
 *
 * @group Diff
 * @group DiffTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffTest extends \GenericArrayObjectTest {

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
		$diff = new MapDiff( $operations );

		$additions = array();

		/**
		 * @var IDiffOp $operation
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
		$diff = new MapDiff( $operations );

		$removals = array();

		/**
		 * @var IDiffOp $operation
		 */
		foreach ( $operations as $operation ) {
			if ( $operation->getType() == 'remove' ) {
				$removals[] = $operation;
			}
		}

		$this->assertArrayEquals( $removals, $diff->getRemovals() );
	}

	public function testGetType() {
		$this->assertInternalType( 'string', Diff::newEmpty()->getType() );
	}

	public function testPreSetElement() {
		$pokemons = null;

		$diff = ListDiff::newEmpty();

		try {
			$diff[] = new DiffOpChange( 0 ,1 );
		}
		catch( \Exception $pokemons ) {}

		$this->assertInstanceOf( '\Exception', $pokemons );
	}

	public function hasParentKeyProvider() {
		return array(
			array( 'foo' ),
			array( 42 ),
			array( null ),
			array(),
		);
	}

	/**
	 * @dataProvider hasParentKeyProvider
	 */
	public function testHasParentKey() {
		$args = func_get_args();

		$diff = array_key_exists( 0, $args ) ? Diff::newEmpty( $args[0] ) : Diff::newEmpty();

		$this->assertEquals(
			array_key_exists( 0, $args ) && $args[0] !== null,
			$diff->hasParentKey()
		);
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testAddOperations( array $operations ) {
		$diff = Diff::newEmpty();

		$diff->addOperations( $operations );

		$this->assertArrayEquals( $operations, $diff->getOperations() );
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testStuff( array $operations ) {
		$diff = new Diff( $operations );

		$this->assertInstanceOf( '\Diff\IDiff', $diff );
		$this->assertInstanceOf( '\ArrayObject', $diff );

		$types = array();

		foreach ( $diff as $operation ) {
			$this->assertInstanceOf( '\Diff\IDiffOp', $operation );
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

	public function getApplicableDiffProvider() {
		// Diff, current object, expected
		$argLists = array();

		$diff = Diff::newEmpty( 42 );
		$currentObject = array();
		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = MapDiff::newEmpty();
		$currentObject = array( 'foo' => 0, 'bar' => 1 );
		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		) );
		$currentObject = array( 'foo' => 0, 'bar' => 1 );
		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		) );
		$currentObject = array();
		$expected = MapDiff::newEmpty();

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		) );
		$currentObject = array( 'foo' => 'something else', 'bar' => 1, 'baz' => 'o_O' );
		$expected = new MapDiff( array(
			'bar' => new DiffOpChange( 1, 9001 ),
		) );

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'bar' => new DiffOpRemove( 9001 ),
		) );
		$currentObject = array();
		$expected = MapDiff::newEmpty();

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		) );
		$currentObject = array( 'foo' => 'bar' );
		$expected = MapDiff::newEmpty();

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		) );
		$currentObject = array( 'foo' => 42, 'bar' => 9001 );
		$expected = new MapDiff( array(
			'bar' => new DiffOpRemove( 9001 ),
		) );

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		) );
		$currentObject = array();
		$expected = new MapDiff( array(
			'foo' => new DiffOpAdd( 42 ),
		) );

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new ListDiff( array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		) );
		$currentObject = array();
		$expected = new MapDiff( array(
			new DiffOpAdd( 42 ),
		) );

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new ListDiff( array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		) );
		$currentObject = array( 1, 42, 9001 );
		$expected = new MapDiff( array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		) );

		$argLists[] = array( $diff, $currentObject, $expected );

		$diff = new MapDiff( array(
			'foo' => new MapDiff( array( 'bar' => new DiffOpChange( 0, 1 ) ) ),
			'le-non-existing-element' => new MapDiff( array( 'bar' => new DiffOpChange( 0, 1 ) ) ),
			'spam' => new ListDiff( array( new DiffOpAdd( 42 ), new DiffOpAdd( 23 ), new DiffOpRemove( 'ohi' ), new DiffOpRemove( 'doom' ) ) ),
			new DiffOpAdd( 9001 ),
		) );
		$currentObject = array(
			'foo' => array( 'bar' => 0, 'baz' => 'O_o' ),
			'spam' => array( 23, 'ohi' )
		);
		$expected = new MapDiff( array(
			'foo' => new MapDiff( array( 'bar' => new DiffOpChange( 0, 1 ) ) ),
			'spam' => new ListDiff( array( new DiffOpAdd( 42 ), new DiffOpAdd( 23 ), new DiffOpRemove( 'ohi' ) ) ),
			new DiffOpAdd( 9001 ),
		) );

		$argLists[] = array( $diff, $currentObject, $expected );

		return $argLists;
	}

	/**
	 * @dataProvider getApplicableDiffProvider
	 *
	 * @param \Diff\IDiff $diff
	 * @param array $currentObject
	 * @param \Diff\IDiff $expected
	 */
	public function testGetApplicableDiff( IDiff $diff, array $currentObject, IDiff $expected ) {
		$actual = $diff->getApplicableDiff( $currentObject );

		$this->assertEquals( $expected->getOperations(), $actual->getOperations() );
		$this->assertEquals( $expected->getParentKey(), $actual->getParentKey() );
	}

	public function testNewEmpty() {
		$diff = Diff::newEmpty();

		$this->assertTrue( $diff->isEmpty() );
		$this->assertEquals( null, $diff->getParentKey() );

		$diff = Diff::newEmpty( '~=[,,_,,]:3' );

		$this->assertTrue( $diff->isEmpty() );
		$this->assertEquals( '~=[,,_,,]:3', $diff->getParentKey() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetOperations( Diff $diff ) {
		$ops = $diff->getOperations();

		$this->assertInternalType( 'array', $ops );

		foreach ( $ops as $diffOp ) {
			$this->assertInstanceOf( '\Diff\IDiffOp', $diffOp );
		}

		$this->assertArrayEquals( $ops, $diff->getOperations() );
	}

	public function testRemoveEmptyOperations() {
		$diff = new Diff( array() );

		$diff['foo'] = new DiffOpAdd( 1 );
		$diff['bar'] = new MapDiff( array( new DiffOpAdd( 1 ) ) );
		$diff['baz'] = new ListDiff( array( new DiffOpAdd( 1 ) ) );
		$diff['bah'] = new ListDiff( array() );
		$diff['spam'] = new MapDiff( array() );

		$diff->removeEmptyOperations();

		$this->assertTrue( $diff->offsetExists( 'foo' ) );
		$this->assertTrue( $diff->offsetExists( 'bar' ) );
		$this->assertTrue( $diff->offsetExists( 'baz' ) );
		$this->assertFalse( $diff->offsetExists( 'bah' ) );
		$this->assertFalse( $diff->offsetExists( 'spam' ) );
	}

}
	
