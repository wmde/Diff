<?php

require_once( 'PHPUnit/Runner/Version.php' );

if ( PHPUnit_Runner_Version::id() !== '@package_version@'
	&& version_compare( PHPUnit_Runner_Version::id(), '3.7', '<' )
) {
	die( 'PHPUnit 3.7 or later required, you have ' . PHPUnit_Runner_Version::id() . ".\n" );
}

require_once( 'PHPUnit/Autoload.php' );

require_once( __DIR__ . '/bootstrap.php' );

echo 'Running tests for Diff version ' . Diff_VERSION . ".\n";

$arguments = $_SERVER['argv'];
array_shift( $arguments );

if ( array_search( '--group', $arguments ) === false ) {
	$arguments[] = '--group';
	$arguments[] = 'Diff';
}

$arguments[] = __DIR__;

$runner = new PHPUnit_TextUI_Command();
$runner->run( $arguments );
