<?php

/**
 * Initialization file for the Diff extension.
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

/**
 * This documentation group collects source code files belonging to Diff.
 *
 * @defgroup Diff Diff
 */

namespace {

	if ( !defined( 'MEDIAWIKI' ) ) {
		die( 'Not an entry point.' );
	}

	$dir = dirname( __FILE__ ) . '/';

	$wgExtensionCredits['other'][] = include( $dir . 'Diff.credits.php' );

	$wgExtensionMessagesFiles['DiffExtension'] = $dir . 'Diff.i18n.php';

	// Autoloading
	foreach ( include( $dir . 'Diff.classes.php' ) as $class => $file ) {
		$wgAutoloadClasses[$class] = $dir . $file;
	}

	/**
	 * Hook to add PHPUnit test cases.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
	 *
	 * @since 0.1
	 *
	 * @param array $files
	 *
	 * @return boolean
	 */
	$wgHooks['UnitTestsList'][]	= function( array &$files ) {
		// @codeCoverageIgnoreStart
		$testFiles = array(
			'DiffOpAdd',
			'DiffOpChange',
			'DiffOpRemove',
			'Diff',
			'ListDiff',
			'MapDiff',
		);

		foreach ( $testFiles as $file ) {
			$files[] = dirname( __FILE__ ) . '/tests/' . $file . 'Test.php';
		}

		return true;
		// @codeCoverageIgnoreEnd
	};

	unset( $dir );

}

namespace Diff {
	class Exception extends \MWException {}
}