<?php

namespace Diff;

/**
 * Differ that only looks at the values of the arrays (and thus ignores key differences).
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
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffer implements Differ {

	/**
	 * Use non-strict comparison and do not care about quantity.
	 * This makes use of @see array_diff
	 */
	const MODE_NATIVE = 0;

	/**
	 * Use strict comparison and care about quantity.
	 * This makes use of @see ListDiffer::arrayDiff
	 */
	const MODE_STRICT = 1;

	/**
	 * @since 0.1
	 *
	 * @var int
	 */
	protected $diffMode;

	/**
	 * Constructor.
	 *
	 * Takes an argument that determines the diff mode.
	 * By default this is ListDiffer::MODE_STRICT, which causes
	 * computation in @see doDiff to be done via @see arrayDiff.
	 * If the native behaviour is preferred, ListDiffer::MODE_NATIVE
	 * can be specified.
	 *
	 * @since 0.1
	 *
	 * @param int $diffMode
	 */
	public function __construct( $diffMode = self::MODE_STRICT ) {
		$this->diffMode = $diffMode;
	}

	/**
	 * @see Differ::doDiff
	 *
	 * @since 0.1
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 *
	 * @throws Exception
	 * @return DiffOp[]
	 */
	public function doDiff( array $oldValues, array $newValues ) {
		$operations = array();

		foreach ( $this->diffArrays( $newValues, $oldValues ) as $addition ) {
			$operations[] = new DiffOpAdd( $addition );
		}

		foreach ( $this->diffArrays( $oldValues, $newValues ) as $removal ) {
			$operations[] = new DiffOpRemove( $removal );
		}

		return $operations;
	}

	/**
	 * @since 0.1
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	protected function diffArrays( array $arrayOne, array $arrayTwo ) {
		if ( $this->diffMode === self::MODE_STRICT ) {
			return $this->arrayDiff( $arrayOne, $arrayTwo );
		}
		else {
			return array_diff( $arrayOne, $arrayTwo );
		}
	}

	/**
	 * Returns an array containing all the entries from arrayOne that are not present in arrayTwo.
	 *
	 * Similar to @see array_diff with the following differences:
	 *
	 * - Strict comparison: ['42'] and [42] are different
	 * - Quantity matters: [42, 42] and [42] are different
	 * - Arrays and objects are compared properly: [[1]] and [[2]] are different
	 * - Only works with two arrays (array_diff can take more)
	 *
	 * @since 0.1
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function arrayDiff( array $arrayOne, array $arrayTwo ) {
		$notInTwo = array();

		foreach ( $arrayOne as $element ) {
			if ( !in_array( $element, $arrayTwo, true ) ) {
				$notInTwo[] = $element;
				continue;
			}

			$location = array_search( $element, $arrayTwo, true );
			assert( $location !== false );

			unset( $arrayTwo[$location] );
		}

		return $notInTwo;
	}

}