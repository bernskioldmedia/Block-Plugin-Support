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

### Customize Dependencies

Each block requires a list of script dependencies when loaded. These are typically the core WordPress block library scripts,
or any other script dependency that we need.

To customize this, add a `dependencies => []` section to the block arguments array when adding the block. A standard block would then look like this:

```
$this->add_block( 'my-js-block-name', [
    'dependencies' => [
        'wp-i18n'
    ],
] );
```

If you do not set dependencies for a specific block, the global dependency list will be used.

By default, the global dependency list contains a most-used set of WP scripts. To modify it, you can ovverride the `protected static $block_dependencies = []` class property.

```
/**
 * Packages that are loaded as dependencies for the blocks globally,
 * unless the block specifies its own dependency list.
 *
 * @var string[]
 */
protected static $block_dependencies = [
	'wp-blocks',
	'wp-components',
	'wp-element',
	'wp-i18n',
	'wp-block-editor',
];
```

# Required Folder Structure

The trait assumes the following folder structure:

`dist/` is the location of built JavaScript blocks, named with the same name as you add the block with using `add_block()`.

`languages/` is the location of the translation files. The handle and domain are both set to `{$block_prefix}-{$block_name}`.

# Required Methods

The trait relies on four methods to be implemented, outside of `blocks()`. These are:

-   `get_version()` that should return a string of the current plugin version. Used for versioning the blocks.
-   `get_url()` should return the URL to the plugin directory.
-   `get_path()` should return the path to the plugin directory.
-   `get_textdomain()` should return the plugin textdomain.

# Hooks & Filters

We strive to make all code easily customizable with plenty of filters and hooks as needed. You never know when or why you might need that simple one-off customization.

Since this is a composer package, filters are housed inside of the "package namespace" and do not inherit the consumer plugin's own naming structure.

## Filters

`bm_block_support_{$BLOCKNAME}_attributes`. Available for any dynamic block that extends `Block` and is reliant on `Block::$name` being set in the consuming dynamic block class. Allows you to customize the specific block attributes.

`bm_block_support_{$BLOCKNAME}_wrapper_args`. Available for any dynamic block that extends `Block` and is reliant on `Block::$name` being set in the consuming dynamic block class. Allows you to customize the args sent to `get_block_wrapper_attributes`.
