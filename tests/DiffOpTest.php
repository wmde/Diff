<?php

namespace Diff\Test;
use \Diff\IDiffOp as IDiffOp;

/**
 * Base test class for the Diff\DiffOp deriving classes.
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
abstract class DiffOpTest extends \AbstractTestCase {

	/**
	 * @dataProvider instanceProvider
	 */
	public function testIsAtomic( IDiffOp $diffOp ) {
		$this->assertInternalType( 'boolean', $diffOp->isAtomic() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetType( IDiffOp $diffOp ) {
		$this->assertInternalType( 'string', $diffOp->getType() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSerialization( IDiffOp $diffOp ) {
		$this->assertEquals( $diffOp, unserialize( serialize( $diffOp ) ) );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testCount( IDiffOp $diffOp ) {
		if ( $diffOp->isAtomic() ) {
			$this->assertEquals( 1, count( $diffOp ) );
		}
		else {
			$count = 0;

			foreach ( $diffOp as $childOp ) {
				$count += $childOp->count();
			}

			$this->assertEquals( $count, count( $diffOp ) );
		}
	}

}