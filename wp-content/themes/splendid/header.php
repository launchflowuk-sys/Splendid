<?php
/**
 * Site header: skip link, top strip, sticky header, mobile drawer.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'splendid' ); ?></a>

<?php splendid_topbar(); ?>

<header class="header">
	<?php splendid_brand(); ?>

	<?php splendid_render_desktop_nav(); ?>

	<a class="button forest header-cta" href="<?php echo esc_url( splendid_url( splendid_option( 'header_cta_url' ) ) ); ?>">
		<span class="header-cta-full"><?php echo wp_kses_post( splendid_option( 'header_cta_text' ) ); ?></span>
		<span class="header-cta-short"><?php echo wp_kses_post( splendid_option( 'header_cta_mobile' ) ); ?></span>
		<?php echo splendid_icon( 'arrow-up-right', 17 ); // phpcs:ignore ?>
	</a>

	<button type="button"
		class="mobile-menu"
		data-splendid-open
		aria-label="<?php esc_attr_e( 'Open navigation', 'splendid' ); ?>"
		aria-expanded="false"
		aria-controls="splendid-mobile-nav">
		<?php echo splendid_icon( 'menu', 24 ); // phpcs:ignore ?>
	</button>
</header>

<div class="mobile-overlay" data-splendid-close hidden></div>
<?php splendid_render_mobile_nav(); ?>

<main id="main">
