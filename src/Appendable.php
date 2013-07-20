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

	/**
	 * Appends a value to the object.
	 *
	 * @since 0.1
	 *
	 * @param mixed $element
	 */
	public function append( $element );

}
