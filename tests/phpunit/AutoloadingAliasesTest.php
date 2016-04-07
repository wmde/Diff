<?php

namespace Diff\Tests;

/**
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class AutoloadingAliasesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider oldNameProvider
	 */
	public function testAliasExists( $className ) {
		$this->assertTrue(
			class_exists( $className ) || interface_exists( $className ),
			'Class name "' . $className . '" should still exist as alias'
		);
	}

	public function oldNameProvider() {
		return array(
			array( 'Diff\Diff' ),
			array( 'Diff\MapDiff' ),
			array( 'Diff\ListDiff' ),

			array( 'Diff\AtomicDiffOp' ),
			array( 'Diff\DiffOp' ),
			array( 'Diff\DiffOpAdd' ),
			array( 'Diff\DiffOpChange' ),
			array( 'Diff\DiffOpRemove' ),

			array( 'Diff\CallbackListDiffer' ),
			array( 'Diff\Differ' ),
			array( 'Diff\ListDiffer' ),
			array( 'Diff\MapDiffer' ),
			array( 'Diff\OrderedListDiffer' ),

			array( 'Diff\ListPatcher' ),
			array( 'Diff\MapPatcher' ),
			array( 'Diff\Patcher' ),
			array( 'Diff\PatcherException' ),
			array( 'Diff\PreviewablePatcher' ),
			array( 'Diff\ThrowingPatcher' ),
		);
	}

}
