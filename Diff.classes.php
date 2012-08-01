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
 */
return array(
	'Diff\Diff' => 'includes/Diff.php',
	'Diff\DiffOp' => 'includes/DiffOp.php',
	'Diff\DiffOpAdd' => 'includes/DiffOpAdd.php',
	'Diff\DiffOpChange' => 'includes/DiffOpChange.php',
	'Diff\DiffOpRemove' => 'includes/DiffOpRemove.php',
	'Diff\GenericArrayObject' => 'includes/GenericArrayObject.php',
	'Diff\IDiff' => 'includes/IDiff.php',
	'Diff\IDiffOp' => 'includes/IDiffOp.php',
	'Diff\ListDiff' => 'includes/ListDiff.php',
	'Diff\MapDiff' => 'includes/MapDiff.php',

	'AbstractTestCase' => 'tests/AbstractTestCase.php',
	'Diff\Test\BaseDiffOpTest' => 'tests/BaseDiffOpTest.php',
	'Diff\Test\GenericArrayObjectTest' => 'tests/GenericArrayObjectTest.php',
);
