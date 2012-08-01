<?php

namespace Diff;

/**
 * Base class for diff operations. A diff operation
 * represents a change to a single element.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DiffOp implements IDiffOp {

	/**
	 * Returns a new IDiffOp implementing instance to represent the provided change.
	 *
	 * @since 0.1
	 *
	 * @param array $array
	 *
	 * @return IDiffOp
	 * @throws \Diff\Exception
	 */
	public static function newFromArray( array $array ) {
		$type = array_shift( $array );

		$typeMap = array(
			'add' => '\Diff\DiffOpAdd',
			'remove' => '\Diff\DiffOpRemove',
			'change' => '\Diff\DiffOpChange',
			'list' => '\Diff\ListDiff',
			'map' => '\Diff\MapDiff',
		);

		if ( !array_key_exists( $type, $typeMap ) ) {
			throw new Exception( 'Invalid diff type provided.' );
		}

		$reflector = new \ReflectionClass( $typeMap[$type] );
		return $reflector->newInstanceArgs( $array );
	}

}