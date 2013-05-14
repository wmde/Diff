<?php

namespace Diff\Merger;

use Diff\Diff;

/**
 * Can merge multiple diffs together.
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
class MultipleDiffMerger {

	protected $listMergingStrategy;
	protected $mapMergingStrategy;

	public function __construct( DiffMerger $listMergingStrategy, DiffMerger $mapMergingStrategy ) {
		$this->listMergingStrategy = $listMergingStrategy;
		$this->mapMergingStrategy = $mapMergingStrategy;
	}

	public function merge() {
		$diffs = func_get_args();

		if ( empty( $diffs ) ) {
			return new Diff();
		}

		$this->validateInput( $diffs );

		$mergedDiff = new Diff();

		foreach ( $diffs as $diff ) {
			$mergedDiff = $this->mergeDiffs( $mergedDiff, $diff );
		}

		return $mergedDiff;
	}

	protected function validateInput( array $arguments ) {
		foreach ( $arguments as $argument ) {
			if ( !( $argument instanceof Diff ) ) {
				throw new \InvalidArgumentException(
					'The merge method only accepts Diff\Diff objects as arguments'
				);
			}
		}
	}

	protected function mergeDiffs( Diff $firstDiff, Diff $secondDiff ) {
		if ( $firstDiff->isAssociative() !== $secondDiff->isAssociative() ) {
			throw new \InvalidArgumentException(
				'Cannot merge an associative diff with a non-associative diff'
			);
		}

		if ( $firstDiff->isAssociative() ) {
			throw new \Exception( 'not implemented yet' );
		}
		else {
			return $this->listMergingStrategy->merge( $firstDiff, $secondDiff );
		}
	}

}
