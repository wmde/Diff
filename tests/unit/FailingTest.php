<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @license BSD-3-Clause
 */
class FailingTest extends TestCase {

	public function testTrueIsNotFalse() {
		$this->assertTrue( false );
	}

}
