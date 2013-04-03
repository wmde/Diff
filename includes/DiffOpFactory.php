<?php

namespace Diff;

use InvalidArgumentException;

/**
 * Factory for constructing DiffOp objects.
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
 * @since 0.5
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpFactory {

	/**
	 * @var callable|null
	 */
	protected $valueConverter;

	/**
	 * Constructor.
	 *
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
	 * This roundtripes with @see DiffOp::toArray.
	 *
	 * @since 0.5
	 *
	 * @param array $diffOp
	 *
	 * @return DiffOp
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

			$operations = array();

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
	 * @param mixed $key
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
	 * @since 0.5
	 *
	 * @param mixed $value The value to convert
	 *
	 * @return mixed The $value unchanged, or the return value of calling the
	 *         value converter callback on $value.
	 */
	protected function arrayToObject( $value ) {
		if ( $this->valueConverter !== null && is_array( $value ) ) {
			$value = call_user_func( $this->valueConverter, $value );
		}

		return $value;
	}

}
