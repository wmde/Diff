<?php

/**
 * Class registration file for the Diff library.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
return array(
	'GenericArrayObject' => 'compat/GenericArrayObject.php',

	'Diff\Appendable' => 'includes/Appendable.php',
	'Diff\DiffOpFactory' => 'includes/DiffOpFactory.php',

	'Diff\CallbackListDiffer' => 'includes/differ/CallbackListDiffer.php',
	'Diff\Differ' => 'includes/differ/Differ.php',
	'Diff\ListDiffer' => 'includes/differ/ListDiffer.php',
	'Diff\MapDiffer' => 'includes/differ/MapDiffer.php',

	'Diff\AtomicDiffOp' => 'includes/diffop/AtomicDiffOp.php',
	'Diff\DiffOp' => 'includes/diffop/DiffOp.php',
	'Diff\IDiffOp' => 'includes/diffop/DiffOp.php',
	'Diff\DiffOpAdd' => 'includes/diffop/DiffOpAdd.php',
	'Diff\DiffOpChange' => 'includes/diffop/DiffOpChange.php',
	'Diff\DiffOpRemove' => 'includes/diffop/DiffOpRemove.php',

	'Diff\ListPatcher' => 'includes/patcher/ListPatcher.php',
	'Diff\MapPatcher' => 'includes/patcher/MapPatcher.php',
	'Diff\Patcher' => 'includes/patcher/Patcher.php',
	'Diff\PatcherException' => 'includes/patcher/PatcherException.php',
	'Diff\PreviewablePatcher' => 'includes/patcher/PreviewablePatcher.php',
	'Diff\ThrowingPatcher' => 'includes/patcher/ThrowingPatcher.php',

	'Diff\Diff' => 'includes/diffop/diff/Diff.php',
	'Diff\IDiff' => 'includes/diffop/diff/IDiff.php',
	'Diff\ListDiff' => 'includes/diffop/diff/ListDiff.php',
	'Diff\MapDiff' => 'includes/diffop/diff/MapDiff.php',

	'AbstractTestCase' => 'tests/AbstractTestCase.php',
	'Diff\Test\DiffOpTest' => 'tests/diffop/DiffOpTest.php',
	'Diff\Test\DiffOpTestDummy' => 'tests/DiffOpTestDummy.php',
);
