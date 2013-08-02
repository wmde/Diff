<?php

/**
 * This documentation group collects source code files belonging to Diff.
 *
 * @defgroup Diff Diff
 */

/**
 * Tests part of the Diff extension.
 *
 * @defgroup DiffTests DiffTest
 * @ingroup Diff
 * @ingroup Test
 */

if ( defined( 'Diff_VERSION' ) ) {
	// Do not initialize more then once.
	return;
}

define( 'Diff_VERSION', '0.8 alpha' );

// @codeCoverageIgnoreStart
spl_autoload_register( function ( $className ) {
	static $classes = false;

	if ( $classes === false ) {
		$classes = include( __DIR__ . '/' . 'Diff.classes.php' );
	}

	if ( array_key_exists( $className, $classes ) ) {
		include_once __DIR__ . '/' . $classes[$className];
	}
} );

if ( defined( 'MEDIAWIKI' ) ) {
	call_user_func( function() {
		require_once __DIR__ . '/Diff.mw.php';
	} );
}
// @codeCoverageIgnoreEnd