<?php

namespace Diff\Tests;

/**
 * @licence GNU GPL v2+
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
		return array_map(
			function( $className ) {
				return array( $className );
			},
			array(
				'Diff\Diff',
				'Diff\MapDiff',
				'Diff\ListDiff',

				'Diff\AtomicDiffOp',
				'Diff\DiffOp',
				'Diff\DiffOpAdd',
				'Diff\DiffOpChange',
				'Diff\DiffOpRemove',

				'Diff\CallbackListDiffer',
				'Diff\Differ',
				'Diff\ListDiffer',
				'Diff\MapDiffer',
				'Diff\OrderedListDiffer',

				'Diff\ListPatcher',
				'Diff\MapPatcher',
				'Diff\Patcher',
				'Diff\PatcherException',
				'Diff\PreviewablePatcher',
				'Diff\ThrowingPatcher',
			)
		);

	}

}
