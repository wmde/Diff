<?php

declare( strict_types = 1 );

namespace Diff\Tests\Fixtures;

/**
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StubComparable {

	/** @var mixed */
	private $field;

	/**
	 * @param mixed $field
	 */
	public function __construct( $field ) {
		$this->field = $field;
	}

	/**
	 * @param mixed $otherComparable
	 */
	public function equals( $otherComparable ): bool {
		return $otherComparable instanceof StubComparable
		&& $otherComparable->getField() === $this->field;
	}

	/**
	 * @return mixed
	 */
	public function getField() {
		return $this->field;
	}

}
