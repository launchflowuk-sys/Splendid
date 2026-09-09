<?php
/**
 * Lucide icon paths used by the approved design.
 *
 * Lucide (https://lucide.dev) is distributed under the ISC licence:
 * Copyright (c) for portions of Lucide are held by Cole Bemis 2013-2022 as part of Feather (MIT).
 * All other copyright (c) for Lucide are held by Lucide Contributors 2022.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the inner markup for a named icon.
 *
 * @param string $name Icon name.
 * @return string
 */
function splendid_icon_paths( $name ) {
	$icons = array(
		'arrow-up-right' => '<path d="M7 7h10v10"></path><path d="M7 17 17 7"></path>',
		'arrow-right'    => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
		'arrow-down'     => '<path d="M12 5v14"></path><path d="m19 12-7 7-7-7"></path>',
		'check'          => '<path d="M20 6 9 17l-5-5"></path>',
		'minus'          => '<path d="M5 12h14"></path>',
		'plus'           => '<path d="M5 12h14"></path><path d="M12 5v14"></path>',
		'menu'           => '<path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path>',
		'x'              => '<path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>',
		'mail'           => '<path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect>',
		'phone'          => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>',
		'map-pin'        => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle>',
		'ruler'          => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"></path><path d="m14.5 12.5 2-2"></path><path d="m11.5 9.5 2-2"></path><path d="m8.5 6.5 2-2"></path><path d="m17.5 15.5 2-2"></path>',
		'sun'            => '<circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>',
		'layers'         => '<path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Render an inline SVG icon matching the approved design.
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @param string $extra Extra class names.
 * @return string Safe SVG markup.
 */
function splendid_icon( $name, $size = 17, $extra = '' ) {
	$paths = splendid_icon_paths( $name );

	if ( '' === $paths ) {
		return '';
	}

	$classes = trim( 'lucide lucide-' . $name . ' ' . $extra );

	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="%2$s" aria-hidden="true" focusable="false">%3$s</svg>',
		(int) $size,
		esc_attr( $classes ),
		$paths
	);
}

/**
 * Allow inline SVG icons through wp_kses when echoing mixed markup.
 *
 * @return array
 */
function splendid_kses_svg() {
	$svg = array(
		'svg'    => array(
			'xmlns'            => true,
			'width'            => true,
			'height'           => true,
			'viewbox'          => true,
			'fill'             => true,
			'stroke'           => true,
			'stroke-width'     => true,
			'stroke-linecap'   => true,
			'stroke-linejoin'  => true,
			'class'            => true,
			'aria-hidden'      => true,
			'focusable'        => true,
		),
		'path'   => array( 'd' => true ),
		'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
		'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
	);

	return array_merge( wp_kses_allowed_html( 'post' ), $svg );
}
