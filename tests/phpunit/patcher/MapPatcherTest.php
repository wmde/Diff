<?php

namespace Diff\Tests;

use Diff\Diff;
use Diff\DiffOpAdd;
use Diff\DiffOpChange;
use Diff\DiffOpRemove;
use Diff\MapPatcher;
use Diff\Patcher;

/**
 * Tests for the Diff\MapPatcher class.
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
class MapPatcherTest extends DiffTestCase {

	public function patchProvider() {
		$argLists = array();

		$patcher = new MapPatcher();
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

		$diff = new Diff( array(), true );
		$currentObject = array();
		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Empty diff should remain empty on empty base' );


		$diff = new Diff( array(), true );

		$currentObject = array( 'foo' => 0, 'bar' => 1 );

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Empty diff should remain empty on non-empty base' );


		$diff = new Diff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );

		$currentObject = array( 'foo' => 0, 'bar' => 1 );

		$expected = clone $diff;

		$argLists[] = array( $diff, $currentObject, $expected, 'Diff should not be altered on matching base' );


		$diff = new Diff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );
		$currentObject = array();

		$expected = new Diff( array(), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Diff with only change ops should be empty on empty base' );


		$diff = new Diff( array(
			'foo' => new DiffOpChange( 0, 42 ),
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );

		$currentObject = array( 'foo' => 'something else', 'bar' => 1, 'baz' => 'o_O' );

		$expected = new Diff( array(
			'bar' => new DiffOpChange( 1, 9001 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Only change ops present in the base should be retained' );


		$diff = new Diff( array(
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array();

		$expected = new Diff( array(), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Remove ops should be removed on empty base' );


		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array( 'foo' => 'bar' );

		$expected = new Diff( array(), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Mismatching add ops and remove ops not present in base should be removed' );


		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array( 'foo' => 42, 'bar' => 9001 );

		$expected = new Diff( array(
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Remove ops present in base should be retained' );


		$diff = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
			'bar' => new DiffOpRemove( 9001 ),
		), true );

		$currentObject = array();

		$expected = new Diff( array(
			'foo' => new DiffOpAdd( 42 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Add ops not present in the base should be retained (MapDiff)' );


		$diff = new Diff( array(
			'foo' => new Diff( array( 'bar' => new DiffOpChange( 0, 1 ) ), true ),
			'le-non-existing-element' => new Diff( array( 'bar' => new DiffOpChange( 0, 1 ) ), true ),
			'spam' => new Diff( array( new DiffOpAdd( 42 ) ), false ),
			new DiffOpAdd( 9001 ),
		), true );

		$currentObject = array(
			'foo' => array( 'bar' => 0, 'baz' => 'O_o' ),
			'spam' => array( 23, 'ohi' )
		);

		$expected = new Diff( array(
			'foo' => new Diff( array( 'bar' => new DiffOpChange( 0, 1 ) ), true ),
			'spam' => new Diff( array( new DiffOpAdd( 42 ) ), false ),
			new DiffOpAdd( 9001 ),
		), true );

		$argLists[] = array( $diff, $currentObject, $expected, 'Recursion should work properly' );

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
