<?php

namespace Diff\Patcher;

use Diff\Differ\ListDiffer;
use Diff\Differ\MapDiffer;
use Diff\DiffOp\Diff\Diff;

/**
 * Base class for patchers that have the ability to throw errors
 * when they encounter diff operations they can not handle or
 * ignore them if specified.
 *
 * @since 0.4
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class ThrowingPatcher implements PreviewablePatcher {

	/**
	 * @var bool
	 */
	private $throwErrors;

	/**
	 * @since 0.4
	 *
	 * @param bool $throwErrors
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
	 * @throws PatcherException
	 */
	public function getApplicableDiff( array $base, Diff $diff ) {
		$throwErrors = $this->throwErrors;
		$this->throwErrors = false;

		$patched = $this->patch( $base, $diff );

		$this->throwErrors = $throwErrors;

		$treatAsMap = $diff->looksAssociative();

		$differ = $treatAsMap ? new MapDiffer( true ) : new ListDiffer();

		$diffOps = $differ->doDiff( $base, $patched );

		$diff = new Diff( $diffOps, $treatAsMap );

		return $diff;
	}

}
