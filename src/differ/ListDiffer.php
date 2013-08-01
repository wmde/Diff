<?php

namespace Diff;

use Diff\ArrayComparer\StrictArrayComparer;

/**
 * Differ that only looks at the values of the arrays (and thus ignores key differences).
 *
 * Values are compared using the strictDiff method in strict mode (default)
 * or using array_diff_assoc in native mode.
 *
 * @since 0.4
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffer implements Differ {

	/**
	 * Use non-strict comparison and do not care about quantity.
	 * This makes use of @see array_diff
	 *
	 * @since 0.4
	 */
	const MODE_NATIVE = 0;

	/**
	 * Use strict comparison and care about quantity.
	 * This makes use of @see ListDiffer::strictDiff
	 *
	 * @since 0.4
	 */
	const MODE_STRICT = 1;

	/**
	 * @since 0.4
	 *
	 * @var int
	 */
	protected $diffMode;

	/**
	 * Constructor.
	 *
	 * Takes an argument that determines the diff mode.
	 * By default this is ListDiffer::MODE_STRICT, which causes
	 * computation in @see doDiff to be done via @see arrayDiff.
	 * If the native behavior is preferred, ListDiffer::MODE_NATIVE
	 * can be specified.
	 *
	 * @since 0.4
	 *
	 * @param int $diffMode
	 */
	public function __construct( $diffMode = self::MODE_STRICT ) {
		$this->diffMode = $diffMode;
	}

	/**
	 * @see Differ::doDiff
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
		$operations = array();

		foreach ( $this->diffArrays( $newValues, $oldValues ) as $addition ) {
			$operations[] = new DiffOpAdd( $addition );
		}

		foreach ( $this->diffArrays( $oldValues, $newValues ) as $removal ) {
			$operations[] = new DiffOpRemove( $removal );
		}

		return $operations;
	}

	/**
	 * @since 0.4
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	protected function diffArrays( array $arrayOne, array $arrayTwo ) {
		if ( $this->diffMode === self::MODE_STRICT ) {
			return $this->strictDiff( $arrayOne, $arrayTwo );
		}
		else {
			return array_diff( $arrayOne, $arrayTwo );
		}
	}

	/**
	 * Returns an array containing all the entries from arrayOne that are not present in arrayTwo.
	 *
	 * @since 0.4
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	protected function strictDiff( array $arrayOne, array $arrayTwo ) {
		$differ = new StrictArrayComparer();

		return $differ->diffArrays( $arrayOne, $arrayTwo );
	}

}
