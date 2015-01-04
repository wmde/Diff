<?php

// This is an IDE helper to understand class aliasing.
// It should not be included anywhere.
// Actual aliasing happens in the entry point using class_alias.

namespace { throw new Exception( 'This code is not meant to be executed' ); }

namespace Diff {

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class CallbackListDiffer extends Differ\CallbackListDiffer {}

	/**
	 * @deprecated since 1.0, use the base interface instead.
	 */
	interface Differ extends Differ\Differ {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class ListDiffer extends Differ\ListDiffer {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class MapDiffer extends Differ\MapDiffer {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class OrderedListDiffer extends Differ\OrderedListDiffer {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class ListPatcher extends Patcher\ListPatcher {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class MapPatcher extends Patcher\MapPatcher {}

	/**
	 * @deprecated since 1.0, use the base interface instead.
	 */
	interface Patcher extends Patcher\Patcher {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class PatcherException extends Patcher\PatcherException {}

	/**
	 * @deprecated since 1.0, use the base interface instead.
	 */
	interface PreviewablePatcher extends Patcher\PreviewablePatcher {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	abstract class ThrowingPatcher extends Patcher\ThrowingPatcher {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class Diff extends DiffOp\Diff\Diff {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class ListDiff extends DiffOp\Diff\ListDiff {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class MapDiff extends DiffOp\Diff\MapDiff {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	abstract class AtomicDiffOp extends DiffOp\AtomicDiffOp {}

	/**
	 * @deprecated since 1.0, use the base interface instead.
	 */
	interface DiffOp extends DiffOp\DiffOp {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class DiffOpAdd extends DiffOp\DiffOpAdd {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class DiffOpChange extends DiffOp\DiffOpChange {}

	/**
	 * @deprecated since 1.0, use the base class instead.
	 */
	class DiffOpRemove extends DiffOp\DiffOpRemove {}

}
