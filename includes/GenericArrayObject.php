<?php

namespace Diff;

/**
 * Extends ArrayObject and does two things:
 *
 * Allows for deriving classes to easily intercept additions
 * and deletions for purposes such as additional indexing.
 *
 * Enforces the objects to be of a certain type, so this
 * can be replied upon, much like if this had true support
 * for generics, which sadly enough is not possible in PHP.
 *
 * @since 0.1
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class GenericArrayObject extends \ArrayObject {

	/**
	 * Returns the name of an interface/class that the element should implement/extend.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public abstract function getObjectType();

	/**
	 * @see SiteList::getNewOffset()
	 * @since 0.1
	 * @var integer
	 */
	protected $indexOffset = 0;

	/**
	 * Finds a new offset for when appending an element.
	 * The base class does this, so it would be better to integrate,
	 * but there does not appear to be any way to do this...
	 *
	 * @since 0.1
	 *
	 * @return integer
	 */
	protected function getNewOffset() {
		while ( true ) {
			if ( !$this->offsetExists( $this->indexOffset ) ) {
				return $this->indexOffset;
			}

			$this->indexOffset++;
		}
	}

	/**
	 * Constructor.
	 * @see ArrayObject::__construct
	 *
	 * @since 0.1
	 *
	 * @param null|array $input
	 * @param int $flags
	 * @param string $iterator_class
	 */
	public function __construct( $input = null, $flags = 0, $iterator_class = 'ArrayIterator' ) {
		parent::__construct( array(), $flags, $iterator_class );

		if ( !is_null( $input ) ) {
			foreach ( $input as $offset => $value ) {
				$this->offsetSet( $offset, $value );
			}
		}
	}

	/**
	 * @see ArrayObject::append
	 *
	 * @since 0.1
	 *
	 * @param mixed $value
	 */
	public function append( $value ) {
		$this->setElement( null, $value );
	}

	/**
	 * @see ArrayObject::offsetSet()
	 *
	 * @since 0.1
	 *
	 * @param mixed $index
	 * @param mixed $value
	 *
	 * @throws \MWException
	 */
	public function offsetSet( $index, $value ) {
		$this->setElement( $index, $value );
	}

	/**
	 * Returns if the provided value has the same type as the elements
	 * that can be added to this ArrayObject.
	 *
	 * @since 0.1
	 *
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	protected function hasValidType( $value ) {
		$class = $this->getObjectType();
		return $value instanceof $class;
	}

	/**
	 * Method that actually sets the element and holds
	 * all common code needed for set operations, including
	 * type checking and offset resolving.
	 *
	 * If you want to do additional indexing or have code that
	 * otherwise needs to be executed whenever an element is added,
	 * you can overload @see preSetElement.
	 *
	 * @since 0.1
	 *
	 * @param mixed $index
	 * @param mixed $value
	 *
	 * @throws \MWException
	 */
	protected function setElement( $index, $value ) {
		if ( !$this->hasValidType( $value ) ) {
			throw new \MWException(
				'Can only add ' . $this->getObjectType() . ' implementing objects to ' . get_called_class() . '.'
			);
		}

		if ( is_null( $index ) ) {
			$index = $this->getNewOffset();
		}

		if ( $this->preSetElement( $index, $value ) ) {
			parent::offsetSet( $index, $value );
		}
	}

	/**
	 * Gets called before a new element is added to the ArrayObject.
	 *
	 * At this point the index is always set (ie not null) and the
	 * value is always of the type returned by @see getObjectType.
	 *
	 * Should return a boolean. When false is returned the element
	 * does not get added to the ArrayObject.
	 *
	 * @since 0.1
	 *
	 * @param integer|string $index
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	protected function preSetElement( $index, $value ) {
		return true;
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize( $this->getSerializationData() );
	}

	/**
	 * Returns an array holding all the data that should go into serialization calls.
	 * This is intended to allow overloading without having to reimplement the
	 * behaviour of this base class.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	protected function getSerializationData() {
		return array(
			'data' => $this->getArrayCopy(),
			'index' => $this->indexOffset,
		);
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $serialization
	 *
	 * @return array
	 */
	public function unserialize( $serialization ) {
		$serializationData = unserialize( $serialization );

		foreach ( $serializationData['data'] as $offset => $value ) {
			// Just set the element, bypassing checks and offset resolving,
			// as these elements have already gone through this.
			parent::offsetSet( $offset, $value );
		}

		$this->indexOffset = $serializationData['index'];

		return $serializationData;
	}

	/**
	 * Returns if the ArrayObject has no elements.
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		return $this->count() === 0;
	}

}
