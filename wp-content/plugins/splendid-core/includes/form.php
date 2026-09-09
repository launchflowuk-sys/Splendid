<?php
/**
 * The enquiry form markup shared by Contact, Free quote and the Quote planner.
 *
 * Works without JavaScript: it posts to admin-post.php and re-renders with the
 * submitted values and field errors. With JavaScript it submits to the REST
 * route and swaps in the received state without a page load.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The state to render: a fresh form, a retry with errors, or the received panel.
 *
 * @return array
 */
function splendid_form_state() {
	$state = array(
		'received' => false,
		'errors'   => array(),
		'message'  => '',
		'values'   => array(),
	);

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display state.
	if ( isset( $_GET['enquiry'] ) && 'received' === sanitize_text_field( wp_unslash( $_GET['enquiry'] ) ) ) {
		$state['received'] = true;

		return $state;
	}

	if ( isset( $_GET['retry'] ) ) {
		$token = sanitize_text_field( wp_unslash( $_GET['retry'] ) );
		$saved = get_transient( 'splendid_retry_' . $token );

		if ( is_array( $saved ) ) {
			$state['errors']  = isset( $saved['errors'] ) ? $saved['errors'] : array();
			$state['message'] = isset( $saved['message'] ) ? $saved['message'] : '';
			$state['values']  = isset( $saved['values'] ) ? $saved['values'] : array();
		}
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	return $state;
}

/**
 * The product preselected by ?product=, validated against the allowed list.
 *
 * @return string
 */
function splendid_form_preselected_product() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only preselection.
	if ( empty( $_GET['product'] ) ) {
		return 'Windows';
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$requested = sanitize_text_field( wp_unslash( $_GET['product'] ) );

	return in_array( $requested, splendid_enquiry_products(), true ) ? $requested : 'Windows';
}

/**
 * Render one field wrapper with its error message.
 *
 * @param string $key    Field key.
 * @param array  $errors Errors.
 * @return string Class attribute value.
 */
function splendid_field_classes( $key, $errors, $extra = '' ) {
	$classes = 'field' . ( $extra ? ' ' . $extra : '' );

	if ( isset( $errors[ $key ] ) ) {
		$classes .= ' has-error';
	}

	return $classes;
}

/**
 * Print the error message for a field, if any.
 *
 * @param string $key    Field key.
 * @param array  $errors Errors.
 */
function splendid_field_error( $key, $errors ) {
	if ( ! isset( $errors[ $key ] ) ) {
		return;
	}

	printf(
		'<span class="field-error" id="%1$s-error">%2$s</span>',
		esc_attr( 'enquiry-' . $key ),
		esc_html( $errors[ $key ] )
	);
}

/**
 * The received panel.
 *
 * @return string
 */
function splendid_form_received_markup() {
	ob_start();
	?>
	<div class="enquiry-received" role="status" tabindex="-1" id="enquiry">
		<span class="success-icon"><?php echo splendid_icon( 'check', 26 ); // phpcs:ignore ?></span>
		<h2><?php echo esc_html( splendid_enquiry_copy( 'success' ) ); ?></h2>
		<p><?php echo esc_html( splendid_enquiry_copy( 'success_body' ) ); ?></p>
		<a class="button brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to the homepage', 'splendid-core' ); ?> <?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
		</a>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * The full enquiry panel.
 *
 * @param array $atts Block attributes: variant, eyebrow, heading, intro.
 * @return string
 */
function splendid_form_markup( $atts = array() ) {
	$atts = wp_parse_args( $atts, array(
		'variant' => 'contact',
		'eyebrow' => 'LET&rsquo;S GET THE DETAILS RIGHT',
		'heading' => 'Your next chapter starts here.',
		'intro'   => '',
	) );

	$state = splendid_form_state();

	if ( $state['received'] ) {
		return '<div class="quote-panel">' . splendid_form_received_markup() . '</div>';
	}

	$errors  = $state['errors'];
	$values  = $state['values'];
	$intro   = '' !== $atts['intro'] ? $atts['intro'] : splendid_enquiry_copy( 'intro' );
	$planner = 'planner' === $atts['variant'];

	$value = static function ( $key, $fallback = '' ) use ( $values ) {
		return isset( $values[ $key ] ) && '' !== $values[ $key ] ? $values[ $key ] : $fallback;
	};

	$product  = $value( 'product', splendid_form_preselected_product() );
	$material = $value( 'material', 'Help me choose' );
	$quantity = (int) $value( 'quantity', 1 );

	$privacy_page = get_page_by_path( 'privacy-policy' );
	$privacy_url  = $privacy_page ? get_permalink( $privacy_page ) : splendid_url( '/privacy-policy' );

	ob_start();
	?>
	<div class="quote-panel">
		<form class="splendid-form"
			id="enquiry"
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			data-splendid-form
			data-rest="<?php echo esc_url( rest_url( 'splendid/v1/enquiry' ) ); ?>"
			novalidate>

			<input type="hidden" name="action" value="<?php echo esc_attr( SPLENDID_ENQUIRY_ACTION ); ?>">
			<?php wp_nonce_field( SPLENDID_ENQUIRY_ACTION ); ?>
			<input type="hidden" name="started" value="<?php echo esc_attr( time() ); ?>">
			<input type="hidden" name="source" value="<?php echo esc_attr( $atts['variant'] ); ?>">

			<div class="splendid-honeypot" aria-hidden="true">
				<label for="splendid-website"><?php esc_html_e( 'Leave this field empty', 'splendid-core' ); ?></label>
				<input type="text" id="splendid-website" name="splendid_website" tabindex="-1" autocomplete="off">
			</div>

			<div class="form-heading">
				<span class="eyebrow"><?php echo wp_kses_post( $atts['eyebrow'] ); ?></span>
				<h2><?php echo wp_kses_post( $atts['heading'] ); ?></h2>
				<p><?php echo wp_kses_post( $intro ); ?></p>
			</div>

			<div class="form-summary" role="alert" tabindex="-1" data-summary <?php echo $state['message'] ? '' : 'hidden'; ?>>
				<?php echo esc_html( $state['message'] ); ?>
				<?php if ( $errors ) : ?>
					<ul>
						<?php foreach ( $errors as $key => $error ) : ?>
							<li><a href="<?php echo esc_attr( '#enquiry-' . $key ); ?>"><?php echo esc_html( $error ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="form-grid">
				<label class="<?php echo esc_attr( splendid_field_classes( 'product', $errors ) ); ?>" for="enquiry-product">
					<span><?php esc_html_e( 'I&rsquo;m interested in', 'splendid-core' ); ?></span>
					<select class="form-select" id="enquiry-product" name="product" required
						<?php echo isset( $errors['product'] ) ? 'aria-describedby="enquiry-product-error" aria-invalid="true"' : ''; ?>>
						<?php foreach ( splendid_enquiry_products() as $option ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $product ); ?>>
								<?php echo esc_html( $option ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php splendid_field_error( 'product', $errors ); ?>
				</label>

				<label class="<?php echo esc_attr( splendid_field_classes( 'material', $errors ) ); ?>" for="enquiry-material">
					<span><?php esc_html_e( 'Preferred material', 'splendid-core' ); ?></span>
					<select class="form-select" id="enquiry-material" name="material" required
						<?php echo isset( $errors['material'] ) ? 'aria-describedby="enquiry-material-error" aria-invalid="true"' : ''; ?>>
						<?php foreach ( splendid_enquiry_materials() as $option ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $material ); ?>>
								<?php echo esc_html( $option ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php splendid_field_error( 'material', $errors ); ?>
				</label>

				<div class="<?php echo esc_attr( splendid_field_classes( 'quantity', $errors ) ); ?>">
					<label for="enquiry-quantity"><span><?php esc_html_e( 'Approximate quantity', 'splendid-core' ); ?></span></label>
					<div class="quantity">
						<button type="button" data-quantity="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'splendid-core' ); ?>">
							<?php echo splendid_icon( 'minus', 16 ); // phpcs:ignore ?>
						</button>
						<input class="splendid-quantity-input"
							id="enquiry-quantity"
							name="quantity"
							type="number"
							inputmode="numeric"
							min="1"
							max="100"
							step="1"
							value="<?php echo esc_attr( $quantity ); ?>"
							<?php echo isset( $errors['quantity'] ) ? 'aria-describedby="enquiry-quantity-error" aria-invalid="true"' : ''; ?>>
						<button type="button" data-quantity="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'splendid-core' ); ?>">
							<?php echo splendid_icon( 'plus', 16 ); // phpcs:ignore ?>
						</button>
					</div>
					<?php splendid_field_error( 'quantity', $errors ); ?>
				</div>

				<label class="<?php echo esc_attr( splendid_field_classes( 'postcode', $errors ) ); ?>" for="enquiry-postcode">
					<span><?php esc_html_e( 'Postcode', 'splendid-core' ); ?></span>
					<input id="enquiry-postcode" name="postcode" required autocomplete="postal-code"
						placeholder="<?php esc_attr_e( 'e.g. DA15 7AA', 'splendid-core' ); ?>" maxlength="12"
						value="<?php echo esc_attr( $value( 'postcode' ) ); ?>"
						<?php echo isset( $errors['postcode'] ) ? 'aria-describedby="enquiry-postcode-error" aria-invalid="true"' : ''; ?>>
					<?php splendid_field_error( 'postcode', $errors ); ?>
				</label>

				<label class="<?php echo esc_attr( splendid_field_classes( 'name', $errors ) ); ?>" for="enquiry-name">
					<span><?php esc_html_e( 'Your name', 'splendid-core' ); ?></span>
					<input id="enquiry-name" name="name" required autocomplete="name"
						placeholder="<?php esc_attr_e( 'Full name', 'splendid-core' ); ?>" maxlength="100"
						value="<?php echo esc_attr( $value( 'name' ) ); ?>"
						<?php echo isset( $errors['name'] ) ? 'aria-describedby="enquiry-name-error" aria-invalid="true"' : ''; ?>>
					<?php splendid_field_error( 'name', $errors ); ?>
				</label>

				<label class="<?php echo esc_attr( splendid_field_classes( 'email', $errors ) ); ?>" for="enquiry-email">
					<span><?php esc_html_e( 'Email address', 'splendid-core' ); ?></span>
					<input id="enquiry-email" name="email" type="email" required autocomplete="email"
						placeholder="you@example.com" maxlength="200"
						value="<?php echo esc_attr( $value( 'email' ) ); ?>"
						<?php echo isset( $errors['email'] ) ? 'aria-describedby="enquiry-email-error" aria-invalid="true"' : ''; ?>>
					<?php splendid_field_error( 'email', $errors ); ?>
				</label>

				<label class="<?php echo esc_attr( splendid_field_classes( 'phone', $errors, 'full' ) ); ?>" for="enquiry-phone">
					<span><?php esc_html_e( 'Phone number', 'splendid-core' ); ?> <small><?php esc_html_e( '(optional)', 'splendid-core' ); ?></small></span>
					<input id="enquiry-phone" name="phone" type="tel" autocomplete="tel"
						placeholder="<?php esc_attr_e( 'Your contact number', 'splendid-core' ); ?>" maxlength="30"
						value="<?php echo esc_attr( $value( 'phone' ) ); ?>"
						<?php echo isset( $errors['phone'] ) ? 'aria-describedby="enquiry-phone-error" aria-invalid="true"' : ''; ?>>
					<?php splendid_field_error( 'phone', $errors ); ?>
				</label>

				<label class="<?php echo esc_attr( splendid_field_classes( 'message', $errors, 'full' ) ); ?>" for="enquiry-message">
					<span><?php esc_html_e( 'A little about your project', 'splendid-core' ); ?></span>
					<textarea id="enquiry-message" name="message" required rows="4" maxlength="3000"
						placeholder="<?php esc_attr_e( 'Tell us what you&rsquo;d like to change, your preferred style and any timings you have in mind.', 'splendid-core' ); ?>"
						<?php echo isset( $errors['message'] ) ? 'aria-describedby="enquiry-message-error" aria-invalid="true"' : ''; ?>><?php echo esc_textarea( $value( 'message' ) ); ?></textarea>
					<?php splendid_field_error( 'message', $errors ); ?>
				</label>
			</div>

			<?php if ( $planner ) : ?>
				<p class="small-note"><?php echo esc_html( splendid_enquiry_copy( 'price_note' ) ); ?></p>
			<?php endif; ?>

			<button class="button brand submit" type="submit" data-submit-label="<?php echo esc_attr( wp_strip_all_tags( splendid_enquiry_copy( 'submit' ) ) ); ?>" data-sending-label="<?php echo esc_attr( wp_strip_all_tags( splendid_enquiry_copy( 'submitting' ) ) ); ?>">
				<span data-submit-text><?php echo esc_html( splendid_enquiry_copy( 'submit' ) ); ?></span>
				<?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
			</button>

			<p class="form-privacy">
				<?php
				printf(
					/* translators: %s: link to the privacy notice. */
					esc_html__( 'We&rsquo;ll use your details to respond to your enquiry. %s.', 'splendid-core' ),
					sprintf(
						'<a href="%s">%s</a>',
						esc_url( $privacy_url ),
						esc_html__( 'Read our privacy notice', 'splendid-core' )
					)
				);
				?>
			</p>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}
