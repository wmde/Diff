<?php

declare( strict_types = 1 );

namespace Diff\Tests\Fixtures;

use Diff\Comparer\ValueComparer;

/**
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StubValueComparer implements ValueComparer {

	private $returnValue;

	public function __construct( bool $returnValue ) {
		$this->returnValue = $returnValue;
	}

	// @codingStandardsIgnoreStart
	public function valuesAreEqual( $firstValue, $secondValue ): bool {
		// @codingStandardsIgnoreEnd
		return $this->returnValue;
	}

}
