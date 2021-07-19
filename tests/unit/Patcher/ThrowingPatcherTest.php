<?php

declare( strict_types = 1 );

namespace Diff\Tests\Patcher;

use Diff\Patcher\AbstractThrowingPatcher;
use Diff\Tests\AbstractDiffTestCase;
use ReflectionClass;

/**
 * @covers \Diff\Patcher\AbstractThrowingPatcher
 *
 * @group Diff
 * @group DiffPatcher
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ThrowingPatcherTest extends AbstractDiffTestCase {

	public function testChangeThrowErrors() {
		/**
		 * @var AbstractThrowingPatcher $patcher
		 */
		$patcher = $this->getMockForAbstractClass('Diff\Patcher\AbstractThrowingPatcher');

		$class = new ReflectionClass('Diff\Patcher\AbstractThrowingPatcher');
		$method = $class->getMethod( 'handleError' );
		$method->setAccessible( true );

		$errorMessage = 'foo bar';

		$method->invokeArgs( $patcher, array( $errorMessage ) );

		$patcher->throwErrors();
		$patcher->ignoreErrors();

		$method->invokeArgs( $patcher, array( $errorMessage ) );

		$patcher->throwErrors();
		$this->expectException( 'Diff\Patcher\PatcherException' );

		$method->invokeArgs( $patcher, array( $errorMessage ) );
	}

}
