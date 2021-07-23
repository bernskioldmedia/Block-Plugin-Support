<?php

namespace BernskioldMedia\WP\Block_Plugin_Support\Traits;

/**
 * Trait Has_Blocks
 *
 * Designed to extend a core plugin file that is a plugin
 * that provides blocks for output.
 *
 * @property array $conditional_blocks An array of blocks that are only loaded if the corresponding class name exists. blockname => classname.
 * @property array $dynamic_blocks An array of blocks that are dynamic, meaning they use PHP to load on the frontend. blockname => classname.
 *
 * @package BernskioldMedia\WP\Block_Plugin_Support\Traits
 */
trait Has_Blocks {

	/**
	 * An array of blocks in this plugin.
	 */
	protected array $blocks = [];

	/**
	 * Get all of the blocks in the blocks folder and register them.
	 */
	protected function load_blocks(): void {
		$blocks = glob( static::get_path() . 'blocks/*' );

		$this->blocks = apply_filters( static::$slug . '_blocks', $blocks );
	}

	public function register_blocks(): void {
		foreach ( $this->blocks as $directory ) {
			$parts = explode( '/', $directory );
			$name  = end( $parts );

			// If this is a conditional block and condition not met, then skip loading.
			if ( isset( static::$conditional_blocks[ $name ] ) && ! class_exists( static::$conditional_blocks[ $name ] ) ) {
				continue;
			}

			// Get the asset file data.
			$asset_meta = include static::get_path() . 'dist/blocks/' . $name . '.asset.php';

			// Register the script with WordPress.
			wp_register_script( 'bm-block-' . $name, static::get_url( 'dist/blocks/' . $name . '.js' ), $asset_meta['dependencies'], $asset_meta['version'], true );

			// Register the block. Dynamic blocks get their callback.
			if ( isset( static::$dynamic_blocks[ $name ] ) ) {
				register_block_type( $directory, [
					'render_callback' => [ static::$dynamic_blocks[ $name ], 'render' ],
				] );
			} else {
				register_block_type( $directory );
			}
		}
	}

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
}
