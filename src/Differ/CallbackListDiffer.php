<?php

declare( strict_types = 1 );

namespace Diff\Differ;

use Diff\ArrayComparer\StrategicArrayComparer;
use Diff\Comparer\CallbackComparer;
use Diff\DiffOp\DiffOpInterface;

/**
 * Differ that only looks at the values of the arrays (and thus ignores key differences).
 * Values are compared via callback.
 *
 * Quantity matters: [42, 42] and [42] are different
 *
 * @since 0.5
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackListDiffer implements DifferInterface {

	/**
	 * @var ListDiffer
	 */
	private $differ;

	/**
	 * @since 0.5
	 *
	 * @param callable $comparisonCallback
	 */
	public function __construct( $comparisonCallback ) {
		$this->differ = new ListDiffer(
			new StrategicArrayComparer( new CallbackComparer( $comparisonCallback ) )
		);
	}

	/**
	 * @see DifferInterface::doDiff
	 *
	 * @since 0.5
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 *
	 * @return DiffOpInterface[]
	 */
	public function doDiff( array $oldValues, array $newValues ): array {
		return $this->differ->doDiff( $oldValues, $newValues );
	}

}
