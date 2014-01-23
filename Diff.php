<?php

if ( defined( 'Diff_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

define( 'Diff_VERSION', '1.0 alpha' );

if ( defined( 'MEDIAWIKI' ) ) {
	call_user_func( function() {
		require_once __DIR__ . '/Diff.mw.php';
	} );
}
// @codeCoverageIgnoreEnd