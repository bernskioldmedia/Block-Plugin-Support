<?php
/**
 * Section
 */

namespace BernskioldMedia\WP\Block_Plugin_Support;

/**
 * Class Section
 *
 * @package BernskioldMedia\WP\BlockLibrary
 */
abstract class Section extends Block {

	/**
	 * An array of the "default" Section attributes, both
	 * for the Section, its header and footer.
	 *
	 * @var array
	 */
	protected static $section_attributes = [
		'align'                 => [
			'type'    => 'string',
			'default' => 'full',
		],
		'anchor'                    => [
			'type' => 'string',
		],
		'sectionWrapperEnabled'		=> [
			'type' => 'boolean',
			'default' => true,
		],
		'backgroundImageId'         => [
			'type' => 'string',
		],
		'backgroundImageUrl'        => [
			'type' => 'string',
		],
		'backgroundImageFocalPoint' => [
			'type'    => 'object',
			'default' => [
				'x' => 0.5,
				'y' => 0.5,
			],
		],
		'backgroundImageDimensions' => [
			'type'    => 'object',
			'default' => [
				'width'  => 0,
				'height' => 0,
			],
		],
		'isSectionFullHeight'       => [
			'type'    => 'boolean',
			'default' => false,
		],
		'sectionContentWidth'       => [
			'type'    => 'string',
			'default' => 'page-width',
		],
		'sectionVerticalSpacing'    => [
			'type'    => 'string',
			'default' => 'normal',
		],
		'sectionHeaderShow'         => [
			'type'    => 'boolean',
			'default' => false,
		],
		'sectionHeaderStyle'        => [
			'type'    => 'text',
			'default' => 'normal',
		],
		'sectionEyebrow'            => [
			'type' => 'string',
		],
		'sectionTitle'              => [
			'type' => 'string',
		],
		'sectionSubtitle'           => [
			'type' => 'string',
		],
		'sectionCtaShow'            => [
			'type'    => 'boolean',
			'default' => false,
		],
		'sectionCtaText'            => [
			'type' => 'string',
		],
		'sectionCtaLink'            => [
			'type' => 'string',
		],
		'sectionFooterShow'         => [
			'type'    => 'boolean',
			'default' => false,
		],
		'sectionFooterAlignment'    => [
			'type'    => 'string',
			'default' => 'center',
		],
		'sectionFooterText'         => [
			'type' => 'string',
		],
		'sectionFooterCtaShow'      => [
			'type'    => 'boolean',
			'default' => false,
		],
		'sectionFooterCtaText'      => [
			'type' => 'string',
		],
		'sectionFooterCtaLink'      => [
			'type' => 'string',
		],

	];

	/**
	 * Get the full array of merged attributes.
	 * Call this method when registering the block
	 * to make the attributes available for use.
	 *
	 * @return array
	 */
	public static function get_attributes() {
		$attributes = array_merge( static::$section_attributes, static::$attributes );

		return apply_filters( 'bm_block_support_' . static::$name . '_attributes', $attributes );
	}

	/**
	 * Blocks that extend the Section must implement this method
	 * in order to set the Section content.
	 * The method should echo the content.
	 *
	 * @param  array  $attributes
	 *
	 * @return void
	 */
	abstract protected static function content( $attributes );

	/**
	 * Checks before the rendering starts. If this returns false,
	 * the block will be hidden. Useful to hide dynamic blocks
	 * if no content is present, for example.
	 *
	 * @return bool
	 */
	protected static function is_content_shown( $attributes ): bool {
		return true;
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
	public static function render( $attributes ) {

		if( false === static::is_content_shown( $attributes ) ) {
			return '';
		}

		$classes = [];
		$args = [];

		if ( isset( $attributes['anchor'] ) ) {
			$args['id'] = esc_attr( $attributes['anchor'] );
		}

		if ( isset( $attributes['align'] ) ) {
			$classes[] = 'align' . $attributes['align'];
		}

		if ( isset( $attributes['displayAsCarousel'] ) && $attributes['displayAsCarousel'] ) {
			$classes[] = 'has-carousel';
		}

		if ( ! static::get_attr_value( $attributes, 'sectionWrapperEnabled' ) ) {
			$args['class'] = implode( ' ', $classes );
			$wrapper_attributes = static::get_block_wrapper_attributes( $attributes, $args );

			ob_start(); ?>
			<div <?php echo $wrapper_attributes; ?>>
				<?php static::content( $attributes ); ?>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Set up the Section classes.
		 */
		$classes[] = 'section';

		if ( isset( $attributes['backgroundImageUrl'] ) ) {
			$classes[] = 'has-background-image bg-cover';
		}

		if ( isset( $attributes['isSectionFullHeight'] ) && $attributes['isSectionFullHeight'] ) {
			$classes[] = 'is-full-height';
		}

		if ( isset( $attributes['sectionHeaderShow'] ) && $attributes['sectionHeaderShow'] ) {
			$classes[] = 'has-header';
		}

		if ( isset( $attributes['sectionFooterShow'] ) && $attributes['sectionFooterShow'] ) {
			$classes[] = 'has-footer';
		}

		if ( isset( $attributes['sectionContentWidth'] ) ) {
			$classes[] = 'has-' . $attributes['sectionContentWidth'] . '-content';
		}

		if ( isset( $attributes['sectionVerticalSpacing'] ) ) {
			$classes[] = 'has-' . $attributes['sectionVerticalSpacing'] . '-vspacing';
		}

		/**
		 * Set up the Section styles.
		 */
		$styles = [];

		if ( isset( $attributes['backgroundImageUrl'] ) ) {
			$styles[] = 'background-image: url(' . esc_url( $attributes['backgroundImageUrl'] ) . ');';

			$styles[] = 'background-position: ' . static::focalpoint_to_background_position( $attributes['backgroundImageFocalPoint'] ) . ';';
		}

		$args = [
			'class' => implode( ' ', $classes ),
			'style' => implode( ' ', $styles ),
		];

		$wrapper_attributes = static::get_block_wrapper_attributes( $attributes, $args );

		ob_start();
		?>
		<section <?php echo $wrapper_attributes; ?>>
			<?php
			static::render_section_header( $attributes );
			?>
			<div class="section-body">
				<?php static::content( $attributes ); ?>
			</div>
			<?php
			static::render_section_footer( $attributes );
			?>
		</section>

		<?php
		return ob_get_clean();
	}

	/**
	 * Convert focal point to background position.
	 *
	 * @param  array  $focal_point
	 *
	 * @return string
	 */
	protected static function focalpoint_to_background_position( $focal_point ) {
		$x = $focal_point['x'] * 100;
		$y = $focal_point['y'] * 100;

		return "$x% $y%";
	}

	/**
	 * Render the Section header.
	 *
	 * @param  array  $attributes
	 */
	protected static function render_section_header( $attributes ) {

		if ( ! static::get_attr_value( $attributes, 'sectionHeaderShow' ) ) {
			return;
		}

		$classes   = [ 'section-header' ];
		$classes[] = 'is-style-' . static::get_attr_value( $attributes, 'sectionHeaderStyle' );
		$classes   = join( ' ', $classes );

		?>
		<header class="<?php echo esc_attr( $classes ); ?>">

			<div class="section-header-content">
				<?php
				$eyebrow = static::get_attr_value( $attributes, 'sectionEyebrow' );

				if ( $eyebrow ) : ?>
					<p class="section-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php
				$title = static::get_attr_value( $attributes, 'sectionTitle' );

				if ( $title ) : ?>
					<h2 class="section-title"><?php echo $title; // @codingStandardsIgnoreLine ?></h2>
				<?php endif; ?>

				<?php
				$subtitle = static::get_attr_value( $attributes, 'sectionSubtitle' );

				if ( $subtitle ) : ?>
					<p class="section-subtitle"><?php echo $subtitle; // @codingStandardsIgnoreLine ?></p>
				<?php endif; ?>
			</div>

			<?php
			$section_cta_show = static::get_attr_value( $attributes, 'sectionCtaShow' );

			if ( $section_cta_show ) :
				?>
				<p class="section-cta">
					<a class="section-cta-link text-button" href="<?php echo esc_url( static::get_attr_value( $attributes,
						'sectionCtaLink' ) ); ?>"><?php echo esc_html( static::get_attr_value( $attributes, 'sectionCtaText' ) ); ?></a>
				</p>
			<?php endif; ?>

		</header>
		<?php
	}

	/**
	 * Render the Section footer.
	 *
	 * @param  array  $attributes
	 */
	protected static function render_section_footer( $attributes ) {

		if ( ! static::get_attr_value( $attributes, 'sectionFooterShow' ) ) {
			return;
		}

		$classes   = [ 'section-footer' ];
		$classes[] = 'is-' . static::get_attr_value( $attributes, 'sectionFooterAlignment' ) . '-aligned';
		$classes   = join( ' ', $classes );

		?>
		<footer class="<?php echo esc_attr( $classes ); ?>">
			<div class="section-footer-content">

				<?php
				$section_footer_text = static::get_attr_value( $attributes, 'sectionFooterText' );

				if ( $section_footer_text ) : ?>
					<p class="section-footer-text"><?php echo $section_footer_text; // @codingStandardsIgnoreLine ?></p>
				<?php endif; ?>

				<?php
				$section_footer_cta_show = static::get_attr_value( $attributes, 'sectionFooterCtaShow' );

				if ( $section_footer_cta_show ) :
					?>
					<div class="section-footer-cta">
						<a class="section-footer-cta-button button" href="<?php echo esc_url( static::get_attr_value( $attributes,
							'sectionFooterCtaLink' ) ); ?>"><?php echo esc_html( static::get_attr_value( $attributes, 'sectionFooterCtaText' ) ); ?></a>
					</div>
				<?php endif; ?>

			</div>
		</footer>
		<?php
	}

}
