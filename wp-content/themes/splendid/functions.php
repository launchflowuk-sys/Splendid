<?php
/**
 * Splendid theme bootstrap.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

define( 'SPLENDID_VERSION', '1.0.0' );
define( 'SPLENDID_DIR', get_template_directory() );
define( 'SPLENDID_URI', get_template_directory_uri() );

require_once SPLENDID_DIR . '/inc/settings.php';
require_once SPLENDID_DIR . '/inc/icons.php';
require_once SPLENDID_DIR . '/inc/nav.php';
require_once SPLENDID_DIR . '/inc/template-tags.php';
require_once SPLENDID_DIR . '/inc/patterns.php';

/**
 * Theme setup.
 */
function splendid_setup() {
	load_theme_textdomain( 'splendid', SPLENDID_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	add_editor_style( array( 'assets/css/design.css', 'assets/css/wp.css', 'assets/css/editor.css' ) );

	register_nav_menus( array(
		'primary'         => __( 'Main navigation (desktop)', 'splendid' ),
		'mobile'          => __( 'Mobile drawer', 'splendid' ),
		'footer_windows'  => __( 'Footer: Windows', 'splendid' ),
		'footer_doors'    => __( 'Footer: Doors & more', 'splendid' ),
		'footer_discover' => __( 'Footer: Discover Splendid', 'splendid' ),
		'footer_legal'    => __( 'Footer: legal row', 'splendid' ),
	) );
}
add_action( 'after_setup_theme', 'splendid_setup' );

/**
 * Front-end assets.
 */
function splendid_assets() {
	$design = SPLENDID_DIR . '/assets/css/design.css';
	$wpcss  = SPLENDID_DIR . '/assets/css/wp.css';
	$js     = SPLENDID_DIR . '/assets/js/site.js';

	wp_enqueue_style( 'splendid-design', SPLENDID_URI . '/assets/css/design.css', array(), file_exists( $design ) ? filemtime( $design ) : SPLENDID_VERSION );
	wp_enqueue_style( 'splendid-wp', SPLENDID_URI . '/assets/css/wp.css', array( 'splendid-design' ), file_exists( $wpcss ) ? filemtime( $wpcss ) : SPLENDID_VERSION );

	wp_enqueue_script( 'splendid-site', SPLENDID_URI . '/assets/js/site.js', array(), file_exists( $js ) ? filemtime( $js ) : SPLENDID_VERSION, true );

	// style.css carries only theme metadata; it is not enqueued on the front end.
}
add_action( 'wp_enqueue_scripts', 'splendid_assets' );

/**
 * Preload the two local font files so first paint uses the real faces.
 */
function splendid_preload_fonts() {
	foreach ( array( 'sans.ttf', 'serif.ttf' ) as $file ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/ttf" crossorigin="anonymous">' . "\n",
			esc_url( SPLENDID_URI . '/assets/fonts/' . $file )
		);
	}
	printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( SPLENDID_URI . '/assets/images/favicon.svg' ) );
}
add_action( 'wp_head', 'splendid_preload_fonts', 1 );

/**
 * The sticky header must clear the admin bar rather than sit under it.
 */
function splendid_admin_bar_offset() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	?>
	<style id="splendid-admin-bar">
		.header { top: 32px; }
		html { scroll-padding-top: 142px; }
		.mobile-sheet { top: 32px; }
		@media screen and (max-width: 782px) {
			.header { top: 46px; }
			html { scroll-padding-top: 156px; }
			.mobile-sheet { top: 46px; }
		}
	</style>
	<?php
}
add_action( 'wp_head', 'splendid_admin_bar_offset', 20 );

/**
 * Body classes that let the CSS target the design's page families.
 *
 * @param array $classes Body classes.
 * @return array
 */
function splendid_body_class( $classes ) {
	if ( is_singular() ) {
		$template = get_post_meta( get_the_ID(), '_splendid_template', true );
		if ( $template ) {
			$classes[] = 'splendid-template-' . sanitize_html_class( $template );
		}
	}

	if ( is_front_page() ) {
		$classes[] = 'splendid-home';
	}

	return $classes;
}
add_filter( 'body_class', 'splendid_body_class' );

/**
 * Register the page-template meta so the importer and editor can set it.
 */
function splendid_register_meta() {
	register_post_meta( 'page', '_splendid_template', array(
		'type'          => 'string',
		'single'        => true,
		'show_in_rest'  => true,
		'auth_callback' => static function () {
			return current_user_can( 'edit_pages' );
		},
	) );
}
add_action( 'init', 'splendid_register_meta' );

/**
 * Keep the editor canvas close to the front end.
 */
function splendid_editor_assets() {
	wp_enqueue_style( 'splendid-editor-fonts', SPLENDID_URI . '/assets/css/design.css', array(), SPLENDID_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'splendid_editor_assets' );

/**
 * Content width used by embeds.
 */
function splendid_content_width() {
	$GLOBALS['content_width'] = 1180;
}
add_action( 'after_setup_theme', 'splendid_content_width', 0 );
