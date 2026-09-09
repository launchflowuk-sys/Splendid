<?php
/**
 * Site footer.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer>
	<div class="footer-top">
		<div>
			<?php splendid_brand(); ?>
			<p><?php echo nl2br( esc_html( splendid_option( 'footer_tagline' ) ) ); ?></p>
			<a class="footer-phone" href="<?php echo esc_url( splendid_tel_href() ); ?>"><?php echo esc_html( splendid_option( 'phone' ) ); ?></a>
			<a href="mailto:<?php echo esc_attr( splendid_option( 'email' ) ); ?>"><?php echo esc_html( splendid_option( 'email' ) ); ?></a>
		</div>
		<?php
		splendid_render_footer_column( 'footer_windows', splendid_menu_name( 'footer_windows', __( 'Windows', 'splendid' ) ) );
		splendid_render_footer_column( 'footer_doors', splendid_menu_name( 'footer_doors', __( 'Doors & more', 'splendid' ) ) );
		splendid_render_footer_column( 'footer_discover', splendid_menu_name( 'footer_discover', __( 'Discover Splendid', 'splendid' ) ) );
		?>
	</div>

	<div class="footer-bottom">
		<span>
			<?php
			printf(
				/* translators: 1: year, 2: company name, 3: company number. */
				esc_html__( '&copy; %1$s %2$s &middot; Company no. %3$s', 'splendid' ),
				esc_html( wp_date( 'Y' ) ),
				esc_html( splendid_option( 'company_name' ) ),
				esc_html( splendid_option( 'company_number' ) )
			);
			?>
			<br>
			<?php echo esc_html( splendid_option( 'address' ) ); ?>
			<br>
			<?php esc_html_e( 'Registered in England and Wales.', 'splendid' ); ?>
			<?php if ( splendid_option( 'vat_number' ) ) : ?>
				<?php
				printf(
					/* translators: %s: VAT registration number. */
					esc_html__( 'VAT no. %s', 'splendid' ),
					esc_html( splendid_option( 'vat_number' ) )
				);
				?>
			<?php endif; ?>
		</span>
		<div>
			<?php foreach ( splendid_nav_items( 'footer_legal' ) as $item ) : ?>
				<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
			<?php
			/**
			 * Cookie settings control.
			 *
			 * Splendid Core prints a working "Cookie settings" button here only when
			 * optional cookies are actually configured. With no optional cookies the
			 * hook stays empty, as briefs/04 requires.
			 */
			do_action( 'splendid_footer_legal_extra' );
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
