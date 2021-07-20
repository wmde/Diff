<?php

declare( strict_types = 1 );

namespace Diff\Comparer;

/**
 * Interface for objects that can determine if two values are equal.
 *
 * @since 0.6
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface ValueComparer {

	/**
	 * @since 0.6
	 *
	 * @param mixed $firstValue
	 * @param mixed $secondValue
	 *
	 * @return bool
	 */
	public function valuesAreEqual( $firstValue, $secondValue ): bool;

}
