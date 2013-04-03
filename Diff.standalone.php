<?php

/**
 * Standalone setup for the diff library contained in the Diff MediaWiki extension.
 * The library should be included via the main entry point, Diff.php.
 *
 * Documentation:	 		https://www.mediawiki.org/wiki/Extension:Diff
 * Support					https://www.mediawiki.org/wiki/Extension_talk:Diff
 * Source code:				https://gerrit.wikimedia.org/r/gitweb?p=mediawiki/extensions/Diff.git
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

namespace Diff {

	if ( !defined( 'Diff_VERSION' ) ) {
		die( 'Not an entry point for MediaWiki. Use Diff.php' );
	}

	spl_autoload_register( function ( $className ) {
		static $classes = false;

		if ( $classes === false ) {
			$classes = include( __DIR__ . '/' . 'Diff.classes.php' );
		}

		if ( array_key_exists( $className, $classes ) ) {
			include_once __DIR__ . '/' . $classes[$className];
		}
	} );

}

