<?php

namespace Diff\Tests\Fixtures;

/**
 * @licence GNU GPL v2+
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
