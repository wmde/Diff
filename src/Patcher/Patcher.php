<?php

declare( strict_types = 1 );

namespace Diff\Patcher;

use Diff\DiffOp\Diff\Diff;

/**
 * Interface for objects that can apply an array of DiffOp on an array.
 *
 * @since 0.4
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Patcher {

	/**
	 * Applies the applicable operations from the provided diff to
	 * the provided base value.
	 *
	 * @since 0.4
	 *
	 * @param array $base
	 * @param Diff $diffOps
	 *
	 * @return array
	 */
	public function patch( array $base, Diff $diffOps ): array;

}
