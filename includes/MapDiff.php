<?php

namespace Diff;

/**
 * Class representing the diff between to (associative) arrays.
 * Since items are identified by keys, it's possible to do meaningful
 * recursive diffs. So the IDiffOp objects contained by this MapDiff can
 * be containers such as MapDiff and ListDiff themselves.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapDiff extends Diff {

	/**
	 * Creates and returns an empty MapDiff.
	 *
	 * @since 0.1
	 *
	 * @param $parentKey = null
	 *
	 * @return MapDiff
	 */
	public static function newEmpty( $parentKey = null ) {
		return new self( array(), $parentKey );
	}

	/**
	 * Creates a new MapDiff given two arrays.
	 *
	 * @since 0.1
	 *
	 * @param array $oldValues
	 * @param array $newValues
	 * @param boolean $recursively
	 *
	 * @return MapDiff
	 */
	public static function newFromArrays( array $oldValues, array $newValues, $recursively = false ) {
		return new self( static::doDiff( $oldValues, $newValues, $recursively ) );
	}

	/**
	 * Computes the diff between two associate arrays.
	 *
	 * @since 0.1
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 * @param boolean $recursively If elements that are arrays should also be diffed.
	 *
	 * @throws \Diff\Exception
	 * @return array
	 * Each key existing in either array will exist in the result and have an array as value.
	 * This value is an array with two keys: old and new.
	 * Example:
	 * array(
	 * 'en' => array( 'old' => 'Foo', 'new' => 'Foobar' ),
	 * 'de' => array( 'old' => 42, 'new' => 9001 ),
	 * )
	 */
	public static function doDiff( array $oldValues, array $newValues, $recursively = false ) {
		$newSet = static::array_diff_assoc( $newValues, $oldValues );
		$oldSet = static::array_diff_assoc( $oldValues, $newValues );

		$diffSet = array();

		foreach ( array_merge( array_keys( $oldSet ), array_keys( $newSet ) ) as $key ) {
			$hasOld = array_key_exists( $key, $oldSet );
			$hasNew = array_key_exists( $key, $newSet );

			if ( $recursively ) {
				if ( ( !$hasOld || is_array( $oldSet[$key] ) ) && ( !$hasNew || is_array( $newSet[$key] ) ) ) {

					$old = $hasOld ? $oldSet[$key] : array();
					$new = $hasNew ? $newSet[$key] : array();

					if ( static::isAssociative( $old ) || static::isAssociative( $new ) ) {
						$diff = static::newFromArrays( $old, $new );
					}
					else {
						$diff = ListDiff::newFromArrays( $old, $new );
					}

					if ( !$diff->isEmpty() ) {
						$diffSet[$key] = $diff;
					}

					continue;
				}
			}

			if ( $hasOld && $hasNew ) {
				$diffSet[$key] = new DiffOpChange( $oldSet[$key], $newSet[$key] );
			}
			elseif ( $hasOld ) {
				$diffSet[$key] = new DiffOpRemove( $oldSet[$key] );
			}
			elseif ( $hasNew ) {
				$diffSet[$key] = new DiffOpAdd( $newSet[$key] );
			}
			else {
				throw new Exception( 'Cannot create a diff op for two empty values.' );
			}
		}

		return $diffSet;
	}

	/**
	 * Returns if an array is associative or not.
	 *
	 * @since 0.1
	 *
	 * @param array $array
	 *
	 * @return boolean
	 */
	protected static function isAssociative( array $array ) {
		return $array !== array() && array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}

	/**
	 * Similar to the native array_diff_assoc function, except that it will
	 * spot differences between array values. Very weird the native
	 * function just ignores these...
	 *
	 * @see http://php.net/manual/en/function.array-diff-assoc.php
	 *
	 * @since 0.1
	 *
	 * @param array $from
	 * @param array $to
	 *
	 * @return array
	 */
	protected static function array_diff_assoc( array $from, array $to ) {
		$diff = array();

		foreach ( $from as $key => $value ) {
			if ( !array_key_exists( $key, $to ) || $to[$key] !== $value ) {
				$diff[$key] = $value;
			}
		}

		return $diff;
	}

	/**
	 * @since 0.1
	 * @return array
	 */
	public function getChanges() {
		return $this->getTypeOperations( 'change' );
	}

	/**
	 * @see IDiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'map';
	}

}