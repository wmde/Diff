<?php

declare( strict_types = 1 );

namespace Diff\Comparer;

/**
 * Value comparer that uses PHPs native strict equality check (ie ===).
 *
 * @since 0.6
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrictComparer implements ValueComparer {

	/**
	 * @param mixed $firstValue
	 * @param mixed $secondValue
	 *
	 * @return bool
	 */
	public function valuesAreEqual( $firstValue, $secondValue ): bool {
		return $firstValue === $secondValue;
	}

}
