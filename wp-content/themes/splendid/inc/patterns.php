<?php
/**
 * Block pattern category.
 *
 * The patterns themselves live in /patterns and are registered automatically by
 * WordPress. They are the approved page sections, so an editor can rebuild or
 * extend any page from the design's own parts.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the pattern category used by every bundled pattern.
 */
function splendid_register_pattern_category() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category( 'splendid', array(
		'label'       => __( 'Splendid sections', 'splendid' ),
		'description' => __( 'Sections from the approved Splendid design.', 'splendid' ),
	) );
}
add_action( 'init', 'splendid_register_pattern_category', 9 );
