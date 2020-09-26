<?php

namespace BernskioldMedia\WP\Block_Plugin_Support\Traits;

/**
 * Trait Has_Blocks
 *
 * Designed to extend a core plugin file that is a plugin
 * that provides blocks for output.
 *
 * @package BernskioldMedia\WP\Block_Plugin_Support\Traits
 */
trait Has_Blocks {

	/**
	 * Block Prefix
	 *
	 * @var string
	 */
	protected $block_prefix;

	/**
	 * An array of blocks in this plugin.
	 *
	 * @var array
	 */
	protected $blocks = [];

	/**
	 * Packages that are loaded as dependencies for the blocks globally,
	 * unless the block specifies its own dependency list.
	 *
	 * @var string[]
	 */
	protected static $default_block_dependencies = [
		'wp-blocks',
		'wp-components',
		'wp-element',
		'wp-i18n',
		'wp-block-editor',
	];

	/**
	 * Place the logic where you add blocks in here.
	 *
	 * @return mixed
	 */
	abstract public function blocks();

	/**
	 * Run this function when you initialize the plugin
	 * so that blocks are registered.
	 *
	 * @param  string  $prefix
	 */
	protected function load_blocks( $prefix = '' ) {

		if ( ! $this->block_prefix ) {
			$this->block_prefix = $prefix;
		}

		add_action( 'plugins_loaded', [ $this, 'blocks' ], static::get_load_priority() );
		add_action( 'init', [ $this, 'register_blocks' ], static::get_load_priority() );
		add_action( 'render_block_data', [ $this, 'add_block_name' ], 10, 1 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'load_block_assets' ], static::get_block_assets_load_priority() );

	}

	/**
	 * Add a block.
	 *
	 * @param  string  $name
	 * @param  array   $args
	 */
	public function add_block( $name, $args = [] ) {
		$this->blocks = array_merge( $this->blocks, [
			$name => $args,
		] );
	}

	/**
	 * Ad a block given that a class exists.
	 *
	 * @param  string  $class
	 * @param  string  $name
	 * @param  array   $args
	 */
	public function add_block_if( $class, $name, $args = [] ) {
		if ( class_exists( $class ) ) {
			$this->add_block( $name, $args );
		}
	}

	/**
	 * Remove a block.
	 *
	 * @param  string  $name
	 */
	public function remove_block( $name ) {
		unset( $this->blocks[ $name ] );
	}

	/**
	 * Load all block assets.
	 */
	public function load_block_assets() {
		foreach ( $this->get_blocks() as $block_name => $args ) {
			$dependencies = $args['script_dependencies'] ?? static::get_block_dependencies();

			wp_enqueue_script( $this->block_prefix . '-' . $block_name, static::get_url( 'dist/' . $block_name . '.js' ), $dependencies, static::get_version(), false );
			wp_set_script_translations( $this->block_prefix . '-' . $block_name, static::get_textdomain(), static::get_path( 'languages/' ) );
		}
	}

	/**
	 * Register the blocks
	 */
	public function register_blocks() {
		foreach ( $this->get_blocks() as $block_name => $args ) {
			register_block_type( $this->block_prefix . '/' . $block_name, $args );
		}
	}

	/**
	 * Add the block name to the block attributes
	 * so that we have it when rendering blocks via PHP.
	 *
	 * @param  array  $block
	 *
	 * @return array
	 */
	public function add_block_name( $block ) {
		$block['attrs']['_name'] = $block['blockName'];

		return $block;
	}

	/**
	 * Get Blocks
	 *
	 * @return array
	 */
	public function get_blocks(): array {
		return $this->blocks;
	}

	/**
	 * Control the priority of when this plugin hooks into
	 * the init and plugins_loaded hooks.
	 *
	 * @return integer
	 **/
	public static function get_load_priority(): int {
		if ( property_exists( static::class, 'load_priority' ) ) {
			return static::$load_priority;
		}

		return 99;
	}

	/**
	 * The priority of the block editor asset enqueue hook.
	 *
	 * @return integer
	 */
	public static function get_block_assets_load_priority(): int {
		if ( property_exists( static::class, 'block_assets_load_priority' ) ) {
			return static::$block_assets_load_priority;
		}

		return 99;
	}

	/**
	 * Get the default block dependencies.
	 *
	 * @return array
	 */
	public static function get_block_dependencies(): array {
		if ( property_exists( static::class, 'block_dependencies' ) ) {
			return array_merge( static::$block_dependencies, static::$default_block_dependencies );
		}

		return static::$default_block_dependencies;
	}

	/**
	 * Get the Plugin's Version
	 *
	 * @return string
	 */
	abstract public static function get_version();

	/**
	 * Get the URL to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	abstract public static function get_url( $file = '' );

	/**
	 * Get the path to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	abstract public static function get_path( $file = '' );

	/**
	 * Get Plugin Textdomain
	 *
	 * @return string
	 */
	abstract public static function get_textdomain();

}
