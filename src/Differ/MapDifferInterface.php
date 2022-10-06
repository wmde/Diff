<?php
declare( strict_types = 1 );

namespace Diff\Differ;

/**
 * Differ that diffs associatively (holding the array keys into account).
 * These differs can produce associative diff operations (changes and diffs).
 *
 * @since 3.2
 *
 * @license GPL-2.0+
 * @author Alexander Borisov < boshurik@gmail.com >
 */
interface MapDifferInterface extends Differ {

}
