<?php

/**
 * PHPUnit bootstrap file for the Diff library.
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

$autoLoader = require_once __DIR__ . '/../vendor/autoload.php';

$autoLoader->addPsr4( 'Diff\\Tests\\', __DIR__ . '/phpunit/' );
