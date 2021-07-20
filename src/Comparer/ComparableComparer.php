<?php

declare( strict_types = 1 );

namespace Diff\Comparer;

/**
 * Value comparer for objects that provide an equals method taking a single argument.
 *
 * @since 0.9
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ComparableComparer implements ValueComparer {

	public function valuesAreEqual( $firstValue, $secondValue ): bool {
		if ( $firstValue && method_exists( $firstValue, 'equals' ) ) {
			return $firstValue->equals( $secondValue );
		}

		return false;
	}

}
