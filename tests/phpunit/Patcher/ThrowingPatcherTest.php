<?php

namespace Diff\Tests\Patcher;

use Diff\Patcher\ThrowingPatcher;
use Diff\Tests\DiffTestCase;
use ReflectionClass;

/**
 * @covers Diff\Patcher\ThrowingPatcher
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
		$patcher = $this->getMockForAbstractClass( 'Diff\Patcher\ThrowingPatcher' );

		$class = new ReflectionClass( 'Diff\Patcher\ThrowingPatcher' );
		$method = $class->getMethod( 'handleError' );
		$method->setAccessible( true );

		$errorMessage = 'foo bar';

		$method->invokeArgs( $patcher, array( $errorMessage ) );

		$patcher->throwErrors();
		$patcher->ignoreErrors();

		$method->invokeArgs( $patcher, array( $errorMessage ) );

		$patcher->throwErrors();
		$this->setExpectedException( 'Diff\Patcher\PatcherException' );

		$method->invokeArgs( $patcher, array( $errorMessage ) );
	}

}
