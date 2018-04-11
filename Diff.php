<?php

if ( defined( 'Diff_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

define( 'Diff_VERSION', '2.3' );

// Aliasing of classes that got renamed.
// For more details, see Aliases.php.

// Aliases introduced in 1.0
class_alias( 'Diff\Differ\CallbackListDiffer', 'Diff\CallbackListDiffer' );
class_alias( 'Diff\Differ\Differ', 'Diff\Differ' );
class_alias( 'Diff\Differ\ListDiffer', 'Diff\ListDiffer' );
class_alias( 'Diff\Differ\MapDiffer', 'Diff\MapDiffer' );
class_alias( 'Diff\Differ\OrderedListDiffer', 'Diff\OrderedListDiffer' );

class_alias( 'Diff\Patcher\ListPatcher', 'Diff\ListPatcher' );
class_alias( 'Diff\Patcher\MapPatcher', 'Diff\MapPatcher' );
class_alias( 'Diff\Patcher\Patcher', 'Diff\Patcher' );
class_alias( 'Diff\Patcher\PatcherException', 'Diff\PatcherException' );
class_alias( 'Diff\Patcher\PreviewablePatcher', 'Diff\PreviewablePatcher' );
class_alias( 'Diff\Patcher\ThrowingPatcher', 'Diff\ThrowingPatcher' );

class_alias( 'Diff\DiffOp\Diff\Diff', 'Diff\Diff' );
class_alias( 'Diff\DiffOp\Diff\ListDiff', 'Diff\ListDiff' );
class_alias( 'Diff\DiffOp\Diff\MapDiff', 'Diff\MapDiff' );

class_alias( 'Diff\DiffOp\AtomicDiffOp', 'Diff\AtomicDiffOp' );
class_alias( 'Diff\DiffOp\DiffOp', 'Diff\DiffOp' );
class_alias( 'Diff\DiffOp\DiffOpAdd', 'Diff\DiffOpAdd' );
class_alias( 'Diff\DiffOp\DiffOpChange', 'Diff\DiffOpChange' );
class_alias( 'Diff\DiffOp\DiffOpRemove', 'Diff\DiffOpRemove' );
