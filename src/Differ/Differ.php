<?php

namespace Diff\Differ;

use Diff\DiffOp\DiffOp;

/**
 * Interface for objects that can diff two arrays to an array of DiffOp.
 *
 * @since 0.4
 *
 * @licence GNU GPL v2+
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
	 * @return DiffOp[]
	 * @throws \Exception
	 */
	public function doDiff( array $oldValues, array $newValues );

}
