<?php

namespace Diff\Comparer;

/**
 * Adapter around a comparision callback that implements the ValueComparer
 * interface.
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
 * @since 0.6
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackComparer implements ValueComparer {

	private $callback;

	/**
	 * @since 0.6
	 *
	 * @param callable $callback
	 */
	public function __construct( $callback ) {
		$this->callback = $callback;
	}

	public function valuesAreEqual( $firstValue, $secondValue ) {
		return call_user_func_array( $this->callback, array( $firstValue, $secondValue ) );
	}

}
