<?php

namespace BernskioldMedia\WP\Block_Plugin_Support\Traits;

use ReflectionException;

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
	 * An array of components class names that are required
	 * by the blocks.
	 *
	 * @var array
	 */
	protected $block_components = [];

	abstract public function blocks();

	public function load_blocks() {

		if ( ! $this->block_prefix ) {
			return;
		}

		$this->register_blocks();
		$this->load_components();
		$this->load_php_blocks();
	}

	public function set_block_prefix( $prefix ) {
		$this->block_prefix = $prefix;

	}

	public function add_block( $name, $args = [] ) {
		$this->blocks = array_merge( $this->blocks, [
			$name => $args,
		] );
	}

	public function add_block_if( $plugin_file_name, $name, $args = [] ) {

		if ( ! $this->is_plugin_active( $plugin_file_name ) ) {
			return $this;
		}

		$this->add_block( $name, $args );
	}

	public function remove_block( $name ) {
		unset( $this->blocks[ $name ] );
	}

	public function block_components( $components ): self {
		$this->block_components = $components;

		return $this;
	}

	public function load_assets() {

		foreach ( $this->get_blocks() as $block_name => $args ) {

			wp_enqueue_script( 'bm-block-' . $block_name, self::get_url( 'dist/' . $block_name . '.js' ), [
				'wp-blocks',
				'wp-element',
				'wp-i18n',
				'wp-editor',
			], self::get_version(), true );

			wp_set_script_translations( 'bm-block-' . $block_name, 'bm-block-' . $block_name, self::get_path( 'languages/' ) );

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
	 * Load Components
	 */
	protected function load_components() {

		foreach ( $this->components as $component ) {
			try {
				$class = new \ReflectionClass( $component );
				require_once $class->getFileName();
			} catch ( ReflectionException $e ) {
				error_log( $e->getMessage() );
			}

		}

	}

	protected function load_php_blocks() {

		foreach ( $this->get_blocks() as $block => $args ) {

			if ( ! isset( $args['render_callback'] ) ) {
				continue;
			}

			try {
				$class = new \ReflectionClass( $args['render_callback'][0] );
				require_once $class->getFileName();
			} catch ( ReflectionException $e ) {
				error_log( $e->getMessage() );
			}

		}

	}

	/**
	 * Check if a plugin is active.
	 *
	 * @param  string  $plugin_file
	 *
	 * @return bool
	 */
	protected function is_plugin_active( $plugin_file ): bool {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		return in_array( $plugin_file, $active_plugins, true );
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
	 * Get the Plugin's Version
	 *
	 * @return string
	 */
	abstract public function get_version();

	/**
	 * Get the URL to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	abstract public function get_url( $file = '' );

	/**
	 * Get the path to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	abstract public function get_path( $file = '' );

}
