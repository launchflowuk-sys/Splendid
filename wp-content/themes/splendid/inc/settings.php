<?php
/**
 * Single source of truth for business contact details.
 *
 * Values are stored in the `splendid_settings` option. Splendid Core provides
 * the editing screen (Settings -> Splendid) and, when active, owns these
 * functions; plugins load first, so the definitions here are the standalone
 * fallback that keeps the theme working on its own.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'splendid_default_settings' ) ) {
	/**
	 * Default business details.
	 *
	 * @return array
	 */
	function splendid_default_settings() {
		return array(
			'company_name'      => 'Splendid Double Glazing Ltd',
			'company_number'    => '12437986',
			'phone'             => '020 7998 6802',
			'phone_tel'         => '02079986802',
			'email'             => 'info@splendidglazing.co.uk',
			'address'           => '758 Sidcup Road, London, SE9 3NS',
			'address_street'    => '758 Sidcup Road',
			'address_city'      => 'London',
			'address_postcode'  => 'SE9 3NS',
			'address_country'   => 'GB',
			'topbar_tagline'    => 'YOUR LOCAL GLAZING SPECIALISTS',
			'topbar_area'       => 'Sidcup & South East London',
			'header_cta_text'   => 'Let&rsquo;s talk about your home',
			'header_cta_mobile' => 'Get a quote',
			'header_cta_url'    => '/free-quote',
			'footer_tagline'    => "Beautifully considered glazing.\nA brighter way to live.",
			'maps_url'          => 'https://www.google.com/maps/search/?api=1&query=758+Sidcup+Road+London+SE9+3NS',
			'reviews_url'       => 'https://www.google.com/search?q=Splendid+Double+Glazing+758+Sidcup+Road+London',
			'vat_number'        => '',
			'enquiry_email'     => '',
			'enquiry_from'      => '',
			'store_leads'       => 0,
			'visit_note'        => 'Planning a visit? Please call ahead to arrange a suitable time.',
		);
	}
}

if ( ! function_exists( 'splendid_option' ) ) {
	/**
	 * Read one business setting.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback when the key is unknown.
	 * @return mixed
	 */
	function splendid_option( $key, $default = '' ) {
		$defaults = splendid_default_settings();
		$saved    = get_option( 'splendid_settings', array() );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$saved = array_filter( $saved, static function ( $value ) {
			return '' !== $value && null !== $value;
		} );

		$settings = array_merge( $defaults, $saved );

		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default;
	}
}

if ( ! function_exists( 'splendid_tel_href' ) ) {
	/**
	 * A tel: href built from the stored dial string.
	 *
	 * @return string
	 */
	function splendid_tel_href() {
		$raw = preg_replace( '/[^0-9+]/', '', (string) splendid_option( 'phone_tel' ) );

		if ( '' === $raw ) {
			$raw = preg_replace( '/[^0-9+]/', '', (string) splendid_option( 'phone' ) );
		}

		return 'tel:' . $raw;
	}
}

if ( ! function_exists( 'splendid_url' ) ) {
	/**
	 * Resolve an internal path such as /free-quote to a real permalink on this
	 * install, so the design's readable paths survive a subdirectory install or
	 * a slug change.
	 *
	 * @param string $path Absolute site path.
	 * @return string
	 */
	function splendid_url( $path ) {
		$path = trim( (string) $path );

		if ( '' === $path || preg_match( '#^(https?:|tel:|mailto:|\#)#i', $path ) ) {
			return $path;
		}

		return home_url( '/' . ltrim( $path, '/' ) );
	}
}
