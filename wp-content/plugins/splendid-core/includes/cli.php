<?php
/**
 * WP-CLI commands.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Splendid content commands.
 */
class Splendid_CLI {

	/**
	 * Import the website content.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Overwrite pages that have been edited since the last import.
	 *
	 * [--no-menus]
	 * : Skip building the navigation menus.
	 *
	 * [--dry-run]
	 * : Report what would change without writing anything.
	 *
	 * ## EXAMPLES
	 *
	 *     wp splendid import --dry-run
	 *     wp splendid import
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Options.
	 */
	public function import( $args, $assoc_args ) {
		$report = splendid_import_run( array(
			'force'   => isset( $assoc_args['force'] ),
			'menus'   => ! isset( $assoc_args['no-menus'] ),
			'dry_run' => isset( $assoc_args['dry-run'] ),
		) );

		foreach ( $report as $line ) {
			WP_CLI::log( $line );
		}

		WP_CLI::success( sprintf( '%d lines.', count( $report ) ) );
	}

	/**
	 * Check that every manifest route resolves, and report its status.
	 *
	 * ## EXAMPLES
	 *
	 *     wp splendid verify
	 */
	public function verify() {
		$manifest = splendid_import_manifest();
		$rows     = array();

		foreach ( $manifest as $item ) {
			$path = $item['parent'] ? $item['parent'] . '/' . $item['slug'] : $item['slug'];
			$page = get_page_by_path( $path );

			$rows[] = array(
				'route'    => $item['route'],
				'template' => $item['template'],
				'status'   => $page ? $page->post_status : 'MISSING',
				'words'    => $page ? str_word_count( wp_strip_all_tags( $page->post_content ) ) : 0,
				'seo'      => $page && get_post_meta( $page->ID, '_splendid_meta_description', true ) ? 'yes' : 'no',
			);
		}

		WP_CLI\Utils\format_items( 'table', $rows, array( 'route', 'template', 'status', 'words', 'seo' ) );
	}
}

WP_CLI::add_command( 'splendid', 'Splendid_CLI' );
