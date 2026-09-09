<?php
/**
 * Business settings screen.
 *
 * One place for the details that appear in the header, footer, contact page,
 * enquiry emails and structured data. The enquiry recipient lives here, server
 * side, and is never accepted from a form field or URL.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions for the settings screen.
 *
 * @return array
 */
function splendid_settings_fields() {
	return array(
		'business' => array(
			'label'  => __( 'Business details', 'splendid-core' ),
			'note'   => __( 'Used in the header, footer, contact page and structured data. Confirm every value with the client before launch.', 'splendid-core' ),
			'fields' => array(
				'company_name'     => array( 'label' => __( 'Registered company name', 'splendid-core' ), 'type' => 'text' ),
				'company_number'   => array( 'label' => __( 'Company number', 'splendid-core' ), 'type' => 'text' ),
				'vat_number'       => array( 'label' => __( 'VAT number', 'splendid-core' ), 'type' => 'text', 'help' => __( 'Leave empty unless the business is VAT registered. It is only printed when set.', 'splendid-core' ) ),
				'phone'            => array( 'label' => __( 'Phone (as displayed)', 'splendid-core' ), 'type' => 'text' ),
				'phone_tel'        => array( 'label' => __( 'Phone (dial string)', 'splendid-core' ), 'type' => 'text' ),
				'email'            => array( 'label' => __( 'Public email address', 'splendid-core' ), 'type' => 'email' ),
				'address'          => array( 'label' => __( 'Address (one line)', 'splendid-core' ), 'type' => 'text' ),
				'address_street'   => array( 'label' => __( 'Street', 'splendid-core' ), 'type' => 'text' ),
				'address_city'     => array( 'label' => __( 'Town or city', 'splendid-core' ), 'type' => 'text' ),
				'address_postcode' => array( 'label' => __( 'Postcode', 'splendid-core' ), 'type' => 'text' ),
			),
		),
		'shell'    => array(
			'label'  => __( 'Header and footer', 'splendid-core' ),
			'fields' => array(
				'topbar_tagline'    => array( 'label' => __( 'Top strip tagline', 'splendid-core' ), 'type' => 'text' ),
				'topbar_area'       => array( 'label' => __( 'Top strip area', 'splendid-core' ), 'type' => 'text' ),
				'header_cta_text'   => array( 'label' => __( 'Header button', 'splendid-core' ), 'type' => 'text' ),
				'header_cta_mobile' => array( 'label' => __( 'Header button (small screens)', 'splendid-core' ), 'type' => 'text' ),
				'header_cta_url'    => array( 'label' => __( 'Header button link', 'splendid-core' ), 'type' => 'text' ),
				'footer_tagline'    => array( 'label' => __( 'Footer tagline', 'splendid-core' ), 'type' => 'textarea' ),
				'visit_note'        => array( 'label' => __( 'Visiting note', 'splendid-core' ), 'type' => 'text' ),
			),
		),
		'links'    => array(
			'label'  => __( 'External links', 'splendid-core' ),
			'note'   => __( 'Only add a review profile URL once the exact business profile has been confirmed. Do not add a competitor profile or a generic search when a real profile exists.', 'splendid-core' ),
			'fields' => array(
				'maps_url'    => array( 'label' => __( 'Directions link', 'splendid-core' ), 'type' => 'url' ),
				'reviews_url' => array( 'label' => __( 'Independent reviews link', 'splendid-core' ), 'type' => 'url' ),
			),
		),
		'enquiry'  => array(
			'label'  => __( 'Enquiries', 'splendid-core' ),
			'note'   => __( 'The recipient is stored here, server side. It is never read from the form or the URL. Use a test mailbox until the owner has confirmed the real destination and authorised a live delivery test.', 'splendid-core' ),
			'fields' => array(
				'enquiry_email' => array(
					'label' => __( 'Send enquiries to', 'splendid-core' ),
					'type'  => 'email',
					'help'  => __( 'Leave empty to fall back to the site admin email. Enquiries are refused rather than silently dropped if no transport accepts them.', 'splendid-core' ),
				),
				'enquiry_from'  => array(
					'label' => __( 'Send enquiries from', 'splendid-core' ),
					'type'  => 'email',
					'help'  => __( 'Must be an address on a domain this site is authorised to send as (SPF/DKIM). The visitor address is used as Reply-To, never as From.', 'splendid-core' ),
				),
				'store_leads'   => array(
					'label' => __( 'Store enquiries in WordPress', 'splendid-core' ),
					'type'  => 'checkbox',
					'help'  => __( 'Off by default. Only switch on once the privacy notice describes storage, retention and who has access. Records are private, excluded from search, feeds, REST and the sitemap.', 'splendid-core' ),
				),
			),
		),
	);
}

/**
 * Register the option and its sanitiser.
 */
function splendid_settings_register() {
	register_setting( 'splendid_settings_group', 'splendid_settings', array(
		'type'              => 'array',
		'sanitize_callback' => 'splendid_settings_sanitize',
		'default'           => array(),
		'show_in_rest'      => false,
	) );
}
add_action( 'admin_init', 'splendid_settings_register' );

/**
 * Sanitise every submitted setting according to its declared type.
 *
 * @param mixed $input Raw input.
 * @return array
 */
function splendid_settings_sanitize( $input ) {
	$clean = get_option( 'splendid_settings', array() );

	if ( ! is_array( $clean ) ) {
		$clean = array();
	}

	if ( ! is_array( $input ) ) {
		return $clean;
	}

	foreach ( splendid_settings_fields() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			if ( 'checkbox' === $field['type'] ) {
				$clean[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
				continue;
			}

			if ( ! isset( $input[ $key ] ) ) {
				continue;
			}

			$value = wp_unslash( $input[ $key ] );

			switch ( $field['type'] ) {
				case 'email':
					$value = sanitize_email( $value );
					break;
				case 'url':
					$value = esc_url_raw( $value );
					break;
				case 'textarea':
					$value = sanitize_textarea_field( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
			}

			$clean[ $key ] = $value;
		}
	}

	return $clean;
}

/**
 * Add the settings screen.
 */
function splendid_settings_menu() {
	add_options_page(
		__( 'Splendid', 'splendid-core' ),
		__( 'Splendid', 'splendid-core' ),
		'manage_options',
		'splendid-settings',
		'splendid_settings_page'
	);
}
add_action( 'admin_menu', 'splendid_settings_menu' );

/**
 * Render the settings screen.
 */
function splendid_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Splendid business settings', 'splendid-core' ); ?></h1>

		<?php splendid_settings_mail_status(); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'splendid_settings_group' ); ?>

			<?php foreach ( splendid_settings_fields() as $section_key => $section ) : ?>
				<h2><?php echo esc_html( $section['label'] ); ?></h2>
				<?php if ( ! empty( $section['note'] ) ) : ?>
					<p class="description"><?php echo esc_html( $section['note'] ); ?></p>
				<?php endif; ?>
				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $section['fields'] as $key => $field ) : ?>
						<?php $value = splendid_option( $key ); ?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( 'splendid-' . $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
							</th>
							<td>
								<?php if ( 'textarea' === $field['type'] ) : ?>
									<textarea id="<?php echo esc_attr( 'splendid-' . $key ); ?>"
										name="<?php echo esc_attr( 'splendid_settings[' . $key . ']' ); ?>"
										rows="3" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
								<?php elseif ( 'checkbox' === $field['type'] ) : ?>
									<label>
										<input type="checkbox"
											id="<?php echo esc_attr( 'splendid-' . $key ); ?>"
											name="<?php echo esc_attr( 'splendid_settings[' . $key . ']' ); ?>"
											value="1" <?php checked( (int) $value, 1 ); ?>>
										<?php esc_html_e( 'Enabled', 'splendid-core' ); ?>
									</label>
								<?php else : ?>
									<input type="<?php echo esc_attr( 'email' === $field['type'] || 'url' === $field['type'] ? $field['type'] : 'text' ); ?>"
										id="<?php echo esc_attr( 'splendid-' . $key ); ?>"
										name="<?php echo esc_attr( 'splendid_settings[' . $key . ']' ); ?>"
										value="<?php echo esc_attr( $value ); ?>"
										class="regular-text">
								<?php endif; ?>
								<?php if ( ! empty( $field['help'] ) ) : ?>
									<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>

			<?php submit_button(); ?>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Content import', 'splendid-core' ); ?></h2>
		<?php splendid_importer_admin_panel(); ?>
	</div>
	<?php
}

/**
 * Tell the administrator honestly whether outgoing mail is configured.
 */
function splendid_settings_mail_status() {
	$configured = splendid_enquiry_transport_is_configured();
	?>
	<div class="notice notice-<?php echo $configured ? 'success' : 'warning'; ?> inline">
		<p>
			<?php if ( $configured ) : ?>
				<strong><?php esc_html_e( 'Authenticated mail detected.', 'splendid-core' ); ?></strong>
				<?php esc_html_e( 'An SMTP or API mailer is configured for this site. Send a test enquiry to an authorised test mailbox and confirm the provider accepted it.', 'splendid-core' ); ?>
			<?php else : ?>
				<strong><?php esc_html_e( 'No authenticated mail transport is configured.', 'splendid-core' ); ?></strong>
				<?php esc_html_e( 'Enquiries will be refused with a retry message rather than reported as sent. Configure SMTP or an email API, then retest.', 'splendid-core' ); ?>
			<?php endif; ?>
		</p>
	</div>
	<?php
}
