<?php

/**
 * MediaWiki setup of the Diff library as a MediaWiki extension.
 * The library should be included via the main entry point, Diff.php.
 *
 * @since 0.3
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'Diff_VERSION' ) ) {
	die( 'Not an entry point.' );
}

$GLOBALS['wgExtensionCredits']['other'][] = include( __DIR__ . '/Diff.credits.php' );

$GLOBALS['wgMessagesDirs']['DiffExtension'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['DiffExtension'] = __DIR__ . '/Diff.i18n.php';
