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
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapPatcher extends ThrowingPatcher {

	/**
	 * @var Patcher
	 */
	private $listPatcher;

	/**
	 * @var ValueComparer|null
	 */
	private $comparer = null;

	/**
	 * @since 0.4
	 *
	 * @param bool $throwErrors
	 * @param Patcher|null $listPatcher The patcher that will be used for lists in the value
	 */
	public function __construct( $throwErrors = false, Patcher $listPatcher = null ) {
		parent::__construct( $throwErrors );

		$this->listPatcher = $listPatcher ?: new ListPatcher( $throwErrors );
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
		foreach ( $diff as $key => $diffOp ) {
			$this->applyOperation( $base, $key, $diffOp );
		}

		return $base;
	}

	/**
	 * @param array &$base
	 * @param int|string $key
	 * @param DiffOp $diffOp
	 *
	 * @throws PatcherException
	 */
	private function applyOperation( &$base, $key, DiffOp $diffOp ) {
		if ( $diffOp instanceof DiffOpAdd ) {
			$this->applyDiffOpAdd( $base, $key, $diffOp );
		}
		elseif ( $diffOp instanceof DiffOpChange ) {
			$this->applyDiffOpChange( $base, $key, $diffOp );
		}
		elseif ( $diffOp instanceof DiffOpRemove ) {
			$this->applyDiffOpRemove( $base, $key, $diffOp );
		}
		elseif ( $diffOp instanceof Diff ) {
			$this->applyDiff( $base, $key, $diffOp );
		}
		else {
			$this->handleError( 'Unknown diff operation cannot be applied to map element' );
		}
	}

	/**
	 * @param array &$base
	 * @param int|string $key
	 * @param DiffOpAdd $diffOp
	 *
	 * @throws PatcherException
	 */
	private function applyDiffOpAdd( &$base, $key, DiffOpAdd $diffOp ) {
		if ( array_key_exists( $key, $base ) ) {
			$this->handleError( 'Cannot add an element already present in a map' );
			return;
		}

		$base[$key] = $diffOp->getNewValue();
	}

	/**
	 * @param array &$base
	 * @param int|string $key
	 * @param DiffOpRemove $diffOp
	 *
	 * @throws PatcherException
	 */
	private function applyDiffOpRemove( &$base, $key, DiffOpRemove $diffOp ) {
		if ( !array_key_exists( $key, $base ) ) {
			$this->handleError( 'Cannot do a non-add operation with an element not present in a map' );
			return;
		}

		if ( !$this->valuesAreEqual( $base[$key], $diffOp->getOldValue() ) ) {
			$this->handleError( 'Tried removing a map value that mismatches the current value' );
			return;
		}

		unset( $base[$key] );
	}

	/**
	 * @param array &$base
	 * @param int|string $key
	 * @param DiffOpChange $diffOp
	 *
	 * @throws PatcherException
	 */
	private function applyDiffOpChange( &$base, $key, DiffOpChange $diffOp ) {
		if ( !array_key_exists( $key, $base ) ) {
			$this->handleError( 'Cannot do a non-add operation with an element not present in a map' );
			return;
		}

		if ( !$this->valuesAreEqual( $base[$key], $diffOp->getOldValue() ) ) {
			$this->handleError( 'Tried changing a map value from an invalid source value' );
			return;
		}

		$base[$key] = $diffOp->getNewValue();
	}

	/**
	 * @param array &$base
	 * @param int|string $key
	 * @param Diff $diffOp
	 *
	 * @throws PatcherException
	 */
	private function applyDiff( &$base, $key, Diff $diffOp ) {
		if ( $this->isAttemptToModifyNotExistingElement( $base, $key, $diffOp ) ) {
			$this->handleError( 'Cannot apply a diff with non-add operations to an element not present in a map' );
			return;
		}

		if ( !array_key_exists( $key, $base ) ) {
			$base[$key] = array();
		}

		$base[$key] = $this->patchMapOrList( $base[$key], $diffOp );
	}

	/**
	 * @param array &$base
	 * @param int|string $key
	 * @param Diff $diffOp
	 *
	 * @return bool
	 */
	private function isAttemptToModifyNotExistingElement( $base, $key, Diff $diffOp ) {
		return !array_key_exists( $key, $base )
			&& ( $diffOp->getChanges() !== array() || $diffOp->getRemovals() !== array() );
	}

	/**
	 * @param array $base
	 * @param Diff $diff
	 *
	 * @return array
	 */
	private function patchMapOrList( array $base, Diff $diff ) {
		if ( $diff->looksAssociative() ) {
			$base = $this->patch( $base, $diff );
		}
		else {
			$base = $this->listPatcher->patch( $base, $diff );
		}

		return $base;
	}

	/**
	 * @param mixed $firstValue
	 * @param mixed $secondValue
	 *
	 * @return bool
	 */
	private function valuesAreEqual( $firstValue, $secondValue ) {
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
