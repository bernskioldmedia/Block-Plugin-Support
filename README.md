** THIS REPOSITORY WILL GO INTO READ-ONLY MODE **
We have integrated this composer package into our wp-plugin-base instead of keeping it separate.

# Block Plugin Support

We find ourselves increasingly creating a bunch of plugins that add one or more blocks to the block editor.

This requires a few functions to load and register. And since we use the same scaffold almost any time, it makes sense to split out the block part into a library.

The library consists of a simple trait for making the plugin support blocks and two abstract classes for a base server-side rendered block.

## Getting started

All you need to do is include the `Has_Blocks` trait in the plugin class as long as you are using the latest version of our plugin base, which recognizes this trait automatically.

Blocks are assumed to be placed in a `blocks` subfolder, and they are being built to `dist/blocks`.

**This trait relies on an autoloader being used in the plugin!**

```
use BernskioldMedia\WP\Block_Plugin_Support\Traits\Has_Blocks;

class My_Plugin {

    use Has_Blocks;

}
```

### Defining a custom block prefix
The trait will default to using the 'bm' prefix for blocks. Blocks are named with a prefix to scope them to the project they are a part of. This also influences how the script and style handles are registered ($prefix-block-$block_name).

Your project probably requires its custom prefix. You can set it on the main plugin class like so:

```
use BernskioldMedia\WP\Block_Plugin_Support\Traits\Has_Blocks;

class My_Plugin {

    use Has_Blocks;
    
    // Set my custom block prefix.
    protected static string $block_prefix = 'my-prefix';

}
```

## Required Folder Structure

The trait assumes the following folder structure:

`blocks/` is the location of all blocks with one folder per block. The `index.js` file in each block folder is the entrypoint for the build script.

`dist/blocks` is the location of built JavaScript blocks with the same filename as the main folder name.

`languages/` is the location of the translation files. The handle and domain are both set to `{$block_prefix}-{$block_name}`.

## Required Methods

The trait relies on two methods to be implemented.

- `get_url()` should return the URL to the plugin directory.
- `get_path()` should return the path to the plugin directory.
- `get_textdomain()` should return the string plugin textdomain.

## Hooks & Filters

We strive to make all code easily customizable with plenty of filters and hooks as needed. You never know when or why you might need that simple one-off customization.

Since this is a composer package, filters are housed inside of the "package namespace" and do not inherit the consumer plugin's own naming structure.

## Filters

`bm_block_support_{$BLOCKNAME}_wrapper_args`. Available for any dynamic block that extends `Block` and is reliant on `Block::$name` being set in the consuming dynamic block class.
Allows you to customize the args sent to `get_block_wrapper_attributes`.
