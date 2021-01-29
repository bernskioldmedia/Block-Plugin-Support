<?php

namespace BernskioldMedia\WP\Block_Plugin_Support;

/**
 * Class Block
 *
 * @package BernskioldMedia\WP\BlockLibrary
 */
abstract class Block {

	/**
	 * An array of attributes that will be registered with
	 * the block in PHP.
	 * Each block that extends this class ought to set the
	 * attributes here too.
	 *
	 * @var array
	 */
	protected static $attributes = [];

	/**
	 * Get the full array of merged attributes.
	 * Call this method when registering the block
	 * to make the attributes available for use.
	 *
	 * @return array
	 */
	public static function get_attributes() {
		return static::$attributes;
	}

	/**
	 * Get Attribute Value
	 * Performs checks to see if the attribute is in the list
	 * of attributes given to the render function.
	 *
	 * If not, we default to its default if it exists,
	 * otherwise to null.
	 *
	 * @param  array   $attributes
	 * @param  string  $name
	 *
	 * @return mixed|null
	 */
	protected static function get_attr_value( $attributes, $name ) {
		return $attributes[ $name ] ?? null;
	}

	/**
	 * The main render function which is used as a callback
	 * in the class that's extending this, when registering the block.
	 *
	 * Automatically adds the blocks' content in the right place.
	 *
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	abstract public static function render( $attributes );
}
