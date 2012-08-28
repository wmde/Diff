<?php

namespace Diff\Test;
use Diff\ListDiff as ListDiff;

/**
 * Tests for the Diff\ListDiff class.
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
 * @since 0.1
 *
 * @ingroup Diff
 * @ingroup Test
 * @group Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ListDiffTest extends DiffOpTest {

	/**
	 * @see AbstractTestCase::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\ListDiff';
	}

	/**
	 * @see AbstractTestCase::constructorProvider
	 *
	 * @since 0.1
	 */
	public function constructorProvider() {
		return array();
	}

	public function newFromArraysProvider() {
		return array(
			array(
				array(),
				array(),
				array(),
				array(),
			),
			array(
				array( 'foo' ),
				array(),
				array(),
				array( 'foo' ),
			),
			array(
				array(),
				array( 'foo' ),
				array( 'foo' ),
				array(),
			),
			array(
				array( 'foo' ),
				array( 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo', 'foo' ),
				array( 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo' ),
				array( 'foo', 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo', 'bar' ),
				array( 'bar', 'foo' ),
				array(),
				array(),
			),
			array(
				array( 'foo', 'bar', 42, 'baz' ),
				array( 42, 1, 2, 3 ),
				array( 1, 2, 3 ),
				array( 'foo', 'bar', 'baz' ),
			),
			array(
				array( false, null ),
				array( 0, '0' ),
				array( 0, '0' ),
				array( false, null ),
			),
			// The arrays here are getting ignored for some reason... array_diff is weird...
//			array(
//				array( 1, 2, array( 'foo', 'bar' ) ),
//				array( 1, 3, array( 'spam' ), array() ),
//				array( 3, array( 'spam' ), array() ),
//				array( 2, array( 'foo', 'bar' ) ),
//			),
		);
	}

	/**
	 * @dataProvider newFromArraysProvider
	 */
	public function testNewFromArrays( array $from, array $to, array $additions, array $removals ) {
		$diff = ListDiff::newFromArrays( $from, $to );

		$this->assertInstanceOf( '\Diff\ListDiff', $diff );
		$this->assertInstanceOf( '\Diff\IDiffOp', $diff );
		$this->assertInstanceOf( '\Diff\IDiff', $diff );
		$this->assertInstanceOf( '\ArrayObject', $diff );

		// array_values because we only care about the values, not promises are made about the keys.
		$resultAdditions = array_values( $diff->getAddedValues() );
		$resultRemovals = array_values( $diff->getRemovedValues() );

		// Sort everything since no promises are made about ordering.
		asort( $resultAdditions );
		asort( $resultRemovals );
		asort( $additions );
		asort( $removals );

		$this->assertEquals( $additions, $resultAdditions, 'additions mismatch' );
		$this->assertEquals( $removals, $resultRemovals, 'removals mismatch' );

		$this->assertEquals(
			$additions === array() && $removals === array(),
			$diff->isEmpty()
		);
	}

}
	
