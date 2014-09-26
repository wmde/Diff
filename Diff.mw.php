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

$GLOBALS['wgExtensionCredits']['other'][] = array(
	'path' => __FILE__,
	'name' => 'Diff',
	'version' => Diff_VERSION,
	'author' => array(
		'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
	),
	'url' => 'https://github.com/wmde/Diff',
	'description' => 'Library for diffing, patching and representing differences between complex objects',
	'license-name' => 'GPL-2.0+'
);
