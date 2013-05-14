<?php

namespace Diff\Merger;

use Diff\Diff;
use Diff\ListDiffer;

/**
 * Note: this class has not been fully implemented yet.
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
class ListDiffMerger implements DiffMerger {

	public function merge( Diff $firstDiff, Diff $secondDiff ) {
		$firstDiff = clone $firstDiff;

		foreach ( $secondDiff as $diffOp ) {
			$firstDiff[] = $diffOp;
		}

		return $firstDiff;
	}

//	protected function getDiffWithoutRemovedAdditions( Diff $diff ) {
//		$simplifiedDiff = new Diff();
//
//		array_diff( $diff->getAddedValues(), $diff->getRemovedValues() );
//
//		foreach (  ) {
//
//		}
//
//		return $simplifiedDiff;
//	}

}
