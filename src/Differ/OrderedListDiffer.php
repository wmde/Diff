<?php

declare( strict_types = 1 );

namespace Diff\Differ;

use Diff\ArrayComparer\OrderedArrayComparer;
use Diff\Comparer\ValueComparerInterface;
use Diff\DiffOp\DiffOpInterface;

/**
 * Differ that looks at the order of the values and the values of the arrays.
 *
 * Quantity matters: [42, 42] and [42] are different
 * Order matters: [42, 43] and [43, 42] are different
 *
 * @since 0.9
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Tobias Gritschacher < tobias.gritschacher@wikimedia.de >
 */
class OrderedListDiffer implements DifferInterface {

	/**
	 * @var ListDiffer
	 */
	private $differ;

	/**
	 * @since 0.9
	 *
	 * @param ValueComparerInterface $comparer
	 */
	public function __construct( ValueComparerInterface $comparer ) {
		$this->differ = new ListDiffer( new OrderedArrayComparer( $comparer ) );
	}

	/**
	 * @see DifferInterface::doDiff
	 *
	 * @since 0.9
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
