<?php

declare( strict_types = 1 );

namespace Diff\Patcher;

use Diff\DiffOp\Diff\Diff;

/**
 * Interface for patchers that can, given a base and a diff, provide
 * the difference between the base and the result once the diff is
 * provided to it. This difference can differ from the diff since
 * some operations might not be applicable to the base.
 *
 * @since 0.4
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface PreviewablePatcher extends Patcher {

	/**
	 * Returns the operations that can be applied to the base.
	 * The returned operations are thus the difference between
	 * the result of @see patch and it's input base value.
	 *
	 * @since 0.4
	 *
	 * @param array $base
	 * @param Diff $diffOps
	 *
	 * @return Diff
	 */
	public function getApplicableDiff( array $base, Diff $diffOps );

}
