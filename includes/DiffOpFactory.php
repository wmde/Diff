<?php

namespace Diff;
use Diff\Exception;

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
	 * Returns an instance of DiffOp constructed from the provided array.
	 *
	 * This roundtripes with @see DiffOp::toArray.
	 *
	 * @since 0.5
	 *
	 * @param array $diffOp
	 *
	 * @return DiffOp
	 * @throws Exception
	 */
	public function newFromArray( array $diffOp ) {
		$this->assertHasKey( 'type', $diffOp );

		if ( $diffOp['type'] === 'add' ) {
			$this->assertHasKey( 'newvalue', $diffOp );
			return new DiffOpAdd( $diffOp['newvalue'] );
		}

		if ( $diffOp['type'] === 'remove' ) {
			$this->assertHasKey( 'oldvalue', $diffOp );
			return new DiffOpRemove( $diffOp['oldvalue'] );
		}

		if ( $diffOp['type'] === 'change' ) {
			$this->assertHasKey( 'newvalue', $diffOp );
			$this->assertHasKey( 'oldvalue', $diffOp );
			return new DiffOpChange( $diffOp['oldvalue'], $diffOp['newvalue'] );
		}

		if ( $diffOp['type'] === 'diff' ) {
			$this->assertHasKey( 'operations', $diffOp );
			$this->assertHasKey( 'isassoc', $diffOp );

			$operations = array();

			foreach ( $diffOp['operations'] as $operation ) {
				$operations[] = $this->newFromArray( $operation );
			}

			return new Diff( $operations, $diffOp['isassoc'] );
		}

		throw new Exception( 'Invalid array provided. Unknown type' );
	}

	/**
	 * @since 0.5
	 *
	 * @param mixed $key
	 * @param array $diffOp
	 *
	 * @throws Exception
	 */
	protected function assertHasKey( $key, array $diffOp ) {
		if ( !array_key_exists( $key, $diffOp ) ) {
			throw new Exception( 'Invalid array provided. Missing key "' . $key . '"' );
		}
	}

}
