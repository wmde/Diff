<?php

namespace Diff;

/**
 * Interface for objects that can diff two arrays to an array of DiffOp.
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
 * @since 0.4
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Differ {

	/**
	 * Takes two arrays, computes the diff, and returns this diff as an array of DiffOp.
	 *
	 * @since 0.4
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 *
	 * @throws Exception
	 * @return DiffOp[]
	 */
	public function doDiff( array $oldValues, array $newValues );

}
