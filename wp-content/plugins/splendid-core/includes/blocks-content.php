<?php
/**
 * Small content blocks that keep the design's exact markup while staying editable.
 *
 * Core blocks wrap images in <figure> and cannot produce an anchor that contains
 * other blocks, both of which the approved CSS depends on. These three blocks
 * emit the original markup and expose every value in the editor sidebar.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the content blocks.
 */
function splendid_content_blocks_register() {
	register_block_type( 'splendid/link', array(
		'api_version'     => 3,
		'editor_script'   => 'splendid-blocks-editor',
		'render_callback' => 'splendid_block_link',
		'supports'        => array( 'html' => false ),
		'attributes'      => array(
			'label'     => array( 'type' => 'string', 'default' => 'Read more' ),
			'url'       => array( 'type' => 'string', 'default' => '/' ),
			'className' => array( 'type' => 'string', 'default' => 'text-link' ),
			'icon'      => array( 'type' => 'string', 'default' => 'arrow-up-right' ),
			'size'      => array( 'type' => 'number', 'default' => 18 ),
			'ariaLabel' => array( 'type' => 'string', 'default' => '' ),
			'external'  => array( 'type' => 'boolean', 'default' => false ),
		),
	) );

	register_block_type( 'splendid/image', array(
		'api_version'     => 3,
		'editor_script'   => 'splendid-blocks-editor',
		'render_callback' => 'splendid_block_image',
		'supports'        => array( 'html' => false ),
		'attributes'      => array(
			'id'       => array( 'type' => 'number', 'default' => 0 ),
			'src'      => array( 'type' => 'string', 'default' => '' ),
			'alt'      => array( 'type' => 'string', 'default' => '' ),
			'priority' => array( 'type' => 'boolean', 'default' => false ),
		),
	) );

	register_block_type( 'splendid/image-card', array(
		'api_version'     => 3,
		'editor_script'   => 'splendid-blocks-editor',
		'render_callback' => 'splendid_block_image_card',
		'supports'        => array( 'html' => false ),
		'attributes'      => array(
			'title'  => array( 'type' => 'string', 'default' => '' ),
			'tag'    => array( 'type' => 'string', 'default' => '' ),
			'href'   => array( 'type' => 'string', 'default' => '/' ),
			'id'     => array( 'type' => 'number', 'default' => 0 ),
			'src'    => array( 'type' => 'string', 'default' => '' ),
			'alt'    => array( 'type' => 'string', 'default' => '' ),
			'number' => array( 'type' => 'string', 'default' => '' ),
		),
	) );

	register_block_type( 'splendid/benefits', array(
		'api_version'     => 3,
		'editor_script'   => 'splendid-blocks-editor',
		'render_callback' => 'splendid_block_benefits',
		'supports'        => array( 'html' => false ),
		'attributes'      => array(
			'items' => array(
				'type'    => 'array',
				'default' => array(
					array( 'icon' => 'ruler', 'label' => 'Made for your home' ),
					array( 'icon' => 'sun', 'label' => 'Light-filled living' ),
					array( 'icon' => 'layers', 'label' => 'Considered materials' ),
					array( 'icon' => 'map-pin', 'label' => 'Your local specialists' ),
				),
			),
		),
	) );
}
add_action( 'init', 'splendid_content_blocks_register' );

/**
 * A link or button with the design's trailing icon.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_link( $atts ) {
	$label = isset( $atts['label'] ) ? $atts['label'] : '';
	$url   = isset( $atts['url'] ) ? $atts['url'] : '/';
	$class = isset( $atts['className'] ) ? $atts['className'] : 'text-link';
	$icon  = isset( $atts['icon'] ) ? $atts['icon'] : 'arrow-up-right';
	$size  = isset( $atts['size'] ) ? (int) $atts['size'] : 18;

	$attributes = '';

	if ( ! empty( $atts['ariaLabel'] ) ) {
		$attributes .= sprintf( ' aria-label="%s"', esc_attr( $atts['ariaLabel'] ) );
	}

	if ( ! empty( $atts['external'] ) ) {
		$attributes .= ' target="_blank" rel="noopener noreferrer"';
	}

	// A tel: or mailto: link keeps the live business value from settings.
	if ( 'tel:' === $url ) {
		$url   = splendid_tel_href();
		$label = '' !== $label ? $label : splendid_option( 'phone' );
	} elseif ( 'mailto:' === $url ) {
		$url   = 'mailto:' . splendid_option( 'email' );
		$label = '' !== $label ? $label : splendid_option( 'email' );
	} elseif ( 'reviews:' === $url ) {
		// Kept in settings so a verified review profile replaces the search link
		// in one place, on every page that links to it.
		$url         = splendid_option( 'reviews_url' );
		$attributes .= ' target="_blank" rel="noopener noreferrer"';
	} elseif ( 'maps:' === $url ) {
		$url         = splendid_option( 'maps_url' );
		$attributes .= ' target="_blank" rel="noopener noreferrer"';
	}

	return sprintf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s%5$s</a>',
		esc_attr( $class ),
		esc_url( splendid_url( $url ) ),
		$attributes,
		wp_kses_post( $label ),
		$icon ? ' ' . splendid_icon( $icon, $size ) : ''
	);
}

/**
 * A bare <img>, exactly as the design's CSS expects.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_image( $atts ) {
	$id  = isset( $atts['id'] ) ? (int) $atts['id'] : 0;
	$alt = isset( $atts['alt'] ) ? $atts['alt'] : '';

	$loading = ! empty( $atts['priority'] ) ? 'eager' : 'lazy';
	$extra   = ! empty( $atts['priority'] ) ? ' fetchpriority="high" decoding="async"' : ' decoding="async"';

	if ( $id ) {
		$src    = wp_get_attachment_image_url( $id, 'full' );
		$srcset = wp_get_attachment_image_srcset( $id, 'full' );
		$sizes  = wp_get_attachment_image_sizes( $id, 'full' );
		$meta   = wp_get_attachment_metadata( $id );
		$width  = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
		$height = isset( $meta['height'] ) ? (int) $meta['height'] : 0;
	} else {
		$src    = isset( $atts['src'] ) ? $atts['src'] : '';
		$srcset = '';
		$sizes  = '';
		$width  = 0;
		$height = 0;
	}

	if ( ! $src ) {
		return '<p class="splendid-block-placeholder">' . esc_html__( 'Choose an image.', 'splendid-core' ) . '</p>';
	}

	return sprintf(
		'<img src="%1$s" alt="%2$s"%3$s%4$s%5$s%6$s loading="%7$s"%8$s>',
		esc_url( $src ),
		esc_attr( $alt ),
		$width ? ' width="' . (int) $width . '"' : '',
		$height ? ' height="' . (int) $height . '"' : '',
		$srcset ? ' srcset="' . esc_attr( $srcset ) . '"' : '',
		$sizes ? ' sizes="' . esc_attr( $sizes ) . '"' : '',
		esc_attr( $loading ),
		$extra
	);
}

/**
 * A photographic collection card: an anchor wrapping an image and caption.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_image_card( $atts ) {
	$image = splendid_block_image( array(
		'id'  => isset( $atts['id'] ) ? $atts['id'] : 0,
		'src' => isset( $atts['src'] ) ? $atts['src'] : '',
		'alt' => isset( $atts['alt'] ) ? $atts['alt'] : '',
	) );

	ob_start();
	?>
	<a class="image-card" href="<?php echo esc_url( splendid_url( isset( $atts['href'] ) ? $atts['href'] : '/' ) ); ?>">
		<div class="card-photo">
			<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above. ?>
			<?php if ( ! empty( $atts['number'] ) ) : ?>
				<span class="card-number"><?php echo esc_html( $atts['number'] ); ?></span>
			<?php endif; ?>
			<span class="round-arrow"><?php echo splendid_icon( 'arrow-up-right', 22 ); // phpcs:ignore ?></span>
		</div>
		<div class="card-caption">
			<h3><?php echo esc_html( isset( $atts['title'] ) ? $atts['title'] : '' ); ?></h3>
			<span><?php echo esc_html( isset( $atts['tag'] ) ? $atts['tag'] : '' ); ?></span>
		</div>
	</a>
	<?php
	return (string) ob_get_clean();
}

/**
 * The four-item benefit strip.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_benefits( $atts ) {
	$items = ! empty( $atts['items'] ) && is_array( $atts['items'] ) ? $atts['items'] : array();

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<div class="benefits-bar">
		<?php foreach ( $items as $item ) : ?>
			<div>
				<?php echo splendid_icon( isset( $item['icon'] ) ? $item['icon'] : 'check', 24 ); // phpcs:ignore ?>
				<span><?php echo esc_html( isset( $item['label'] ) ? $item['label'] : '' ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
