<?php

namespace Diff\Test;
use Diff\Diff;
use Diff\DiffOpRemove;
use Diff\DiffOpAdd;
use Diff\DiffOpChange;
use Diff\DiffOp;

/**
 * Tests for the Diff\DiffOpFactory class.
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
 * @since 0.5
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group DiffOpFactory
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DiffOpFactoryTest extends \MediaWikiTestCase {

	public function diffOpProvider() {
		$diffOps = array();

		$diffOps[] = new DiffOpAdd( 42 );
		$diffOps['foo bar'] = new DiffOpAdd( '42' );
		$diffOps[9001] = new DiffOpAdd( 4.2 );
		$diffOps['42'] = new DiffOpAdd( array( 42, array( 9001 ) ) );
		$diffOps[] = new DiffOpRemove( 42 );

		$atomicDiffOps = $diffOps;

		foreach ( array( true, false, null ) as $isAssoc ) {
			$diffOps[] = new Diff( $atomicDiffOps, $isAssoc );
		}

		$diffOps[] = new DiffOpChange( 42, '9001' );

		$diffOps[] = new Diff( $diffOps );

		return $this->arrayWrap( $diffOps );
	}

	/**
	 * @dataProvider diffOpProvider
	 *
	 * @param \Diff\DiffOp $diffOp
	 */
	public function testNewFromArray( DiffOp $diffOp ) {
		$array = $diffOp->toArray();

		$factory = new \Diff\DiffOpFactory();

		$newInstance = $factory->newFromArray( $array );

		// If an equality method is implemented in DiffOp, it should be used here
		$this->assertEquals( $diffOp, $newInstance );
		$this->assertEquals( $diffOp->getType(), $newInstance->getType() );
	}

}
