<?php

if ( defined( 'Diff_VERSION' ) ) {
	// Do not initialize more then once.
	return;
}

define( 'Diff_VERSION', '0.9' );

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