<?php
/**
 * Search results.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-heading">
	<p class="eyebrow"><?php esc_html_e( 'SEARCH', 'splendid' ); ?></p>
	<h1><?php echo esc_html( get_search_query() ); ?></h1>
	<?php get_search_form(); ?>
</section>

<section class="section reveal">
	<div class="product-grid">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<a class="product-card" href="<?php the_permalink(); ?>">
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
					<span class="text-link"><?php esc_html_e( 'Open page', 'splendid' ); ?> <?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?></span>
				</a>
				<?php
			endwhile;
		else :
			?>
			<p><?php esc_html_e( 'No matches. Try a different word, or contact the team.', 'splendid' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
echo splendid_cta_band_markup(); // phpcs:ignore
get_footer();
