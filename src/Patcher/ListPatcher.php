<?php

namespace Diff\Patcher;

use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;

/**
 * @since 0.4
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
			}
		}

		return $base;
	}

}
