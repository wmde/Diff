<?php

/**
 * Standalone entrypoint for the diff library contained in the Diff MediaWiki extension.
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

	if ( defined( 'MEDIAWIKI' ) ) {
		die( 'Not an entry point for MediaWiki. Use Diff.php' );
	}

	class Exception extends \Exception {}

	spl_autoload_register( function ( $className ) {
		static $classes = false;

		if ( $classes === false ) {
			$classes = include( __DIR__ . '/' . 'Diff.classes.php' );
		}

		if ( array_key_exists( $className, $classes ) ) {
			include_once __DIR__ . '/' . $classes[$className];
		}
	} );

	// Some example code :)
	/*
	$diff = MapDiff::newFromArrays(
		array(
			'foo' => 'bar',
			42,
			9001,
			'ohi' => 'there',
		),
		array(
			'foo' => 'baz',
			42,
			'spam' => 'zor',
		)
	);

	foreach ( $diff as $diffOp ) {
		var_dump( $diffOp );
	}
	*/
}

