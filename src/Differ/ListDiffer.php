<?php

namespace Diff\Differ;

use Diff\ArrayComparer\ArrayComparer;
use Diff\ArrayComparer\NativeArrayComparer;
use Diff\ArrayComparer\StrictArrayComparer;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
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
	 * @deprecated since 0.8, use new NativeArrayComparer() instead
	 */
	const MODE_NATIVE = 0;

	/**
	 * Use strict comparison and care about quantity.
	 * This makes use of @see ListDiffer::strictDiff
	 *
	 * @since 0.4
	 * @deprecated since 0.8, use null instead
	 */
	const MODE_STRICT = 1;

	/**
	 * @var ArrayComparer
	 */
	private $arrayComparer;

	/**
	 * @param ArrayComparer|int|null $arrayComparer Defaults to a StrictArrayComparer. The use of
	 * the self::MODE_... constants is deprecated.
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $arrayComparer = null ) {
		if ( $arrayComparer === self::MODE_NATIVE ) {
			$arrayComparer = new NativeArrayComparer();
		} elseif ( $arrayComparer === self::MODE_STRICT ) {
			$arrayComparer = new StrictArrayComparer();
		}

		if ( $arrayComparer !== null && !( $arrayComparer instanceof ArrayComparer ) ) {
			throw new InvalidArgumentException( 'Got an invalid $arrayComparer' );
		}

		$this->arrayComparer = $arrayComparer ?: new StrictArrayComparer();
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
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	private function diffArrays( array $arrayOne, array $arrayTwo ) {
		return $this->arrayComparer->diffArrays( $arrayOne, $arrayTwo );
	}

}
