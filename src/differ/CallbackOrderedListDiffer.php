<?php

namespace Diff;

use Diff\ArrayComparer\StrategicOrderedArrayComparer;
use Diff\Comparer\CallbackComparer;

/**
 * Differ that looks at the order of the values and the values of the arrays.
 * Values are compared via callback.
 *
 * Quantity matters: [42, 42] and [42] are different
 * Order matters: [42, 43] and [43, 42] are different
 *
 * @since 0.8
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Tobias Gritschacher < tobias.gritschacher@wikimedia.de >
 */
class CallbackOrderedListDiffer implements Differ {

	/**
	 * @since 0.8
	 *
	 * @var ListDiffer
	 */
	protected $differ = null;

	/**
	 * Constructor.
	 *
	 * @since 0.8
	 *
	 * @param callable $comparisonCallback
	 */
	public function __construct( $comparisonCallback ) {
		$this->differ = new ListDiffer( new StrategicOrderedArrayComparer( new CallbackComparer( $comparisonCallback ) ) );
	}

	/**
	 * @see Differ::doDiff
	 *
	 * @since 0.8
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 *
	 * @return DiffOp[]
	 */
	public function doDiff( array $oldValues, array $newValues ) {
		$diffOps = $this->differ->doDiff( $oldValues, $newValues );
		return $diffOps;
	}

}
