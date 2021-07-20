<?php

declare( strict_types = 1 );

namespace Diff;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use InvalidArgumentException;

/**
 * Constructs a DiffOp from its array form (which can be obtained via @see DiffOp::toArray).
 *
 * @since 0.5
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
class DiffOpFactory {

	/**
	 * @var callable|null
	 */
	private $valueConverter;

	/**
	 * @since 0.5
	 *
	 * @param callable|null $valueConverter optional callback used to convert special
	 *        array structures into objects used as values in atomic diff ops.
	 */
	public function __construct( $valueConverter = null ) {
		$this->valueConverter = $valueConverter;
	}

	/**
	 * Returns an instance of DiffOp constructed from the provided array.
	 *
	 * This roundtrips with @see DiffOp::toArray.
	 *
	 * @since 0.5
	 *
	 * @param array $diffOp
	 *
	 * @return DiffOp\DiffOp
	 * @throws InvalidArgumentException
	 */
	public function newFromArray( array $diffOp ) {
		$this->assertHasKey( 'type', $diffOp );

		if ( $diffOp['type'] === 'add' ) {
			$this->assertHasKey( 'newvalue', $diffOp );
			return new DiffOpAdd( $this->arrayToObject( $diffOp['newvalue'] ) );
		}

		if ( $diffOp['type'] === 'remove' ) {
			$this->assertHasKey( 'oldvalue', $diffOp );
			return new DiffOpRemove( $this->arrayToObject( $diffOp['oldvalue'] ) );
		}

		if ( $diffOp['type'] === 'change' ) {
			$this->assertHasKey( 'newvalue', $diffOp );
			$this->assertHasKey( 'oldvalue', $diffOp );
			return new DiffOpChange(
				$this->arrayToObject( $diffOp['oldvalue'] ),
				$this->arrayToObject( $diffOp['newvalue'] ) );
		}

		if ( $diffOp['type'] === 'diff' ) {
			$this->assertHasKey( 'operations', $diffOp );
			$this->assertHasKey( 'isassoc', $diffOp );

			$operations = [];

			foreach ( $diffOp['operations'] as $key => $operation ) {
				$operations[$key] = $this->newFromArray( $operation );
			}

			return new Diff( $operations, $diffOp['isassoc'] );
		}

		throw new InvalidArgumentException( 'Invalid array provided. Unknown type' );
	}

	/**
	 * @since 0.5
	 *
	 * @param string $key
	 * @param array $diffOp
	 *
	 * @throws InvalidArgumentException
	 */
	protected function assertHasKey( $key, array $diffOp ) {
		if ( !array_key_exists( $key, $diffOp ) ) {
			throw new InvalidArgumentException( 'Invalid array provided. Missing key "' . $key . '"' );
		}
	}

	/**
	 * Converts an array structure to an object using the value converter callback function
	 * provided to the constructor, if any.
	 *
	 * If the convert callback is null or the value is not an array, the value is returned
	 * unchanged. The Converter callback is intended for constructing an object from an array,
	 * but may also just leave the value unchanged if it cannot handle it.
	 *
	 * @param mixed $value The value to convert
	 *
	 * @return mixed The $value unchanged, or the return value of calling the
	 *         value converter callback on $value.
	 */
	private function arrayToObject( $value ) {
		if ( $this->valueConverter !== null && is_array( $value ) ) {
			$value = call_user_func( $this->valueConverter, $value );
		}

		return $value;
	}

}
