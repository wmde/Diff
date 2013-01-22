<?php

namespace Diff;

/**
 * List patcher.
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
class ListPatcher extends ThrowingPatcher {

	/**
	 * @see Patcher::patch
	 *
	 * Applies the provided diff to the provided array and returns the result.
	 * The provided diff needs to be non-associative. In other words, calling
	 * isAssociative on it should return false.
	 *
	 * @since 0.4
	 *
	 * @param array $base
	 * @param Diff $diff
	 *
	 * @return array
	 */
	public function patch( array $base, Diff $diff ) {
		if ( $this->throwErrors && $diff->looksAssociative() ) {
			$this->handleError( 'ListPatcher can only patch using non-associative diffs' );
		}

		/**
		 * @var DiffOp $diffOp
		 */
		foreach ( $diff as $diffOp ) {
			switch ( true ) {
				case $diffOp instanceof DiffOpAdd:
					$base[] = $diffOp->getNewValue();
					break;
				case $diffOp instanceof DiffOpRemove:
					$key = array_search( $diffOp->getOldValue(), $base, true );

					if ( $key === false ) {
						$this->handleError( 'Cannot remove an element from a list if it is not present' );
						continue;
					}

					unset( $base[$key] );
					break;
				default:
					$this->handleError( 'Non-add and non-remove diff operation cannot be applied to a list' );
			}
		}

		return $base;
	}

	/**
	 * @see Patcher::getApplicableDiff
	 *
	 * @since 0.4
	 *
	 * @param array $base
	 * @param Diff $diff
	 *
	 * @return Diff
	 */
	public function getApplicableDiff( array $base, Diff $diff ) {
		$throwErrors = $this->throwErrors;
		$this->throwErrors = false;

		$patched = $this->patch( $base, $diff );

		$this->throwErrors = $throwErrors;

		$differ = new ListDiffer();
		return new Diff( $differ->doDiff( $base, $patched ) );
	}

}