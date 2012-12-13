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
	 * @deprecated since 0.4, just use the constructor
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
	 * @deprecated since 0.4, use MapDiffer::doDiff instead
	 *
	 * @param array $oldValues
	 * @param array $newValues
	 * @param boolean $recursively
	 *
	 * @return MapDiff
	 */
	public static function newFromArrays( array $oldValues, array $newValues, $recursively = false ) {
		$differ = new MapDiffer( $recursively );
		return new static( $differ->doDiff( $oldValues, $newValues ) );
	}

	/**
	 * Computes the diff between two associate arrays.
	 *
	 * @since 0.1
	 * @deprecated since 0.4, use MapDiffer::doDiff instead
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 * @param boolean $recursively If elements that are arrays should also be diffed.
	 *
	 * @throws Exception
	 * @return IDiffOp[]
	 */
	public static function doDiff( array $oldValues, array $newValues, $recursively = false ) {
		$differ = new MapDiffer( $recursively );
		return $differ->doDiff( $oldValues, $newValues );
	}

	/**
	 * @since 0.1
	 *
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
