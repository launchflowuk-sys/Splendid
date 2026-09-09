<?php
/**
 * 404 template. Returns a real HTTP 404 status.
 *
 * The source used a "navy" button class that no longer exists in the green
 * design; the approved green button class is used instead.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-heading">
	<p class="eyebrow"><?php esc_html_e( '404 / A DIFFERENT VIEW', 'splendid' ); ?></p>
	<h1><?php echo wp_kses_post( __( 'Let&rsquo;s find your<br><em>way home.</em>', 'splendid' ) ); ?></h1>
	<p><?php esc_html_e( 'We couldn&rsquo;t find that page. Explore our windows, doors and ideas from the homepage.', 'splendid' ); ?></p>
	<?php
	splendid_link( array(
		'label' => __( 'Back to Splendid', 'splendid' ),
		'url'   => '/',
		'class' => 'button brand',
		'icon'  => '',
	) );
	?>
</section>

<?php
get_footer();
