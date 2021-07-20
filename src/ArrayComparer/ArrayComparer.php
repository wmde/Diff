<?php

declare( strict_types = 1 );

namespace Diff\ArrayComparer;

/**
 * Interface for objects that can compute the difference between two arrays
 * in similar fashion to PHPs native array_diff.
 *
 * @since 0.8
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface ArrayComparer {

	/**
	 * Returns an array containing all the entries from arrayOne that are not present in arrayTwo.
	 *
	 * Implementations are allowed to hold quantity into account or to disregard it.
	 *
	 * @since 0.8
	 *
	 * @param array $firstArray
	 * @param array $secondArray
	 *
	 * @return array
	 */
	public function diffArrays( array $firstArray, array $secondArray ): array;

}
