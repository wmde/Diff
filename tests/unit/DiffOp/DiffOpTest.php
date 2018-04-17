<?php

declare( strict_types = 1 );

namespace Diff\Tests\DiffOp;

use Diff\DiffOp\DiffOp;
use Diff\Tests\DiffTestCase;
use ReflectionClass;

/**
 * Base test class for the Diff\DiffOp\DiffOp deriving classes.
 *
 * @group Diff
 * @group DiffOp
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
abstract class DiffOpTest extends DiffTestCase {

	/**
	 * Returns the name of the concrete class tested by this test.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public abstract function getClass();

	/**
	 * First element can be a boolean indication if the successive values are valid,
	 * or a string indicating the type of exception that should be thrown (ie not valid either).
	 *
	 * @since 0.1
	 */
	public abstract function constructorProvider();

	/**
	 * Creates and returns a new instance of the concrete class.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function newInstance() {
		$reflector = new ReflectionClass( $this->getClass() );
		return $reflector->newInstanceArgs( func_get_args() );
	}

	/**
	 * @since 0.1
	 *
	 * @return array[] An array of arrays, each containing an instance and an array of constructor
	 * arguments used to construct the instance.
	 */
	public function instanceProvider() {
		$self = $this;

		return array_filter( array_map(
			function( array $args ) use ( $self ) {
				$isValid = array_shift( $args ) === true;

				if ( !$isValid ) {
					return false;
				}

				return array( call_user_func_array( array( $self, 'newInstance' ), $args ), $args );
			},
			$this->constructorProvider()
		), 'is_array' );
	}

	/**
	 * @dataProvider constructorProvider
	 *
	 * @since 0.1
	 */
	public function testConstructor() {
		$args = func_get_args();
		$valid = array_shift( $args );

		if ( $valid !== true ) {
			$this->expectException( $valid ?: 'InvalidArgumentException' );
		}

		$dataItem = call_user_func_array( array( $this, 'newInstance' ), $args );
		$this->assertInstanceOf( $this->getClass(), $dataItem );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testIsAtomic( DiffOp $diffOp ) {
		$this->assertInternalType( 'boolean', $diffOp->isAtomic() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetType( DiffOp $diffOp ) {
		$this->assertInternalType( 'string', $diffOp->getType() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSerialization( DiffOp $diffOp ) {
		$serialization = serialize( $diffOp );
		$unserialization = unserialize( $serialization );
		$this->assertEquals( $diffOp, $unserialization );
		$this->assertEquals( serialize( $diffOp ), serialize( $unserialization ) );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testCount( DiffOp $diffOp ) {
		if ( $diffOp->isAtomic() ) {
			$this->assertSame( 1, $diffOp->count() );
		}
		else {
			$count = 0;

			/**
			 * @var DiffOp $childOp
			 */
			foreach ( $diffOp as $childOp ) {
				$count += $childOp->count();
			}

			$this->assertSame( $count, $diffOp->count() );
		}
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArray( DiffOp $diffOp ) {
		$array = $diffOp->toArray();

		$this->assertInternalType( 'array', $array );
		$this->assertArrayHasKey( 'type', $array );
		$this->assertInternalType( 'string', $array['type'] );
		$this->assertEquals( $diffOp->getType(), $array['type'] );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayWithConversion( DiffOp $diffOp ) {
		$array = $diffOp->toArray( function() {
			return array( 'Nyan!' );
		} );

		$this->assertInternalType( 'array', $array );
	}

}
