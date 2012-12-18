<?php

namespace Diff\Test;
use Diff\DiffOpRemove as DiffOpRemove;

/**
 * Tests for the Diff\DiffOpRemove class.
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
 * @ingroup DiffTest
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpRemoveTest extends DiffOpTest {

	/**
	 * @see AbstractTestCase::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOpRemove';
	}

	/**
	 * @see AbstractTestCase::constructorProvider
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function constructorProvider() {
		return array(
			array( true, 'foo' ),
			array( true, array() ),
			array( true, true ),
			array( true, 42 ),
			array( false ),
		);
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetNewValue( DiffOpRemove $diffOp, array $constructorArgs ) {
		$this->assertEquals( $constructorArgs[0], $diffOp->getOldValue() );
	}

}
