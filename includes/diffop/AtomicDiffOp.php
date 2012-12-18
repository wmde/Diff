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
abstract class AtomicDiffOp implements IDiffOp {

	/**
	 * @see Countable::count
	 *
	 * @since 0.1
	 *
	 * @return integer
	 */
	public function count() {
		return 1;
	}

	/**
	 * @see DiffOp::isAtomic
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isAtomic() {
		return true;
	}

}