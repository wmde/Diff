<?php

namespace Diff\Tests;

use Diff\ThrowingPatcher;
use ReflectionClass;

/**
 * @covers Diff\ThrowingPatcher
 *
 * @group Diff
 * @group DiffPatcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ThrowingPatcherTest extends DiffTestCase {

	public function testChangeThrowErrors() {
		/**
		 * @var ThrowingPatcher $patcher
		 */
		$patcher = $this->getMockForAbstractClass( 'Diff\ThrowingPatcher' );

		$class = new ReflectionClass( 'Diff\ThrowingPatcher' );
		$method = $class->getMethod( 'handleError' );
		$method->setAccessible( true );

		$errorMessage = 'foo bar';

		$method->invokeArgs( $patcher, array( $errorMessage ) );

		$patcher->throwErrors();
		$patcher->ignoreErrors();

		$method->invokeArgs( $patcher, array( $errorMessage ) );

		$patcher->throwErrors();
		$this->setExpectedException( 'Diff\PatcherException' );

		$method->invokeArgs( $patcher, array( $errorMessage ) );
	}

}
