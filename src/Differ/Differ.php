<?php

declare( strict_types = 1 );

namespace Diff\Differ;

use Diff\DiffOp\DiffOp;
use Exception;

/**
 * Interface for objects that can diff two arrays to an array of DiffOp.
 *
 * @since 0.4
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Differ {

	/**
	 * Takes two arrays, computes the diff, and returns this diff as an array of DiffOp.
	 *
	 * @since 0.4
	 *
	 * @param array $oldValues The first array
	 * @param array $newValues The second array
	 *
	 * @throws Exception
	 * @return DiffOp[]
	 */
	public function doDiff( array $oldValues, array $newValues ): array;

}
