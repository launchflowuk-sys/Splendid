<?php
/**
 * Fallback template.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-heading">
	<p class="eyebrow"><?php esc_html_e( 'SPLENDID', 'splendid' ); ?></p>
	<h1><?php echo esc_html( is_home() ? get_the_title( (int) get_option( 'page_for_posts' ) ) : wp_get_document_title() ); ?></h1>
</section>

<section class="section reveal">
	<div class="product-grid">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<a class="product-card" href="<?php the_permalink(); ?>">
					<span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
					<span class="text-link">
						<?php esc_html_e( 'Read the article', 'splendid' ); ?> <?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
					</span>
				</a>
				<?php
			endwhile;
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing to show here yet.', 'splendid' ); ?></p>
		<?php endif; ?>
	</div>
	<?php the_posts_pagination( array( 'class' => 'splendid-pagination' ) ); ?>
</section>

<?php
echo splendid_cta_band_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
get_footer();
