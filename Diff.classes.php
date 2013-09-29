<?php

/**
 * Class registration file for the Diff library.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
return array(
	'Diff\Appendable' => 'src/Appendable.php',
	'Diff\DiffOpFactory' => 'src/DiffOpFactory.php',

	'Diff\ArrayComparer\ArrayComparer' => 'src/ArrayComparer/ArrayComparer.php',
	'Diff\ArrayComparer\NativeArrayComparer' => 'src/ArrayComparer/NativeArrayComparer.php',
	'Diff\ArrayComparer\StrategicArrayComparer' => 'src/ArrayComparer/StrategicArrayComparer.php',
	'Diff\ArrayComparer\OrderedArrayComparer' => 'src/ArrayComparer/OrderedArrayComparer.php',
	'Diff\ArrayComparer\StrictArrayComparer' => 'src/ArrayComparer/StrictArrayComparer.php',

	'Diff\Comparer\CallbackComparer' => 'src/Comparer/CallbackComparer.php',
	'Diff\Comparer\ComparableComparer' => 'src/Comparer/ComparableComparer.php',
	'Diff\Comparer\StrictComparer' => 'src/Comparer/StrictComparer.php',
	'Diff\Comparer\ValueComparer' => 'src/Comparer/ValueComparer.php',

	'Diff\CallbackListDiffer' => 'src/differ/CallbackListDiffer.php',
	'Diff\OrderedListDiffer' => 'src/differ/OrderedListDiffer.php',
	'Diff\Differ' => 'src/differ/Differ.php',
	'Diff\ListDiffer' => 'src/differ/ListDiffer.php',
	'Diff\MapDiffer' => 'src/differ/MapDiffer.php',

	'Diff\AtomicDiffOp' => 'src/diffop/AtomicDiffOp.php',
	'Diff\DiffOp' => 'src/diffop/DiffOp.php',
	'Diff\IDiffOp' => 'src/diffop/DiffOp.php',
	'Diff\DiffOpAdd' => 'src/diffop/DiffOpAdd.php',
	'Diff\DiffOpChange' => 'src/diffop/DiffOpChange.php',
	'Diff\DiffOpRemove' => 'src/diffop/DiffOpRemove.php',

	'Diff\ListPatcher' => 'src/patcher/ListPatcher.php',
	'Diff\MapPatcher' => 'src/patcher/MapPatcher.php',
	'Diff\Patcher' => 'src/patcher/Patcher.php',
	'Diff\PatcherException' => 'src/patcher/PatcherException.php',
	'Diff\PreviewablePatcher' => 'src/patcher/PreviewablePatcher.php',
	'Diff\ThrowingPatcher' => 'src/patcher/ThrowingPatcher.php',

	'Diff\Diff' => 'src/diffop/diff/Diff.php',
	'Diff\IDiff' => 'src/diffop/diff/IDiff.php',
	'Diff\ListDiff' => 'src/diffop/diff/ListDiff.php',
	'Diff\MapDiff' => 'src/diffop/diff/MapDiff.php',

	'Diff\Tests\DiffOpTest' => 'tests/phpunit/diffop/DiffOpTest.php',
	'Diff\Tests\DiffTestCase' => 'tests/phpunit/DiffTestCase.php',
);
