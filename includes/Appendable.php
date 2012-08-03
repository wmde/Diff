<?php

namespace Diff;

/**
 * Interface things to which elements can be appended.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Appendable {

	public function append( $element );

}