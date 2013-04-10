<?php

namespace Diff\Tests;

use Diff\Diff;
use Diff\DiffOpAdd;
use Diff\DiffOpRemove;
use Diff\MapPatcher;
use Diff\Patcher;

/**
 * Tests for the Diff\ListPatcher class.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.4
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group DiffPatcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListPatcherTest extends DiffTestCase {

	public function patchProvider() {
		$argLists = array();

		$patcher = new \Diff\ListPatcher();
		$base = array();
		$diff = new Diff();
		$expected = array();

		$argLists[] = array( $patcher, $base, $diff, $expected );

		// TODO

		return $argLists;
	}

	/**
	 * @dataProvider patchProvider
	 *
	 * @param Patcher $patcher
	 * @param array $base
	 * @param Diff $diff
	 * @param array $expected
	 */
	public function testPatch( Patcher $patcher, array $base, Diff $diff, array $expected ) {
		$actual = $patcher->patch( $base, $diff );

		$this->assertArrayEquals( $actual, $expected );
	}

	public function getApplicableDiffProvider() {
		// Diff, current object, expected
		$argLists = array();

		$diff = new Diff( array(), false );
		$currentObject = array();
		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Empty diff should remain empty on empty base' );


		$diff = new Diff( array(), false );

		$currentObject = array( 'foo' => 0, 'bar' => 1 );

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Empty diff should remain empty on non-empty base' );


		$diff = new Diff( array(
			new DiffOpRemove( 9001 ),
		), false );

		$currentObject = array();

		$expected = new Diff( array(), false );

		$argLists[] = array( $diff, $currentObject, $expected, 'Remove ops should be removed on empty base' );


		$diff = new Diff( array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		), false );

		$currentObject = array();

		$expected = new Diff( array(
			new DiffOpAdd( 42 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Add ops not present in the base should be retained (ListDiff)' );


		$diff = new Diff( array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		), false );

		$currentObject = array( 1, 42, 9001 );

		$expected = new Diff( array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 9001 ),
		), false );

		$argLists[] = array( $diff, $currentObject, $expected, 'Add ops with values present in the base should be retained in ListDiff' );


		$diff = new Diff( array(
			'en' => new Diff( array( new DiffOpAdd( 42 ) ), false ),
		), true );

		$currentObject = array();

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'list diffs containing only add ops should be retained even when not in the base' );


		$diff = new Diff( array(
			'en' => new Diff( array( new DiffOpRemove( 42 ) ), false ),
		), true );

		$currentObject = array(
			'en' => array( 42 ),
		);

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'list diffs containing only remove ops should be retained when present in the base' );

		return $argLists;
	}

	/**
	 * @dataProvider getApplicableDiffProvider
	 *
	 * @param Diff $diff
	 * @param array $currentObject
	 * @param Diff $expected
	 * @param string|null $message
	 */
	public function testGetApplicableDiff( Diff $diff, array $currentObject, Diff $expected, $message = null ) {
		$patcher = new MapPatcher();
		$actual = $patcher->getApplicableDiff( $currentObject, $diff );

		$this->assertEquals( $expected->getOperations(), $actual->getOperations(), $message );
	}

}
