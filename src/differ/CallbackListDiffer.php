<?php

namespace Diff;

use Exception;

/**
 * Differ that only looks at the values of the arrays (and thus ignores key differences).
 * Values are compared via callback.
 *
 * Quantity matters: [42, 42] and [42] are different
 *
 * @since 0.5
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackListDiffer implements Differ {

	/**
	 * @since 0.5
	 *
	 * @var callable|null
	 */
	protected $comparisonCallback = null;

	/**
	 * Constructor.
	 *
	 * @since 0.5
	 *
	 * @param callable $comparisonCallback
	 */
	public function __construct( $comparisonCallback ) {
		$this->comparisonCallback = $comparisonCallback;
	}

	/**
	 * @see Differ::doDiff
	 *
	 * @since 0.5
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
	 * @since 0.5
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	protected function diffArrays( array $arrayOne, array $arrayTwo ) {
		$notInTwo = array();

		foreach ( $arrayOne as $element ) {
			$valueOffset = $this->arraySearch( $element, $arrayTwo );

			if ( $valueOffset === false ) {
				$notInTwo[] = $element;
				continue;
			}

			unset( $arrayTwo[$valueOffset] );
		}

		return $notInTwo;
	}

	/**
	 * @since 0.5
	 *
	 * @param string|int $needle
	 * @param array $haystack
	 *
	 * @return bool|int|string
	 * @throws Exception
	 */
	protected function arraySearch( $needle, array $haystack ) {
		foreach ( $haystack as $valueOffset => $thing ) {
			$areEqual = call_user_func_array( $this->comparisonCallback, array( $needle, $thing ) );

			if ( !is_bool( $areEqual ) ) {
				// TODO: throw a more specific exception type
				throw new Exception( 'Comparison callback returned a non-boolean value' );
			}

			if ( $areEqual ) {
				return $valueOffset;
			}
		}

		return false;
	}

}
