<?php

namespace Diff;

/**
 * Interface for patchers that can, given a base and a diff, provide
 * the difference between the base and the result once the diff is
 * provided to it. This difference can differ from the diff since
 * some operations might not be applicable to the base.
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
interface PreviewablePatcher extends Patcher {

	/**
	 * Returns the operations that can be applied to the base.
	 * The returned operations are thus the difference between
	 * the result of @see patch and it's input base value.
	 *
	 * @since 0.1
	 *
	 * @param array $base
	 * @param Diff $diffOps
	 *
	 * @return Diff
	 */
	public function getApplicableDiff( array $base, Diff $diffOps );

}
