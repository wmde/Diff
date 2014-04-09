<?php

namespace Diff\Patcher;

use Diff\Comparer\StrictComparer;
use Diff\Comparer\ValueComparer;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;

/**
 * Map patcher.
 *
 * @since 0.4
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapPatcher extends ThrowingPatcher {

	/**
	 * @since 0.4
	 *
	 * @var Patcher
	 */
	protected $listPatcher;

	/**
	 * @since 0.6
	 *
	 * @var ValueComparer|null
	 */
	protected $comparer = null;

	/**
	 * @since 0.4
	 *
	 * @param bool $throwErrors
	 * @param Patcher|null $listPatcher The patcher that will be used for lists in the value
	 */
	public function __construct( $throwErrors = false, Patcher $listPatcher = null ) {
		parent::__construct( $throwErrors );

		if ( $listPatcher === null ) {
			$listPatcher = new ListPatcher( $throwErrors );
		}

		$this->listPatcher = $listPatcher;
	}

	/**
	 * @see Patcher::patch
	 *
	 * Applies the provided diff to the provided array and returns the result.
	 * The array is treated as a map, ie keys are held into account.
	 *
	 * It is possible to pass in non-associative diffs (those for which isAssociative)
	 * returns false, however the likely intended behavior can be obtained via
	 * a list patcher.
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
		/**
		 * @var DiffOp $diffOp
		 */
		foreach ( $diff as $key => $diffOp ) {
			if ( $diffOp instanceof DiffOpAdd ) {
				if ( array_key_exists( $key, $base ) ) {
					$this->handleError( 'Cannot add an element already present in a map' );
					continue;
				}

				$base[$key] = $diffOp->getNewValue();
			}
			else if ( $diffOp instanceof Diff ) {
				if ( !array_key_exists( $key, $base )
				&& ( $diffOp->getChanges() !== array() || $diffOp->getRemovals() !== array() )
				) {
					$this->handleError( 'Cannot apply a diff with non-add operations to an element not present in a map' );
					continue;
				}

				if ( !array_key_exists( $key, $base ) ) {
					$base[$key] = array();
				}

				$base[$key] = $this->patchMapOrList( $base[$key], $diffOp );
			}
			else if ( $diffOp instanceof DiffOpRemove ) {
				if ( !array_key_exists( $key, $base ) ) {
					$this->handleError( 'Cannot do a non-add operation with an element not present in a map' );
					continue;
				}

				if ( !$this->valuesAreEqual( $base[$key], $diffOp->getOldValue() ) ) {
					$this->handleError( 'Tried removing a map value that mismatches the current value' );
					continue;
				}

				unset( $base[$key] );
			}
			else if ( $diffOp instanceof DiffOpChange ) {
				if ( !array_key_exists( $key, $base ) ) {
					$this->handleError( 'Cannot do a non-add operation with an element not present in a map' );
					continue;
				}

				if ( !$this->valuesAreEqual( $base[$key], $diffOp->getOldValue() ) ) {
					$this->handleError( 'Tried changing a map value from an invalid source value' );
					continue;
				}

				$base[$key] = $diffOp->getNewValue();
			}
			else {
				$this->handleError( 'Unknown diff operation cannot be applied to map element' );
			}
		}

		return $base;
	}

	protected function patchMapOrList( array $base, Diff $diff ) {
		if ( $diff->looksAssociative() ) {
			$base = $this->patch( $base, $diff );
		}
		else {
			$base = $this->listPatcher->patch( $base, $diff );
		}

		return $base;
	}

	protected function valuesAreEqual( $firstValue, $secondValue ) {
		if ( $this->comparer === null ) {
			$this->comparer = new StrictComparer();
		}

		return $this->comparer->valuesAreEqual( $firstValue, $secondValue );
	}

	/**
	 * Sets the value comparer that should be used to determine if values are equal.
	 *
	 * @since 0.6
	 *
	 * @param ValueComparer $comparer
	 */
	public function setValueComparer( ValueComparer $comparer ) {
		$this->comparer = $comparer;
	}

}
