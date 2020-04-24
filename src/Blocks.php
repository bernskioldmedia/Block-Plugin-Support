<?php
/**
 *
 *  *
 * @package BernskioldMedia\WP\Block_Plugin_Support
 */

namespace BernskioldMedia\WP\Block_Plugin_Support;

use ReflectionException;

/**
 * Class Blocks
 *
 * @package BernskioldMedia\WP\Block_Plugin_Support
 */
class Blocks {



	public function get_blocks_url(): string {
		return untrailingslashit( $this->blocks_url );
	}

}
