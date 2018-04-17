<?php

declare( strict_types = 1 );

namespace Diff\Tests\Fixtures;

/**
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StubComparable {

	private $field;

	public function __construct( $field ) {
		$this->field = $field;
	}

	public function equals( $otherComparable ) {
		return $otherComparable instanceof StubComparable
		&& $otherComparable->getField() === $this->field;
	}

	public function getField() {
		return $this->field;
	}

}
