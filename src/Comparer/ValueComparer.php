<?php

namespace Diff\Comparer;

/**
 * Interface for objects that can determine if two values are equal.
 *
 * @since 0.6
 *
 * @licence GNU GPL v2+
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
	public function valuesAreEqual( $firstValue, $secondValue );

}
