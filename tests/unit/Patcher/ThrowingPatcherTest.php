<?php

declare(strict_types=1);

namespace Diff\Tests\Patcher;

use Diff\Patcher\ThrowingPatcher;
use Diff\Tests\DiffTestCase;
use ReflectionClass;

/**
 * @covers  \Diff\Patcher\ThrowingPatcher
 *
 * @group   Diff
 * @group   DiffPatcher
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ThrowingPatcherTest extends DiffTestCase {

	public function testChangeThrowErrors(): void {
		/**
		 * @var ThrowingPatcher $patcher
		 */
		$patcher = $this->getMockForAbstractClass('Diff\Patcher\ThrowingPatcher');

		$class = new ReflectionClass('Diff\Patcher\ThrowingPatcher');
		$method = $class->getMethod('handleError');
		$method->setAccessible(true);

		$errorMessage = 'foo bar';

		$method->invokeArgs($patcher, [$errorMessage]);

		$patcher->throwErrors();
		$patcher->ignoreErrors();

		$method->invokeArgs($patcher, [$errorMessage]);

		$patcher->throwErrors();
		$this->expectException('Diff\Patcher\PatcherException');

		$method->invokeArgs($patcher, [$errorMessage]);
	}

}
