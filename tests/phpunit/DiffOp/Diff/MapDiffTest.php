<?php

namespace Diff\Tests\DiffOp\Diff;

use Diff\Differ\ListDiffer;
use Diff\Differ\MapDiffer;
use Diff\DiffOp\Diff\Diff;
use Diff\DiffOp\DiffOp;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Diff\Tests\DiffOp\DiffOpTest;

/**
 * @covers Diff\DiffOp\Diff\MapDiff
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapDiffTest extends DiffOpTest {

	/**
	 * @see DiffOpTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return '\Diff\DiffOp\Diff\MapDiff';
	}

	/**
	 * @see DiffOpTest::constructorProvider
	 *
	 * @since 0.1
	 */
	public function constructorProvider() {
		$operationLists = array();

		$operationLists[] = array();

		$operationLists[] = array(
			new DiffOpAdd( 42 ),
		);

		$operationLists[] = array(
			new DiffOpAdd( 42 ),
			new DiffOpChange( 9000, 9001 ),
		);

		$operationLists[] = array(
			new DiffOpAdd( 42 ),
			new DiffOpRemove( 1 ),
			new DiffOpChange( 9000, 9001 ),
			new DiffOpChange( 5, 1 ),
			new Diff( array(
				new DiffOpAdd( 42 ),
				new DiffOpRemove( 1 ),
			), false ),
			new Diff( array(
				new DiffOpAdd( 42 ),
				new DiffOpRemove( 1 ),
				new DiffOpChange( 9000, 9001 ),
			), true ),
		);

		$argLists = array();

		foreach ( $operationLists as $operationList ) {
			$argLists[] = array( true, $operationList );
			$argLists[] = array( true, $operationList, 'foobar' );
		}

		$argLists[] = array( false, 42 );
		$argLists[] = array( false, new DiffOpAdd( 42 ) );
		$argLists[] = array( false, '~=[,,_,,]:3' );

		return $argLists;
	}

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
					'de' => array( 'a' => 'hax' ),
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
		$mapDiffer = new MapDiffer( true );
		$listDiffer = new ListDiffer();

		$diff = new Diff( $mapDiffer->doDiff( $from, $to ) );

		foreach ( $expected as $key => &$value ) {
			if ( $value === 'list' || $value === 'map' ) {
				$differ = $value === 'list' ? $listDiffer : $mapDiffer;

				$value = new Diff(
					$differ->doDiff(
						array_key_exists( $key, $from ) ? $from[$key] : array(),
						array_key_exists( $key, $to ) ? $to[$key] : array()
					),
					$value === 'map'
				);
			}
		}

		$sorter = function( $a, $b ) {
			$aa = serialize( $a );
			$bb = serialize( $b );

			if ( $aa == $bb ) {
				return 0;
			}
			else {
				return $aa > $bb ? -1 : 1;
			}
		};

		uasort( $expected, $sorter );
		$diff->uasort( $sorter );
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
		$differ = new MapDiffer( true );
		$diff = new Diff( $differ->doDiff( $from, $to ) );

		$this->assertInstanceOf( '\Diff\DiffOp\DiffOp', $diff );
		$this->assertInstanceOf( '\Diff\DiffOp\Diff\Diff', $diff );
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

	public function elementInstancesProvider() {
		return array(
			array( array(
			) ),
			array( array(
				new DiffOpAdd( 'ohi' )
			) ),
			array( array(
				new DiffOpRemove( 'ohi' )
			) ),
			array( array(
				new DiffOpAdd( 'ohi' ),
				new DiffOpRemove( 'there' )
			) ),
			array( array(
			) ),
			array( array(
				new DiffOpAdd( 'ohi' ),
				new DiffOpRemove( 'there' ),
				new DiffOpChange( 'ohi', 'there' )
			) ),
			array( array(
				'1' => new DiffOpAdd( 'ohi' ),
				'33' => new DiffOpRemove( 'there' ),
				'7' => new DiffOpChange( 'ohi', 'there' )
			) ),
		);
	}

	/**
	 * @dataProvider elementInstancesProvider
	 */
	public function testGetAdditions( array $operations ) {
		$diff = new Diff( $operations, true );

		$changes = array();

		/**
		 * @var DiffOp $operation
		 */
		foreach ( $operations as $operation ) {
			if ( $operation->getType() == 'change' ) {
				$changes[] = $operation;
			}
		}

		$this->assertArrayEquals( $changes, $diff->getChanges() );
	}

	public function testElementsInRecuriveDiff() {
		$old = array(
			'en' => array( 'en-foo', 'en-bar' ),
			'de' => array( 'de-0', 'de-1' ),
			'onoez' => array( '~=[,,_,,]:3' ),
			'a' => 'b',
		);

		$new = array(
			'en' => array( 'en-foo', 'en-baz' ),
			'nl' => array( 'nl-0', 'nl-1' ),
			'onoez' => array( '~=[,,_,,]:3' ),
			'a' => 'b',
		);

		$differ = new MapDiffer( true );
		$diff = new Diff( $differ->doDiff( $old, $new ) );

		$this->assertTrue( $diff->offsetExists( 'en' ) );
		$this->assertTrue( $diff->offsetExists( 'de' ) );
		$this->assertTrue( $diff->offsetExists( 'nl' ) );
		$this->assertFalse( $diff->offsetExists( 'onoez' ) );
		$this->assertFalse( $diff->offsetExists( 'a' ) );

		$this->assertInstanceOf( 'Diff\DiffOp\Diff\Diff', $diff['de'] );
		$this->assertInstanceOf( 'Diff\DiffOp\Diff\Diff', $diff['nl'] );
		$this->assertInstanceOf( 'Diff\DiffOp\Diff\Diff', $diff['en'] );

		$this->assertSame( 2, count( $diff['de'] ) );
		$this->assertSame( 2, count( $diff['nl'] ) );
		$this->assertSame( 2, count( $diff['en'] ) );

		/**
		 * @var Diff $listDiff
		 */
		$listDiff = $diff['en'];

		$add = $listDiff->getAdditions();
		$add = array_shift( $add );
		$this->assertEquals( 'en-baz', $add->getNewValue() );

		$remove = $listDiff->getRemovals();
		$remove = array_shift( $remove );
		$this->assertEquals( 'en-bar', $remove->getOldValue() );
	}

	public function testEmptyElementsInRecursiveDiff() {
		$old = array(
			'en' => array( 'a' => 'en-foo', 'b' => 'en-bar' ),
		);

		$new = array(
			'en' => array( 'a' => 'en-foo', 'b' => 'en-bar' ),
		);

		$differ = new MapDiffer( true );
		$diff = new Diff( $differ->doDiff( $old, $new ) );

		$this->assertTrue( $diff->isEmpty() );
		$this->assertTrue( $diff->getOperations() === array() );
	}

}
