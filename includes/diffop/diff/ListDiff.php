<?php

namespace Diff;

/**
 * Class representing the diff between to (non-associative) arrays.
 * Since items are not identified by keys, we only deal with the actual values,
 * so can only compute additions and removals.
 *
 * Soft deprecated since 0.4, just use Diff
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiff extends Diff {

	public function __construct( array $operations = array() ) {
		parent::__construct( $operations, false );
	}

	/**
	 * Creates and returns an empty ListDiff.
	 * @see Diff::newEmpty
	 *
	 * @since 0.1
	 * @deprecated since 0.4, just use the constructor
	 *
	 * @return ListDiff
	 */
	public static function newEmpty() {
		return new static( array() );
	}

	/**
	 * Creates a new ListDiff given two arrays.
	 *
	 * @since 0.1
	 * @deprecated since 0.4, use ListDiffer::doDiff instead
	 *
	 * @param array $firstList
	 * @param array $secondList
	 *
	 * @return ListDiff
	 */
	public static function newFromArrays( array $firstList, array $secondList ) {
		$differ = new ListDiffer();
		return new static( $differ->doDiff( $firstList, $secondList ) );
	}

	/**
	 * @see DiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'list';
	}

}
