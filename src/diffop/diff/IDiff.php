<?php

namespace Diff;

/**
 * Interface for diffs. Diffs are collections of DiffOp objects.
 *
 * Softly deprecated (since 0.4), use Diff instead.
 *
 * @since 0.1
 * @deprecated since 0.5
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface IDiff extends DiffOp, Appendable, \ArrayAccess, \Traversable {

	/**
	 * Returns the operations that make up the diff.
	 *
	 * @since 0.1
	 *
	 * @return DiffOp[]
	 */
	public function getOperations();

	/**
	 * Returns if the diff is empty. ie if it has no operations.
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isEmpty();

	/**
	 * Adds the provided operations to the diff.
	 * The operations are appended, so their associated keys
	 * in the provided array get lost.
	 *
	 * @since 0.1
	 *
	 * @param DiffOp[] $operations
	 */
	public function addOperations( array $operations );

	/**
	 * Removes empty Diff DiffOps from the diff.
	 *
	 * @since 0.3
	 */
	public function removeEmptyOperations();

	/**
	 * Returns if the diff is associative or not.
	 * Associative diffs are those where the operation keys are relevant.
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isAssociative();

}
