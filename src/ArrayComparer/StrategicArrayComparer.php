<?php

namespace Diff\ArrayComparer;

use Diff\Comparer\ValueComparer;

/**
 * Computes the difference between two arrays by comparing elements with
 * a ValueComparer.
 *
 * @since 0.7
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrategicArrayComparer implements ArrayComparer {

	protected $valueComparer;

	public function __construct( ValueComparer $valueComparer ) {
		$this->valueComparer = $valueComparer;
	}

	/**
	 * @see ArrayComparer::diffArrays
	 *
	 * @since 0.7
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function diffArrays( array $arrayOne, array $arrayTwo ) {
		return array(); // TODO: implement
	}

}
