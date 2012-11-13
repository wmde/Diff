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

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$dir = __DIR__ . '/';

$wgExtensionCredits['other'][] = include( $dir . 'Diff.credits.php' );

$wgExtensionMessagesFiles['DiffExtension'] = $dir . 'Diff.i18n.php';

// Autoloading
foreach ( include( $dir . 'Diff.classes.php' ) as $class => $file ) {
	if ( !array_key_exists( $class, $GLOBALS['wgAutoloadLocalClasses'] ) ) {
		$wgAutoloadClasses[$class] = $dir . $file;
	}
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
		//'Diff',
		'ListDiff',
		'MapDiff',
	);

	foreach ( $testFiles as $file ) {
		$files[] = __DIR__ . '/tests/' . $file . 'Test.php';
	}

	return true;
	// @codeCoverageIgnoreEnd
};

class_alias( '\MWException', 'Diff\Exception' );

unset( $dir );
