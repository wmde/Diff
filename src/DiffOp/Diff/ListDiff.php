<?php

namespace Diff\DiffOp\Diff;

/**
 * Class representing the diff between to (non-associative) arrays.
 * Since items are not identified by keys, we only deal with the actual values,
 * so can only compute additions and removals.
 *
 * Soft deprecated since 0.4, just use Diff
 *
 * @since 0.1
 * @deprecated since 0.5, just use Diff instead
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiff extends Diff {

	public function __construct( array $operations = array() ) {
		parent::__construct( $operations, false );
	}

	/**
	 * @see DiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'list';
	}

}
