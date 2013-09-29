<?php

namespace Diff;

use Diff\ArrayComparer\ArrayComparer;
use Diff\ArrayComparer\NativeArrayComparer;
use Diff\ArrayComparer\StrictArrayComparer;
use InvalidArgumentException;

/**
 * Differ that only looks at the values of the arrays (and thus ignores key differences).
 *
 * By default values are compared using a StrictArrayComparer.
 * You can alter the ArrayComparer used by providing one in the constructor.
 *
 * @since 0.4
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffer implements Differ {

	/**
	 * Use non-strict comparison and do not care about quantity.
	 * This makes use of @see array_diff
	 *
	 * @since 0.4
	 * @deprecated since 0.8
	 */
	const MODE_NATIVE = 0;

	/**
	 * Use strict comparison and care about quantity.
	 * This makes use of @see ListDiffer::strictDiff
	 *
	 * @since 0.4
	 * @deprecated since 0.8
	 */
	const MODE_STRICT = 1;

	/**
	 * @since 0.8
	 *
	 * @var ArrayComparer
	 */
	protected $arrayComparer;

	/**
	 * @param ArrayComparer $arrayComparer
	 *
	 * The first argument is an ArrayComparer since version 0.8.
	 * Before this it was an element of the ListDiffer::MODE_ enum.
	 * The later is still supported though deprecated.
	 */
	public function __construct( $arrayComparer = null ) {
		$this->arrayComparer = $this->getRealArrayComparer( $arrayComparer );
	}

	/**
	 * @param $arrayComparer
	 *
	 * @return ArrayComparer
	 * @throws InvalidArgumentException
	 */
	protected function getRealArrayComparer( $arrayComparer ) {
		if ( $arrayComparer === null || $arrayComparer === self::MODE_STRICT ) {
			return new StrictArrayComparer();
		}

		if ( $arrayComparer === self::MODE_NATIVE ) {
			 return new NativeArrayComparer();
		}

		if ( is_object( $arrayComparer ) && $arrayComparer instanceof ArrayComparer ) {
			return $arrayComparer;
		}

		throw new InvalidArgumentException( 'Got an invalid $arrayComparer' );
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
	 * @throws \Exception
	 */
	public function doDiff( array $oldValues, array $newValues ) {
		$operations = array();

		foreach ( $this->diffArrays( $newValues, $oldValues ) as $addition ) {
			$operations[] = new DiffOpAdd( $addition );
		}

		foreach ( $this->diffArrays( $oldValues, $newValues ) as $removal ) {
			$operations[] = new DiffOpRemove( $removal );
		}

		return $operations;
	}

	/**
	 * @since 0.4
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	protected function diffArrays( array $arrayOne, array $arrayTwo ) {
		return $this->arrayComparer->diffArrays( $arrayOne, $arrayTwo );
	}

}
