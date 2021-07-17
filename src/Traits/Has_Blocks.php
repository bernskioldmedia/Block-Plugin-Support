<?php

namespace BernskioldMedia\WP\Block_Plugin_Support\Traits;

use Exception;

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
	 * An array of blocks in this plugin.
	 */
	protected array $blocks = [];

	/**
	 * Packages that are loaded as dependencies for the blocks globally,
	 * unless the block specifies its own dependency list.
	 *
	 * @var string[]
	 */
	protected static array $default_block_dependencies = [
		'wp-blocks',
		'wp-components',
		'wp-element',
		'wp-i18n',
		'wp-block-editor',
	];

	/**
	 * Place the logic where you add blocks in here.
	 */
	abstract public function blocks(): void;

	/**
	 * Run this function when you initialize the plugin
	 * so that blocks are registered.
	 */
	protected function load_blocks(): void {
		if ( ! property_exists( static::class, 'block_prefix' ) ) {
			throw new Exception( 'block_prefix is not set.' );
		}

		add_action( 'plugins_loaded', [ $this, 'blocks' ], static::get_load_priority() );
		add_action( 'init', [ $this, 'register_blocks' ], static::get_load_priority() );
		add_action( 'render_block_data', [ $this, 'add_block_name' ], 10, 1 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'load_block_assets' ], static::get_block_assets_load_priority() );
	}

	/**
	 * Add a block.
	 *
	 * @return static
	 */
	protected function add_block( string $name, array $args = [] ) {
		$this->blocks = array_merge( $this->blocks, [
			$name => $args,
		] );

		return $this;
	}

	/**
	 * Ad a block given that a class exists.
	 *
	 * @return static
	 */
	protected function add_block_if( string $class, string $name, array $args = [] ) {
		if ( class_exists( $class ) ) {
			$this->add_block( $name, $args );
		}

		return $this;
	}

	/**
	 * Remove a block.
	 *
	 * @return static
	 */
	protected function remove_block( string $name ) {
		unset( $this->blocks[ $name ] );

		return $this;
	}

	/**
	 * Load all block assets.
	 */
	public function load_block_assets(): void {
		foreach ( $this->get_blocks() as $block_name => $args ) {
			$dependencies = $args['script_dependencies'] ?? static::get_block_dependencies();

			wp_enqueue_script( static::$block_prefix . '-' . $block_name, static::get_url( 'dist/' . $block_name . '.js' ), $dependencies, static::get_version(), false );
			wp_set_script_translations( static::$block_prefix . '-' . $block_name, static::get_textdomain(), static::get_path( 'languages/' ) );
		}
	}

	/**
	 * Register the blocks
	 */
	public function register_blocks(): void {
		foreach ( $this->get_blocks() as $block_name => $args ) {
			register_block_type( static::$block_prefix . '/' . $block_name, $args );
		}
	}

	/**
	 * Add the block name to the block attributes
	 * so that we have it when rendering blocks via PHP.
	 */
	public function add_block_name( array $block ): array {
		$block['attrs']['_name'] = $block['blockName'];

		return $block;
	}

	/**
	 * Get Blocks
	 */
	public function get_blocks(): array {
		return $this->blocks;
	}

	/**
	 * Control the priority of when this plugin hooks into
	 * the init and plugins_loaded hooks.
	 **/
	public static function get_load_priority(): int {
		if ( property_exists( static::class, 'load_priority' ) ) {
			return static::$load_priority;
		}

		return 99;
	}

	/**
	 * The priority of the block editor asset enqueue hook.
	 */
	public static function get_block_assets_load_priority(): int {
		if ( property_exists( static::class, 'block_assets_load_priority' ) ) {
			return static::$block_assets_load_priority;
		}

		return 99;
	}

	/**
	 * Get the default block dependencies.
	 */
	public static function get_block_dependencies(): array {
		if ( property_exists( static::class, 'block_dependencies' ) ) {
			return array_merge( static::$block_dependencies, static::$default_block_dependencies );
		}

		return static::$default_block_dependencies;
	}

	/**
	 * Get the Plugin's Version
	 */
	abstract public static function get_version(): string;

	/**
	 * Get the URL to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 */
	abstract public static function get_url( string $file = '' ): string;

	/**
	 * Get the path to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 */
	abstract public static function get_path( string $file = '' ): string;

	/**
	 * Get Plugin Textdomain
	 */
	abstract public static function get_textdomain(): string;

}
