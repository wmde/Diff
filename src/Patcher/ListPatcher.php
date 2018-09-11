<?php

declare( strict_types = 1 );

namespace Diff\Patcher;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;

/**
 * @since 0.4
 *
 * @license BSD-3-Clause
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
	 * Note that remove operations can introduce gaps into the input array $base.
	 * For instance, when the input is [ 0 => 'a', 1 => 'b', 2 => 'c' ], and there
	 * is one remove operation for 'b', the result will be [ 0 => 'a', 2 => 'c' ].
	 *
	 * @since 0.4
	 *
	 * @param array $base
	 * @param Diff $diff
	 *
	 * @return array
	 * @throws PatcherException
	 */
	public function patch( array $base, Diff $diff ): array {
		if ( $diff->looksAssociative() ) {
			$this->handleError( 'ListPatcher can only patch using non-associative diffs' );
		}

		foreach ( $diff as $diffOp ) {
			if ( $diffOp instanceof DiffOpAdd ) {
				$base[] = $diffOp->getNewValue();
			} elseif ( $diffOp instanceof DiffOpRemove ) {
				$needle = $diffOp->getOldValue();
				$key = array_search( $needle, $base, !is_object( $needle ) );

				if ( $key === false ) {
					$this->handleError( 'Cannot remove an element from a list if it is not present' );
					continue;
				}

				unset( $base[$key] );
			}
		}

		return $base;
	}

}
