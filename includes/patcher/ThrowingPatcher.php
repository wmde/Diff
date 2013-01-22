<?php

namespace Diff;

/**
 * Base class for patchers that have the ability to throw errors
 * when they encounter diff operations they can not handle or
 * ignore them if specified.
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
 * @since 0.4
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class ThrowingPatcher implements PreviewablePatcher {

	/**
	 * @since 0.4
	 *
	 * @var boolean
	 */
	protected $throwErrors;

	/**
	 * @since 0.4
	 *
	 * @param boolean $throwErrors
	 */
	public function __construct( $throwErrors = false ) {
		$this->throwErrors = $throwErrors;
	}

	/**
	 * @since 0.4
	 *
	 * @param string $message
	 *
	 * @throws PatcherException
	 */
	protected function handleError( $message ) {
		if ( $this->throwErrors ) {
			throw new PatcherException( $message );
		}
	}

	/**
	 * Set the patcher to ignore errors.
	 *
	 * @since 0.4
	 */
	public function ignoreErrors() {
		$this->throwErrors = false;
	}

	/**
	 * Set the patcher to throw errors.
	 *
	 * @since 0.4
	 */
	public function throwErrors() {
		$this->throwErrors = true;
	}

	/**
	 * @see PreviewablePatcher::getApplicableDiff
	 *
	 * @since 0.4
	 *
	 * @param array $base
	 * @param Diff $diff
	 *
	 * @return Diff
	 */
	public function getApplicableDiff( array $base, Diff $diff ) {
		if ( function_exists( 'wfProfileIn' ) ) {
			wfProfileIn( __METHOD__ );
		}

		$throwErrors = $this->throwErrors;
		$this->throwErrors = false;

		$patched = $this->patch( $base, $diff );

		$this->throwErrors = $throwErrors;

		$treatAsMap = $diff->looksAssociative();

		$differ = $treatAsMap ? new MapDiffer( true ) : new ListDiffer();

		$diffOps = $differ->doDiff( $base, $patched );

		$diff = new Diff( $diffOps, $treatAsMap );

		if ( function_exists( 'wfProfileOut' ) ) {
			wfProfileOut( __METHOD__ );
		}

		return $diff;
	}

}