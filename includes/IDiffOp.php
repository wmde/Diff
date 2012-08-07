<?php

namespace Diff;

/**
 * Interface for diff operations. A diff operation
 * represents a change to a single element.
 * In case the elements are maps or diffs, the resulting operation
 * can be a MapDiff or ListDiff, which contain their own list of IDiffOp objects.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface IDiffOp extends \Serializable {

	/**
	 * Returns a string identifier for the operation type.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType();

	/**
	 * Returns if the operation is atomic, opposing to it
	 * being a composite that can contain one or more child elements.
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isAtomic();

}
