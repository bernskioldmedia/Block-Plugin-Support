# Block Plugin Support
We find ourselves increasingly creating a bunch of plugins that add one or more blocks
to the block editor.

This requires a few functions to load and register. And since we use the same scaffold
almost any time, it makes sense to split out the block part into a library.

The library consists of a simple trait for making the plugin support blocks and an abstract class
for a base server-side rendered block.

## Getting started
All you need to do is include the `Has_Blocks` trait in the plugin class, add the loader
with your custom prefix when initializing the plugin, and finally, add
the blocks by implementing the `blocks` method.

**This trait relies on an autoloader being used in the plugin!**

```
use BernskioldMedia\WP\Block_Plugin_Support\Traits\Has_Blocks;

class My_Plugin {

    use Has_Blocks;

    public function __construct() {

        // Load blocks.
        $this->load_blocks( 'my-block-prefix' );

    }

    public function blocks() {

        // Full JS only block.
        $this->add_block( 'my-js-block-name' );

        // Server side rendered block, class extends abstract "Block" class.
        $this->add_block( 'my-server-side-rendered-block', [
        	'render_callback' => [ My_Block::class, 'render' ],
        	'attributes'      => My_Block::get_attributes(),
        ] );

        // Load block conditionally on class being loaded.
        $this->add_block_if( 'My_Class', 'my-js-block' );

    }

}
```

### Customize Loading Priority
We run our block enqueue fairly late by default at a 999 priority on `enqueue_block_editor_assets`. To customize this order,
you may override the following static variable on the class:

```
/**
 * The priority of the block editor asset enqueue hook.
 *
 * @var integer
 */
protected static $block_assets_load_priority = 999;
```

# Required Folder Structure
The trait assumes the following folder structure:

`dist/` is the location of built JavaScript blocks, named with the same name as you add the block with using `add_block()`.

`languages/` is the location of the translation files. The handle and domain are both set to `{$block_prefix}-{$block_name}`.

# Required Methods
The trait relies on three methods to be implemented, outside of `blocks()`. These are:

- `get_version()` that should return a string of the current plugin version. Used for versioning the blocks.
- `get_url()` should return the URL to the plugin directory.
- `get_path()` should return the path to the plugin directory.
