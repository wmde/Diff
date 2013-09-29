<?php

namespace Diff;

use Exception;
use LogicException;

/**
 * Differ that does an associative diff between two arrays,
 * with the option to do this recursively.
 *
 * @since 0.4
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapDiffer implements Differ {

	/**
	 * @var boolean
	 */
	protected $recursively;

	/**
	 * @var Differ
	 */
	protected $listDiffer;

	/**
	 * @since 0.5
	 *
	 * @var callable|null
	 */
	protected $comparisonCallback = null;

	/**
	 * Constructor.
	 *
	 * @since 0.4
	 *
	 * @param bool $recursively
	 * @param Differ $listDiffer
	 */
	public function __construct( $recursively = false, Differ $listDiffer = null ) {
		$this->recursively = $recursively;

		if ( $listDiffer === null ) {
			$listDiffer = new ListDiffer();
		}

		$this->listDiffer = $listDiffer;
	}

	/**
	 * Sets a callback to use for comparison. The callback should accept two
	 * arguments.
	 *
	 * @since 0.5
	 *
	 * @param callable $comparisonCallback
	 */
	public function setComparisonCallback( $comparisonCallback ) {
		$this->comparisonCallback = $comparisonCallback;
	}

	/**
	 * @see Differ::doDiff
	 *
	 * Computes the diff between two associate arrays.
	 *
	 * @since 0.4
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 *
	 * @return DiffOp[]
	 * @throws \Exception
	 */
	public function doDiff( array $oldValues, array $newValues ) {
		$newSet = $this->arrayDiffAssoc( $newValues, $oldValues );
		$oldSet = $this->arrayDiffAssoc( $oldValues, $newValues );

		$diffSet = array();

		foreach ( $this->getAllKeys( $oldSet, $newSet ) as $key ) {
			$diffOp = $this->getDiffOpForElement( $key, $oldSet, $newSet );

			if ( $diffOp !== null ) {
				$diffSet[$key] = $diffOp;
			}
		}

		return $diffSet;
	}

	protected function getAllKeys( $oldSet, $newSet ) {
		return array_unique( array_merge(
			array_keys( $oldSet ),
			array_keys( $newSet )
		) );
	}

	protected function getDiffOpForElement( $key, array $oldSet, array $newSet ) {
		$hasOld = array_key_exists( $key, $oldSet );
		$hasNew = array_key_exists( $key, $newSet );

		if ( $this->recursively ) {
			$diffOp = $this->getDiffOpForElementRecursively( $key, $oldSet, $newSet );

			if ( $diffOp !== null ) {
				if ( $diffOp->isEmpty() ) {
					// there is no (relevant) difference
					return null;
				} else {
					return $diffOp;
				}
			}
		}

		if ( $hasOld && $hasNew ) {
			return new DiffOpChange( $oldSet[$key], $newSet[$key] );
		}
		elseif ( $hasOld ) {
			return new DiffOpRemove( $oldSet[$key] );
		}
		elseif ( $hasNew ) {
			return new DiffOpAdd( $newSet[$key] );
		}

		// @codeCoverageIgnoreStart
		throw new LogicException( 'The element needs to exist in either the old or new list to compare' );
		// @codeCoverageIgnoreEnd
	}

	protected function getDiffOpForElementRecursively( $key, array $oldSet, array $newSet ) {
		$old = array_key_exists( $key, $oldSet ) ? $oldSet[$key] : array();
		$new = array_key_exists( $key, $newSet ) ? $newSet[$key] : array();

		if ( is_array( $old ) && is_array( $new ) ) {
			$diff = $this->getDiffForArrays( $old, $new );
			return $diff;
		}

		return null;
	}

	protected function getDiffForArrays( array $old, array $new ) {
		if ( $this->isAssociative( $old ) || $this->isAssociative( $new ) ) {
			return new Diff( $this->doDiff( $old, $new ), true );
		}
		else {
			return new Diff( $this->listDiffer->doDiff( $old, $new ), false );
		}
	}

	/**
	 * Returns if an array is associative or not.
	 *
	 * @since 0.4
	 *
	 * @param array $array
	 *
	 * @return boolean
	 */
	protected function isAssociative( array $array ) {
		foreach ( $array as $key => $value ) {
			if ( is_string( $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Similar to the native array_diff_assoc function, except that it will
	 * spot differences between array values. Very weird the native
	 * function just ignores these...
	 *
	 * @see http://php.net/manual/en/function.array-diff-assoc.php
	 *
	 * @since 0.4
	 *
	 * @param array $from
	 * @param array $to
	 *
	 * @return array
	 */
	protected function arrayDiffAssoc( array $from, array $to ) {
		$diff = array();

		foreach ( $from as $key => $value ) {
			if ( !array_key_exists( $key, $to ) || !$this->valuesAreEqual( $to[$key], $value ) ) {
				$diff[$key] = $value;
			}
		}

		return $diff;
	}

	/**
	 * @since 0.5
	 *
	 * @param mixed $value0
	 * @param mixed $value1
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function valuesAreEqual( $value0, $value1 ) {
		if ( $this->comparisonCallback === null ) {
			return $value0 === $value1;
		}

		$areEqual = call_user_func_array( $this->comparisonCallback, array( $value0, $value1 ) );

		if ( !is_bool( $areEqual ) ) {
			throw new Exception( 'Comparison callback returned a non-boolean value' );
		}

		return $areEqual;
	}

}