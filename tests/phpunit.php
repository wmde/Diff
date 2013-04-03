<?php

require_once( 'PHPUnit/Runner/Version.php' );

if ( PHPUnit_Runner_Version::id() !== '@package_version@'
	&& version_compare( PHPUnit_Runner_Version::id(), '3.7', '<' )
) {
	die( 'PHPUnit 3.7 or later required, you have ' . PHPUnit_Runner_Version::id() . ".\n" );
}

require_once( 'PHPUnit/Autoload.php' );

require_once( __DIR__ . '/../Diff.php' );

$runner = new PHPUnit_TextUI_Command();

$runner->run( array(
   __FILE__,
   '--group',
   'Diff',
   __DIR__
) );