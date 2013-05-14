<?php

namespace Diff\ArrayComparer;

/**
 * Strict variant of PHPs array_diff method.
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
 * @since 0.7
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictArrayComparer implements ArrayComparer {

	/**
	 * @see ArrayComparer::diffArrays
	 *
	 * Similar to @see array_diff with the following differences:
	 *
	 * - Strict comparison for arrays: ['42'] and [42] are different
	 * - Quantity matters: [42, 42] and [42] are different
	 * - Arrays and objects are compared properly: [[1]] and [[2]] are different
	 * - Naive support for objects by using non-strict comparison
	 * - Only works with two arrays (array_diff can take more)
	 *
	 * @since 0.7
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function diffArrays( array $arrayOne, array $arrayTwo ) {
		$notInTwo = array();

		foreach ( $arrayOne as $element ) {
			$location = array_search( $element, $arrayTwo, !is_object( $element ) );

			if ( $location === false ) {
				$notInTwo[] = $element;
				continue;
			}

			unset( $arrayTwo[$location] );
		}

		return $notInTwo;
	}

}
