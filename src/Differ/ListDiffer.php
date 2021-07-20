<?php

declare( strict_types = 1 );

namespace Diff\Differ;

use Diff\ArrayComparer\ArrayComparer;
use Diff\ArrayComparer\StrictArrayComparer;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;

/**
 * Differ that only looks at the values of the arrays (and thus ignores key differences).
 *
 * By default values are compared using a StrictArrayComparer.
 * You can alter the ArrayComparer used by providing one in the constructor.
 *
 * @since 0.4
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffer implements Differ {

	/**
	 * @var ArrayComparer
	 */
	private $arrayComparer;

	public function __construct( ArrayComparer $arrayComparer = null ) {
		$this->arrayComparer = $arrayComparer ?? new StrictArrayComparer();
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
	 */
	public function doDiff( array $oldValues, array $newValues ): array {
		$operations = [];

		foreach ( $this->diffArrays( $newValues, $oldValues ) as $addition ) {
			$operations[] = new DiffOpAdd( $addition );
		}

		foreach ( $this->diffArrays( $oldValues, $newValues ) as $removal ) {
			$operations[] = new DiffOpRemove( $removal );
		}

		return $operations;
	}

	/**
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	private function diffArrays( array $arrayOne, array $arrayTwo ): array {
		return $this->arrayComparer->diffArrays( $arrayOne, $arrayTwo );
	}

}
