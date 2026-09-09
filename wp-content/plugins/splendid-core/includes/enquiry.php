<?php
/**
 * Server-backed enquiries for Contact, Free quote and the Quote planner.
 *
 * The three pages share this service and differ only in heading and context.
 * Nothing here calculates a price. The recipient is a server-side setting.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

const SPLENDID_ENQUIRY_ACTION   = 'splendid_enquiry';
const SPLENDID_RATE_LIMIT       = 6;    // Submissions allowed per window, per address.
const SPLENDID_RATE_WINDOW      = HOUR_IN_SECONDS;
const SPLENDID_DUPLICATE_WINDOW = 10 * MINUTE_IN_SECONDS;
const SPLENDID_MIN_FILL_SECONDS = 3;

/**
 * Every value the "I'm interested in" select is allowed to hold.
 *
 * @return array
 */
function splendid_enquiry_products() {
	$data     = splendid_data();
	$products = array( 'Windows', 'Doors', 'Conservatories', 'Porches', 'A whole-home project' );

	foreach ( array( 'windows', 'doors' ) as $group ) {
		foreach ( $data[ $group ] as $product ) {
			$products[] = $product['name'];
		}
	}

	/**
	 * Filter the allowed enquiry products.
	 *
	 * @param array $products Allowed values.
	 */
	return apply_filters( 'splendid_enquiry_products', $products );
}

/**
 * Allowed material choices.
 *
 * @return array
 */
function splendid_enquiry_materials() {
	return apply_filters(
		'splendid_enquiry_materials',
		array( 'Help me choose', 'uPVC', 'Aluminium', 'Timber', 'Composite' )
	);
}

/**
 * The exact production microcopy from briefs/03-production-behaviour.md.
 *
 * @param string $key Message key.
 * @return string
 */
function splendid_enquiry_copy( $key ) {
	$copy = array(
		'intro'          => __( 'Tell us what you have in mind and our team will contact you about the next steps.', 'splendid-core' ),
		'submit'         => __( 'Send my enquiry', 'splendid-core' ),
		'submitting'     => __( 'Sending your enquiry&hellip;', 'splendid-core' ),
		'success'        => __( 'Thank you. Your enquiry has been received.', 'splendid-core' ),
		'success_body'   => __( 'Our team will review your project details and contact you using the information you provided.', 'splendid-core' ),
		'summary'        => __( 'Please check the highlighted fields.', 'splendid-core' ),
		'email_invalid'  => __( 'Enter a valid email address.', 'splendid-core' ),
		'price_note'     => __( 'Your price will be confirmed after the team reviews your requirements and the agreed specification. This planner does not calculate a price.', 'splendid-core' ),
		'privacy'        => __( 'We&rsquo;ll use your details to respond to your enquiry. Read our privacy notice.', 'splendid-core' ),
	);

	if ( 'failure' === $key ) {
		return sprintf(
			/* translators: %s: telephone number. */
			__( 'We couldn&rsquo;t submit your enquiry. Your details are still here&mdash;please try again, or call %s.', 'splendid-core' ),
			splendid_option( 'phone', '020 7998 6802' )
		);
	}

	return isset( $copy[ $key ] ) ? $copy[ $key ] : '';
}

/* -------------------------------------------------------------------------
 * Validation
 * ---------------------------------------------------------------------- */

/**
 * Normalise a UK postcode without rejecting valid formats.
 *
 * Accepts every current UK outward/inward combination plus BFPO and the
 * special cases (GIR 0AA). Spacing and case are normalised, not enforced.
 *
 * @param string $raw Raw input.
 * @return string|false Normalised postcode, or false when it cannot be a UK postcode.
 */
function splendid_normalise_postcode( $raw ) {
	$value = strtoupper( preg_replace( '/[\s\-]+/', '', (string) $raw ) );

	if ( '' === $value ) {
		return false;
	}

	if ( 'GIR0AA' === $value ) {
		return 'GIR 0AA';
	}

	if ( 0 === strpos( $value, 'BFPO' ) ) {
		$number = substr( $value, 4 );

		return ctype_digit( $number ) && strlen( $number ) <= 4 ? 'BFPO ' . $number : false;
	}

	// Outward: 2-4 characters. Inward: digit + two letters.
	if ( ! preg_match( '/^([A-Z]{1,2}[0-9][A-Z0-9]?)([0-9][A-Z]{2})$/', $value, $matches ) ) {
		return false;
	}

	return $matches[1] . ' ' . $matches[2];
}

/**
 * Validate a submitted enquiry.
 *
 * @param array $raw Raw request values.
 * @return array {
 *     @type array $values Cleaned values.
 *     @type array $errors Field key => message.
 * }
 */
function splendid_enquiry_validate( $raw ) {
	$errors = array();
	$values = array();

	$get = static function ( $key ) use ( $raw ) {
		return isset( $raw[ $key ] ) ? wp_unslash( $raw[ $key ] ) : '';
	};

	// Interested in.
	$product = sanitize_text_field( $get( 'product' ) );
	if ( '' === $product ) {
		$errors['product'] = __( 'Choose what you are interested in.', 'splendid-core' );
	} elseif ( ! in_array( $product, splendid_enquiry_products(), true ) ) {
		$errors['product'] = __( 'Choose one of the listed options.', 'splendid-core' );
	}
	$values['product'] = $product;

	// Material.
	$material = sanitize_text_field( $get( 'material' ) );
	if ( '' === $material ) {
		$errors['material'] = __( 'Choose a preferred material.', 'splendid-core' );
	} elseif ( ! in_array( $material, splendid_enquiry_materials(), true ) ) {
		$errors['material'] = __( 'Choose one of the listed options.', 'splendid-core' );
	}
	$values['material'] = $material;

	// Quantity.
	$quantity_raw = trim( (string) $get( 'quantity' ) );
	if ( '' === $quantity_raw || ! preg_match( '/^[0-9]{1,3}$/', $quantity_raw ) ) {
		$errors['quantity'] = __( 'Enter a whole number between 1 and 100.', 'splendid-core' );
		$values['quantity'] = 1;
	} else {
		$quantity = (int) $quantity_raw;
		if ( $quantity < 1 || $quantity > 100 ) {
			$errors['quantity'] = __( 'Enter a whole number between 1 and 100.', 'splendid-core' );
		}
		$values['quantity'] = $quantity;
	}

	// Postcode.
	$postcode = splendid_normalise_postcode( $get( 'postcode' ) );
	if ( false === $postcode ) {
		$errors['postcode']  = __( 'Enter a UK postcode, for example DA15 7AA.', 'splendid-core' );
		$values['postcode']  = sanitize_text_field( $get( 'postcode' ) );
	} else {
		$values['postcode'] = $postcode;
	}

	// Name. Normal Unicode names are allowed; only length and tags are limited.
	$name = trim( sanitize_text_field( $get( 'name' ) ) );
	if ( '' === $name ) {
		$errors['name'] = __( 'Enter your name.', 'splendid-core' );
	} elseif ( mb_strlen( $name ) > 100 ) {
		$errors['name'] = __( 'Names can be up to 100 characters.', 'splendid-core' );
	}
	$values['name'] = $name;

	// Email.
	$email = trim( (string) $get( 'email' ) );
	if ( '' === $email || ! is_email( $email ) || mb_strlen( $email ) > 200 ) {
		$errors['email'] = splendid_enquiry_copy( 'email_invalid' );
	}
	$values['email'] = sanitize_email( $email );

	// Phone (optional). International formatting is accepted.
	$phone = trim( sanitize_text_field( $get( 'phone' ) ) );
	if ( mb_strlen( $phone ) > 30 ) {
		$errors['phone'] = __( 'Phone numbers can be up to 30 characters.', 'splendid-core' );
	}
	$values['phone'] = $phone;

	// Project details.
	$message = trim( sanitize_textarea_field( $get( 'message' ) ) );
	if ( '' === $message ) {
		$errors['message'] = __( 'Tell us a little about your project.', 'splendid-core' );
	} elseif ( mb_strlen( $message ) > 3000 ) {
		$errors['message'] = __( 'Project details can be up to 3000 characters.', 'splendid-core' );
	}
	$values['message'] = $message;

	$values['source'] = sanitize_text_field( $get( 'source' ) );

	return array(
		'values' => $values,
		'errors' => $errors,
	);
}

/* -------------------------------------------------------------------------
 * Abuse controls
 * ---------------------------------------------------------------------- */

/**
 * A coarse, hashed client key for rate limiting. The raw address is not stored.
 *
 * @return string
 */
function splendid_client_key() {
	$address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

	return substr( hash_hmac( 'sha256', $address, wp_salt( 'nonce' ) ), 0, 32 );
}

/**
 * Has this client submitted too often?
 *
 * @return bool
 */
function splendid_enquiry_rate_limited() {
	$key   = 'splendid_rate_' . splendid_client_key();
	$count = (int) get_transient( $key );

	return $count >= SPLENDID_RATE_LIMIT;
}

/**
 * Record a submission against the rate limit.
 */
function splendid_enquiry_record_attempt() {
	$key   = 'splendid_rate_' . splendid_client_key();
	$count = (int) get_transient( $key );

	set_transient( $key, $count + 1, SPLENDID_RATE_WINDOW );
}

/**
 * Reject a repeat of an identical submission from the same client.
 *
 * @param array $values Cleaned values.
 * @return bool True when this is a duplicate.
 */
function splendid_enquiry_is_duplicate( $values ) {
	$fingerprint = 'splendid_dup_' . substr(
		hash_hmac( 'sha256', wp_json_encode( $values ) . splendid_client_key(), wp_salt( 'nonce' ) ),
		0,
		32
	);

	if ( get_transient( $fingerprint ) ) {
		return true;
	}

	set_transient( $fingerprint, 1, SPLENDID_DUPLICATE_WINDOW );

	return false;
}

/**
 * Silent spam controls: an accessible honeypot and a minimum fill time.
 *
 * @param array $raw Raw request.
 * @return bool True when the submission looks automated.
 */
function splendid_enquiry_looks_automated( $raw ) {
	if ( ! empty( $raw['splendid_website'] ) ) {
		return true;
	}

	$started = isset( $raw['started'] ) ? (int) $raw['started'] : 0;

	if ( $started > 0 && ( time() - $started ) < SPLENDID_MIN_FILL_SECONDS ) {
		return true;
	}

	return false;
}

/* -------------------------------------------------------------------------
 * Delivery
 * ---------------------------------------------------------------------- */

/**
 * Is an authenticated mail transport configured for this site?
 *
 * wp_mail() returning true from a bare PHP mail() call is not evidence of
 * delivery, so a configured SMTP/API mailer is required before an enquiry can
 * be reported as received.
 *
 * @return bool
 */
function splendid_enquiry_transport_is_configured() {
	/**
	 * Filter whether a durable mail transport is available.
	 *
	 * Mail plugins that configure PHPMailer should return true here, or hook
	 * phpmailer_init and set SMTP, which is detected below.
	 *
	 * @param bool|null $configured Null when undetermined.
	 */
	$override = apply_filters( 'splendid_mail_transport_configured', null );

	if ( is_bool( $override ) ) {
		return $override;
	}

	if ( defined( 'SPLENDID_MAIL_CONFIGURED' ) && SPLENDID_MAIL_CONFIGURED ) {
		return true;
	}

	// Common mailer plugins configure PHPMailer through this hook.
	foreach ( array( 'phpmailer_init', 'wp_mail_from' ) as $hook ) {
		if ( has_filter( $hook ) || has_action( $hook ) ) {
			return true;
		}
	}

	return false;
}

/**
 * The confirmed recipient. Never taken from the request.
 *
 * @return string
 */
function splendid_enquiry_recipient() {
	$recipient = sanitize_email( (string) splendid_option( 'enquiry_email' ) );

	if ( ! is_email( $recipient ) ) {
		$recipient = sanitize_email( (string) get_option( 'admin_email' ) );
	}

	return $recipient;
}

/**
 * Strip anything that could inject a mail header.
 *
 * @param string $value Header value.
 * @return string
 */
function splendid_header_safe( $value ) {
	return trim( preg_replace( '/[\r\n\t]+/', ' ', (string) $value ) );
}

/**
 * Compose and send one enquiry.
 *
 * @param array $values Cleaned values.
 * @return array { @type bool $sent, @type string $reason }
 */
function splendid_enquiry_send( $values ) {
	$recipient = splendid_enquiry_recipient();

	if ( ! is_email( $recipient ) ) {
		return array( 'sent' => false, 'reason' => 'no_recipient' );
	}

	if ( ! splendid_enquiry_transport_is_configured() ) {
		return array( 'sent' => false, 'reason' => 'no_transport' );
	}

	$from = sanitize_email( (string) splendid_option( 'enquiry_from' ) );

	if ( ! is_email( $from ) ) {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$from = 'wordpress@' . preg_replace( '/^www\./i', '', (string) $host );
	}

	$subject = sprintf(
		/* translators: %s: product interest. */
		__( 'New website enquiry — %s', 'splendid-core' ),
		splendid_header_safe( $values['product'] )
	);

	$lines = array(
		__( 'A new enquiry was submitted on the website.', 'splendid-core' ),
		'',
		sprintf( '%s: %s', __( 'Interested in', 'splendid-core' ), $values['product'] ),
		sprintf( '%s: %s', __( 'Preferred material', 'splendid-core' ), $values['material'] ),
		sprintf( '%s: %s', __( 'Approximate quantity', 'splendid-core' ), $values['quantity'] ),
		sprintf( '%s: %s', __( 'Postcode', 'splendid-core' ), $values['postcode'] ),
		'',
		sprintf( '%s: %s', __( 'Name', 'splendid-core' ), $values['name'] ),
		sprintf( '%s: %s', __( 'Email', 'splendid-core' ), $values['email'] ),
		sprintf( '%s: %s', __( 'Phone', 'splendid-core' ), '' !== $values['phone'] ? $values['phone'] : __( 'not supplied', 'splendid-core' ) ),
		'',
		__( 'Project details', 'splendid-core' ) . ':',
		$values['message'],
		'',
		sprintf( '%s: %s', __( 'Submitted from', 'splendid-core' ), $values['source'] ),
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', splendid_header_safe( splendid_option( 'company_name', 'Splendid' ) . ' website' ), $from ),
		sprintf( 'Reply-To: %s <%s>', splendid_header_safe( $values['name'] ), $values['email'] ),
	);

	$failure = null;
	$capture = static function ( $error ) use ( &$failure ) {
		$failure = $error;
	};
	add_action( 'wp_mail_failed', $capture );

	$sent = wp_mail( $recipient, $subject, implode( "\n", $lines ), $headers );

	remove_action( 'wp_mail_failed', $capture );

	if ( ! $sent || $failure instanceof WP_Error ) {
		return array(
			'sent'   => false,
			'reason' => $failure instanceof WP_Error ? $failure->get_error_code() : 'send_failed',
		);
	}

	return array( 'sent' => true, 'reason' => '' );
}

/**
 * Handle one validated submission end to end.
 *
 * @param array $raw Raw request values.
 * @return array { @type bool $ok, @type array $errors, @type string $message }
 */
function splendid_enquiry_process( $raw ) {
	if ( splendid_enquiry_looks_automated( $raw ) ) {
		// Refuse quietly with the ordinary failure message; do not explain the control.
		return array( 'ok' => false, 'errors' => array(), 'message' => splendid_enquiry_copy( 'failure' ) );
	}

	if ( splendid_enquiry_rate_limited() ) {
		return array(
			'ok'      => false,
			'errors'  => array(),
			'message' => __( 'We have received several enquiries from this connection. Please try again later, or call the team.', 'splendid-core' ),
		);
	}

	$checked = splendid_enquiry_validate( $raw );

	if ( $checked['errors'] ) {
		return array(
			'ok'      => false,
			'errors'  => $checked['errors'],
			'message' => splendid_enquiry_copy( 'summary' ),
			'values'  => $checked['values'],
		);
	}

	splendid_enquiry_record_attempt();

	if ( splendid_enquiry_is_duplicate( $checked['values'] ) ) {
		// Identical repeat: report the original acceptance rather than sending twice.
		return array( 'ok' => true, 'errors' => array(), 'message' => splendid_enquiry_copy( 'success' ), 'duplicate' => true );
	}

	$result = splendid_enquiry_send( $checked['values'] );

	if ( $result['sent'] ) {
		splendid_lead_record( $checked['values'], 'sent' );

		/**
		 * Fires after an enquiry has been accepted by the mail transport.
		 *
		 * @param array $values Cleaned values.
		 */
		do_action( 'splendid_enquiry_sent', $checked['values'] );

		return array( 'ok' => true, 'errors' => array(), 'message' => splendid_enquiry_copy( 'success' ) );
	}

	// Delivery failed. Queue only where storage is approved, otherwise report failure.
	$queued = splendid_lead_record( $checked['values'], 'queued', $result['reason'] );

	if ( $queued ) {
		splendid_enquiry_schedule_retry();

		return array( 'ok' => true, 'errors' => array(), 'message' => splendid_enquiry_copy( 'success' ), 'queued' => true );
	}

	return array(
		'ok'      => false,
		'errors'  => array(),
		'message' => splendid_enquiry_copy( 'failure' ),
		'values'  => $checked['values'],
		'reason'  => $result['reason'],
	);
}

/**
 * Schedule the retry pass for queued enquiries.
 */
function splendid_enquiry_schedule_retry() {
	if ( ! wp_next_scheduled( 'splendid_retry_queue' ) ) {
		wp_schedule_single_event( time() + 15 * MINUTE_IN_SECONDS, 'splendid_retry_queue' );
	}
}

/**
 * Retry queued enquiries once a transport is available.
 */
function splendid_enquiry_retry_queue() {
	$queued = splendid_leads_queued();

	if ( ! $queued ) {
		return;
	}

	foreach ( $queued as $lead ) {
		$values = splendid_lead_values( $lead );
		$result = splendid_enquiry_send( $values );

		if ( $result['sent'] ) {
			splendid_lead_mark_sent( $lead );
		}
	}

	if ( splendid_leads_queued() ) {
		wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'splendid_retry_queue' );
	}
}
add_action( 'splendid_retry_queue', 'splendid_enquiry_retry_queue' );

/* -------------------------------------------------------------------------
 * Endpoints
 * ---------------------------------------------------------------------- */

/**
 * REST route used by the enhanced form, plus a nonce refresh route for caches.
 */
function splendid_enquiry_rest_routes() {
	register_rest_route( 'splendid/v1', '/enquiry', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'splendid_enquiry_rest_handler',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'splendid/v1', '/nonce', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => static function () {
			return rest_ensure_response( array( 'nonce' => wp_create_nonce( 'wp_rest' ) ) );
		},
		'permission_callback' => '__return_true',
	) );
}
add_action( 'rest_api_init', 'splendid_enquiry_rest_routes' );

/**
 * REST handler.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function splendid_enquiry_rest_handler( WP_REST_Request $request ) {
	$result = splendid_enquiry_process( $request->get_params() );

	unset( $result['values'], $result['reason'] );

	return new WP_REST_Response( $result, $result['ok'] ? 200 : 422 );
}

/**
 * Non-JavaScript submission handler.
 */
function splendid_enquiry_post_handler() {
	$referer  = wp_get_referer();
	$redirect = $referer ? $referer : home_url( '/contact' );

	check_admin_referer( SPLENDID_ENQUIRY_ACTION );

	$result = splendid_enquiry_process( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked above.

	if ( ! empty( $result['ok'] ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'received', remove_query_arg( array( 'enquiry', 'retry' ), $redirect ) ) . '#enquiry' );
		exit;
	}

	// Keep the submitted values out of the URL: hand back a short-lived token instead.
	$token = wp_generate_password( 20, false, false );

	set_transient(
		'splendid_retry_' . $token,
		array(
			'errors'  => isset( $result['errors'] ) ? $result['errors'] : array(),
			'message' => $result['message'],
			'values'  => isset( $result['values'] ) ? $result['values'] : array(),
		),
		15 * MINUTE_IN_SECONDS
	);

	wp_safe_redirect( add_query_arg( 'retry', $token, remove_query_arg( array( 'enquiry', 'retry' ), $redirect ) ) . '#enquiry' );
	exit;
}
add_action( 'admin_post_nopriv_' . SPLENDID_ENQUIRY_ACTION, 'splendid_enquiry_post_handler' );
add_action( 'admin_post_' . SPLENDID_ENQUIRY_ACTION, 'splendid_enquiry_post_handler' );
