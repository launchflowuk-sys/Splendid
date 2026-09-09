<?php
/**
 * Shared markup helpers for the shell.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

/**
 * The name of the menu assigned to a location, for use as a footer heading.
 *
 * @param string $location Menu location.
 * @param string $default  Fallback heading.
 * @return string
 */
function splendid_menu_name( $location, $default ) {
	$assigned = get_nav_menu_locations();

	if ( ! empty( $assigned[ $location ] ) ) {
		$menu = wp_get_nav_menu_object( $assigned[ $location ] );

		if ( $menu && ! empty( $menu->name ) ) {
			return $menu->name;
		}
	}

	return $default;
}

/**
 * The wordmark.
 *
 * Uses a Custom Logo when one is set (Appearance -> Customize -> Site Identity),
 * otherwise the approved text treatment. The text treatment is a proposed
 * wordmark, not a verified pre-existing business logo.
 */
function splendid_brand() {
	$label = sprintf( '%s home', splendid_option( 'company_name' ) );

	if ( has_custom_logo() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand brand-image" aria-label="<?php echo esc_attr( $label ); ?>">
			<?php echo wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => esc_attr( splendid_option( 'company_name' ) ) ) ); ?>
		</a>
		<?php
		return;
	}
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand" aria-label="<?php echo esc_attr( $label ); ?>">
		<span class="brand-main">splendid<span aria-hidden="true">&middot;</span></span>
		<span class="brand-sub">DOUBLE GLAZING</span>
	</a>
	<?php
}

/**
 * The deep green strip above the header.
 */
function splendid_topbar() {
	?>
	<div class="topbar">
		<span><?php echo esc_html( splendid_option( 'topbar_tagline' ) ); ?></span>
		<span>
			<?php echo splendid_icon( 'map-pin', 12 ); // phpcs:ignore ?>
			<?php echo esc_html( splendid_option( 'topbar_area' ) ); ?>
		</span>
		<a href="<?php echo esc_url( splendid_tel_href() ); ?>">
			<?php echo splendid_icon( 'phone', 12 ); // phpcs:ignore ?>
			<?php echo esc_html( splendid_option( 'phone' ) ); ?>
		</a>
	</div>
	<?php
}

/**
 * A link styled as one of the design's buttons, with its trailing icon.
 *
 * @param array $args label, url, class, icon, size, attrs.
 */
function splendid_link( $args ) {
	$args = wp_parse_args( $args, array(
		'label' => '',
		'url'   => '#',
		'class' => 'button brand',
		'icon'  => 'arrow-up-right',
		'size'  => 18,
		'attrs' => '',
	) );

	printf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s %5$s</a>',
		esc_attr( $args['class'] ),
		esc_url( splendid_url( $args['url'] ) ),
		$args['attrs'], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_kses_post( $args['label'] ),
		$args['icon'] ? splendid_icon( $args['icon'], $args['size'] ) : '' // phpcs:ignore
	);
}

/**
 * The shared closing call-to-action band.
 *
 * Rendered by the splendid/cta-band block so editors can change the copy; this
 * function holds the markup both the block and any template fallback use.
 *
 * @param array $atts Block attributes.
 * @return string
 */
function splendid_cta_band_markup( $atts = array() ) {
	$atts = wp_parse_args( $atts, array(
		'eyebrow'  => 'LET&rsquo;S MAKE IT SPLENDID',
		'heading'  => 'Your home.<br><em>With a fresh perspective.</em>',
		'text'     => 'A single window or a complete transformation.<br>Tell us what you have in mind.',
		'ctaLabel' => 'Start your project',
		'ctaUrl'   => '/free-quote',
	) );

	ob_start();
	?>
	<section class="cta-band reveal">
		<div>
			<p class="eyebrow"><?php echo wp_kses_post( $atts['eyebrow'] ); ?></p>
			<h2><?php echo wp_kses_post( $atts['heading'] ); ?></h2>
		</div>
		<div>
			<p><?php echo wp_kses_post( $atts['text'] ); ?></p>
			<?php
			splendid_link( array(
				'label' => $atts['ctaLabel'],
				'url'   => $atts['ctaUrl'],
				'class' => 'button brand',
				'size'  => 19,
			) );
			?>
			<a class="cta-call" href="<?php echo esc_url( splendid_tel_href() ); ?>">
				<?php
				/* translators: %s: telephone number. */
				echo esc_html( sprintf( __( 'Or call %s', 'splendid' ), splendid_option( 'phone' ) ) );
				?>
			</a>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}
