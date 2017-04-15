<?php

declare( strict_types = 1 );

if ( defined( 'Diff_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

define( 'Diff_VERSION', '3.0' );

if ( defined( 'MEDIAWIKI' ) ) {
	call_user_func( function() {
		require_once __DIR__ . '/Diff.mw.php';
	} );
}
