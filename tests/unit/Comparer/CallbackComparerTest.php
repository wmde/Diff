<?php

declare(strict_types=1);

namespace Diff\Tests\Comparer;

use Diff\Comparer\CallbackComparer;
use Diff\Tests\DiffTestCase;
use RuntimeException;

/**
 * @covers  \Diff\Comparer\CallbackComparer
 *
 * @group   Diff
 * @group   Comparer
 *
 * @license BSD-3-Clause
 * @author  Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CallbackComparerTest extends DiffTestCase {

	public function testWhenCallbackReturnsTrue_valuesAreEqual(): void {
		$comparer = new CallbackComparer(function () {
			return true;
		});

		$this->assertTrue($comparer->valuesAreEqual(null, null));
	}

	public function testWhenCallbackReturnsFalse_valuesAreNotEqual(): void {
		$comparer = new CallbackComparer(function () {
			return false;
		});

		$this->assertFalse($comparer->valuesAreEqual(null, null));
	}

	public function testWhenCallbackReturnsNonBoolean_exceptionIsThrown(): void {
		$comparer = new CallbackComparer(function () {
			return null;
		});

		$this->expectException(RuntimeException::class);
		$comparer->valuesAreEqual(null, null);
	}

	public function testCallbackIsGivenArguments(): void {
		$firstArgument = null;
		$secondArgument = null;

		$comparer = new CallbackComparer(function ($a, $b) use (&$firstArgument, &$secondArgument) {
			$firstArgument = $a;
			$secondArgument = $b;

			return true;
		});

		$comparer->valuesAreEqual(42, 23);

		$this->assertSame(42, $firstArgument);
		$this->assertSame(23, $secondArgument);
	}

}
