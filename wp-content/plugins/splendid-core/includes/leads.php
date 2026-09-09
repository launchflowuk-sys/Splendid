<?php
/**
 * Restricted enquiry records.
 *
 * Storage is OFF by default. It should only be enabled once the privacy notice
 * describes what is stored, for how long and who can see it. Records are
 * private, never public, never in REST, search, feeds or the sitemap.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

const SPLENDID_LEAD_POST_TYPE = 'splendid_lead';

/**
 * Register the restricted record type.
 */
function splendid_leads_register_post_type() {
	register_post_type( SPLENDID_LEAD_POST_TYPE, array(
		'labels'              => array(
			'name'          => __( 'Enquiries', 'splendid-core' ),
			'singular_name' => __( 'Enquiry', 'splendid-core' ),
			'menu_name'     => __( 'Enquiries', 'splendid-core' ),
		),
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => splendid_leads_enabled(),
		'show_in_menu'        => splendid_leads_enabled(),
		'show_in_rest'        => false,
		'show_in_nav_menus'   => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'can_export'          => true,
		'menu_icon'           => 'dashicons-email-alt',
		'supports'            => array( 'title' ),
		'capability_type'     => 'splendid_lead',
		'capabilities'        => array(
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap'        => true,
	) );
}
add_action( 'init', 'splendid_leads_register_post_type' );

/**
 * Grant enquiry capabilities to administrators only.
 *
 * @param array  $caps    Primitive capabilities.
 * @param string $cap     Requested capability.
 * @param int    $user_id User ID.
 * @return array
 */
function splendid_leads_map_caps( $caps, $cap, $user_id ) {
	$ours = array(
		'edit_splendid_leads',
		'edit_others_splendid_leads',
		'read_private_splendid_leads',
		'delete_splendid_leads',
		'delete_others_splendid_leads',
		'edit_published_splendid_leads',
		'delete_published_splendid_leads',
		'publish_splendid_leads',
	);

	if ( in_array( $cap, $ours, true ) ) {
		return user_can( $user_id, 'manage_options' ) ? array( 'manage_options' ) : array( 'do_not_allow' );
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'splendid_leads_map_caps', 10, 3 );

/**
 * Is enquiry storage switched on?
 *
 * @return bool
 */
function splendid_leads_enabled() {
	return (bool) splendid_option( 'store_leads', 0 );
}

/**
 * Store one enquiry record.
 *
 * @param array  $values Cleaned values.
 * @param string $status sent|queued.
 * @param string $reason Failure reason when queued.
 * @return bool True when a record was stored.
 */
function splendid_lead_record( $values, $status = 'sent', $reason = '' ) {
	if ( ! splendid_leads_enabled() ) {
		return false;
	}

	$post_id = wp_insert_post( array(
		'post_type'   => SPLENDID_LEAD_POST_TYPE,
		'post_status' => 'private',
		'post_title'  => sprintf(
			/* translators: 1: product, 2: postcode, 3: date. */
			__( '%1$s — %2$s — %3$s', 'splendid-core' ),
			$values['product'],
			$values['postcode'],
			wp_date( 'j M Y H:i' )
		),
	), true );

	if ( is_wp_error( $post_id ) ) {
		return false;
	}

	foreach ( $values as $key => $value ) {
		update_post_meta( $post_id, '_splendid_' . $key, $value );
	}

	update_post_meta( $post_id, '_splendid_status', $status );

	if ( $reason ) {
		update_post_meta( $post_id, '_splendid_reason', $reason );
	}

	return true;
}

/**
 * Queued records awaiting delivery.
 *
 * @return WP_Post[]
 */
function splendid_leads_queued() {
	if ( ! splendid_leads_enabled() ) {
		return array();
	}

	return get_posts( array(
		'post_type'      => SPLENDID_LEAD_POST_TYPE,
		'post_status'    => 'private',
		'posts_per_page' => 20,
		'meta_key'       => '_splendid_status', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'     => 'queued', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	) );
}

/**
 * Read the stored values back off a record.
 *
 * @param WP_Post $lead Record.
 * @return array
 */
function splendid_lead_values( $lead ) {
	$values = array();

	foreach ( array( 'product', 'material', 'quantity', 'postcode', 'name', 'email', 'phone', 'message', 'source' ) as $key ) {
		$values[ $key ] = get_post_meta( $lead->ID, '_splendid_' . $key, true );
	}

	return $values;
}

/**
 * Mark a queued record delivered.
 *
 * @param WP_Post $lead Record.
 */
function splendid_lead_mark_sent( $lead ) {
	update_post_meta( $lead->ID, '_splendid_status', 'sent' );
	update_post_meta( $lead->ID, '_splendid_sent_at', wp_date( 'c' ) );
}

/**
 * Show the enquiry values on the record screen.
 */
function splendid_leads_meta_box() {
	add_meta_box(
		'splendid-lead',
		__( 'Enquiry', 'splendid-core' ),
		static function ( $post ) {
			$values = splendid_lead_values( $post );
			$status = get_post_meta( $post->ID, '_splendid_status', true );

			echo '<p><strong>' . esc_html__( 'Status', 'splendid-core' ) . ':</strong> ' . esc_html( $status ) . '</p>';
			echo '<table class="widefat striped"><tbody>';

			foreach ( $values as $key => $value ) {
				printf(
					'<tr><th scope="row" style="width:180px">%s</th><td>%s</td></tr>',
					esc_html( ucfirst( str_replace( '_', ' ', $key ) ) ),
					nl2br( esc_html( (string) $value ) )
				);
			}

			echo '</tbody></table>';
		},
		SPLENDID_LEAD_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'splendid_leads_meta_box' );

/**
 * Belt and braces: never let a record appear in a sitemap.
 *
 * @param array $post_types Post types.
 * @return array
 */
function splendid_leads_exclude_from_sitemap( $post_types ) {
	unset( $post_types[ SPLENDID_LEAD_POST_TYPE ] );

	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'splendid_leads_exclude_from_sitemap' );
