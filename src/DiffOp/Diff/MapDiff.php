<?php

namespace Diff\DiffOp\Diff;

/**
 * Class representing the diff between to (associative) arrays.
 * Since items are identified by keys, it's possible to do meaningful
 * recursive diffs. So the DiffOp objects contained by this MapDiff can
 * be containers such as MapDiff and ListDiff themselves.
 *
 * Soft deprecated since 0.4, just use Diff
 *
 * @since 0.1
 * @deprecated since 0.5, just use Diff instead
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapDiff extends Diff {

	public function __construct( array $operations = array() ) {
		parent::__construct( $operations, true );
	}

	/**
	 * @see DiffOp::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return 'map';
	}

}
