<?php

declare( strict_types = 1 );

namespace Diff\DiffOp;

use Countable;
use Serializable;

/**
 * Interface for diff operations. A diff operation
 * represents a change to a single element.
 * In case the elements are maps or diffs, the resulting operation
 * can be a Diff which contains its own list of DiffOp objects.
 *
 * @since 0.1
 *
 * @license BSD-3-Clause
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface DiffOp extends Serializable, Countable {

	/**
	 * Returns a string identifier for the operation type.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType(): string;

	/**
	 * Returns if the operation is atomic, opposing to it
	 * being a composite that can contain one or more child elements.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function isAtomic(): bool;

	/**
	 * Returns the DiffOp in array form.
	 *
	 * All element of the array with either be primitives or arrays, with the exception
	 * of complex values. For instance an add operation containing an object will have this
	 * object in the resulting array.
	 *
	 * This array form is particularly useful for serialization, as you can feed it
	 * to serialization functions such as json_encode() or serialize(), keeping in mind
	 * you might need extra handling for complex objects contained in the DiffOp.
	 *
	 * Roundtrips with DiffOpFactory::newFromArray.
	 *
	 * @since 0.5
	 *
	 * @param callable|null $valueConverter optional callback used to convert any
	 *        complex values to arrays.
	 *
	 * @return array
	 */
	public function toArray( callable $valueConverter = null ): array;

}
