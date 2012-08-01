<?php

namespace Diff\Test;
use Diff\DiffOp as DiffOp;

/**
 * Tests for the Diff\DiffOp class.
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
 *
 * @group Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpTest extends \MediaWikiTestCase {

	public function newFromArrayProvider() {
		return array(
			array( 'add', 'foo' ),
			array( 'remove', 'bar' ),
			array( 'change', 'foo', 'bar' ),
			array( 'add', 42 ),
			array( 'remove', true ),
			array( 'change', array(), null ),
			array( 'list', array() ),
			array( 'map', array() ),
		);
	}

	/**
	 * @dataProvider newFromArrayProvider
	 */
	public function testNewFromArray() {
		$array = func_get_args();

		$diffOp = DiffOp::newFromArray( $array );

		$this->assertInstanceOf( '\Diff\IDiffOp', $diffOp );
		$this->assertEquals( array_shift( $array ), $diffOp->getType() );
	}

}