<?php
/**
 * Archive template.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-heading">
	<p class="eyebrow"><?php esc_html_e( 'ADVICE &amp; IDEAS', 'splendid' ); ?></p>
	<h1><?php the_archive_title(); ?></h1>
	<?php the_archive_description(); ?>
</section>

<section class="section reveal">
	<div class="product-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<a class="product-card" href="<?php the_permalink(); ?>">
				<span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
				<h3><?php the_title(); ?></h3>
				<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
				<span class="text-link"><?php esc_html_e( 'Read the article', 'splendid' ); ?> <?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?></span>
			</a>
			<?php
		endwhile;
		?>
	</div>
	<?php the_posts_pagination(); ?>
</section>

<?php
echo splendid_cta_band_markup(); // phpcs:ignore
get_footer();
