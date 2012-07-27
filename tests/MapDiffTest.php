<?php

namespace Diff\Test;
use Diff\MapDiff as MapDiff;
use Diff\ListDiff as ListDiff;
use Diff\DiffOpRemove as DiffOpRemove;
use Diff\DiffOpAdd as DiffOpAdd;
use Diff\DiffOpChange as DiffOpChange;

/**
 * Tests for the Diff\MapDiff class.
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
class MapDiffTest extends \MediaWikiTestCase {

	public function recursionProvider() {
		return array_merge( $this->newFromArraysProvider(), array(
			array(
				array(
					'en' => array( 1, 2, 3 ),
				),
				array(
					'en' => array( 4, 2 ),
				),
				array(
					'en' => 'list',
				),
			),
			array(
				array(
					'en' => array( 1, 2, 3 ),
					'nl' => array( 'hax' ),
					'foo' => 'bar',
				),
				array(
					'en' => array( 4, 2 ),
					'de' => array( 'hax' ),
				),
				array(
					'en' => 'list',
					'de' => 'list',
					'nl' => 'list',
					'foo' => new DiffOpRemove( 'bar' ),
				),
			),
			array(
				array(
					'en' => array( 'a' => 1, 'b' => 2, 'c' => 3 ),
					'nl' => array( 'a' => 'hax' ),
					'foo' => 'bar',
					'bar' => array( 1, 2, 3 ),
				),
				array(
					'bar' => array( 4, 2 ),
					'en' => array( 'd' => 4, 'b' => 2 ),
					'de' => array( 'a' =>'hax' ),
				),
				array(
					'en' => 'map',
					'de' => 'map',
					'nl' => 'map',
					'foo' => new DiffOpRemove( 'bar' ),
					'bar' => 'list',
				),
			),
		) );
	}

	/**
	 * @dataProvider recursionProvider
	 */
	public function testRecursion( array $from, array $to, $expected ) {
		$diff = MapDiff::newFromArrays( $from, $to, true );

		foreach ( $expected as $key => &$value ) {
			if ( $value === 'list' ) {
				$value = ListDiff::newFromArrays(
					array_key_exists( $key, $from ) ? $from[$key] : array(),
					array_key_exists( $key, $to ) ? $to[$key] : array()
				);
			}
			elseif ( $value === 'map' ) {
				$value = MapDiff::newFromArrays(
					array_key_exists( $key, $from ) ? $from[$key] : array(),
					array_key_exists( $key, $to ) ? $to[$key] : array()
				);
			}
		}

		asort( $expected );
		$diff->asort();
		$actual = $diff->getArrayCopy();

		$this->assertEquals( $expected, $actual );
	}

	public function newFromArraysProvider() {
		return array(
			array(
				array(),
				array(),
				array(),
			),
			array(
				array( 'en' => 'en' ),
				array(),
				array(
					'en' => new DiffOpRemove( 'en' )
				),
			),
			array(
				array(),
				array( 'en' => 'en' ),
				array(
					'en' => new DiffOpAdd( 'en' )
				)
			),
			array(
				array( 'en' => 'foo' ),
				array( 'en' => 'en' ),
				array(
					'en' => new DiffOpChange( 'foo', 'en' )
				),
			),
			array(
				array( 'en' => 'foo' ),
				array( 'en' => 'foo', 'de' => 'bar' ),
				array(
					'de' => new DiffOpAdd( 'bar' )
				)
			),
			array(
				array( 'en' => 'foo' ),
				array( 'en' => 'baz', 'de' => 'bar' ),
				array(
					'de' => new DiffOpAdd( 'bar' ),
					'en' => new DiffOpChange( 'foo', 'baz' )
				)
			),
		);
	}

	/**
	 * @dataProvider newFromArraysProvider
	 */
	public function testNewFromArrays( array $from, array $to, array $expected ) {
		$diff = MapDiff::newFromArrays( $from, $to );

		$this->assertInstanceOf( '\Diff\MapDiff', $diff );
		$this->assertInstanceOf( '\Diff\IDiffOp', $diff );
		$this->assertInstanceOf( '\Diff\IDiff', $diff );
		$this->assertInstanceOf( '\ArrayObject', $diff );

		// Sort to get rid of differences in order, since no promises about order are made.
		asort( $expected );
		$diff->asort();
		$actual = $diff->getArrayCopy();

		$this->assertEquals( $expected, $actual );

		$this->assertEquals(
			$actual === array(),
			$diff->isEmpty()
		);
	}

}
	
