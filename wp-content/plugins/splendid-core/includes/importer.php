<?php
/**
 * Repeatable content import.
 *
 * Creates the 38 routes from content/manifest.json, imports the supplied images
 * into the Media Library, records the source-to-media URL mapping and builds the
 * navigation menus. Running it twice does not duplicate anything, and it never
 * overwrites a page an editor has since changed unless asked to.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

const SPLENDID_IMPORT_OPTION = 'splendid_imported';
const SPLENDID_MEDIA_OPTION  = 'splendid_media_map';

/**
 * Read the manifest.
 *
 * @return array
 */
function splendid_import_manifest() {
	$file = SPLENDID_CORE_DIR . 'content/manifest.json';

	if ( ! file_exists( $file ) ) {
		return array();
	}

	$manifest = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	return is_array( $manifest ) ? $manifest : array();
}

/**
 * Import the three supplied images into the Media Library, once.
 *
 * @return array filename => array( id, url )
 */
function splendid_import_media() {
	$map = get_option( SPLENDID_MEDIA_OPTION, array() );

	if ( ! is_array( $map ) ) {
		$map = array();
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$source_dir = get_template_directory() . '/assets/images/';
	$files      = array(
		'bay-window.webp'      => 'Sunlit room with a white bay window framing a garden view',
		'composite-door.webp'  => 'Charcoal composite entrance door set into a brick home',
		'sliding-glazing.webp' => 'Contemporary brick home with charcoal sliding glazing opening onto a garden',
	);

	foreach ( $files as $filename => $alt ) {
		if ( ! empty( $map[ $filename ]['id'] ) && get_post( $map[ $filename ]['id'] ) ) {
			continue;
		}

		$path = $source_dir . $filename;

		if ( ! file_exists( $path ) ) {
			continue;
		}

		$temp = wp_tempnam( $filename );

		if ( ! $temp ) {
			continue;
		}

		copy( $path, $temp );

		$attachment_id = media_handle_sideload(
			array(
				'name'     => $filename,
				'tmp_name' => $temp,
			),
			0,
			null,
			array( 'post_title' => ucwords( str_replace( array( '-', '.webp' ), array( ' ', '' ), $filename ) ) )
		);

		if ( is_wp_error( $attachment_id ) ) {
			if ( file_exists( $temp ) ) {
				wp_delete_file( $temp );
			}
			continue;
		}

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

		$map[ $filename ] = array(
			'id'  => (int) $attachment_id,
			'url' => wp_get_attachment_url( $attachment_id ),
		);
	}

	update_option( SPLENDID_MEDIA_OPTION, $map, false );

	return $map;
}

/**
 * Rewrite the design's image paths to Media Library URLs and IDs.
 *
 * @param string $content Block markup.
 * @param array  $map     Media map.
 * @return string
 */
function splendid_import_map_media( $content, $map ) {
	foreach ( $map as $filename => $media ) {
		if ( empty( $media['url'] ) ) {
			continue;
		}

		// splendid/image and splendid/image-card blocks carry a src attribute;
		// give them the attachment id too so srcset and dimensions are emitted.
		$content = str_replace(
			'"src":"/images/' . $filename . '"',
			'"id":' . (int) $media['id'] . ',"src":"' . esc_url_raw( $media['url'] ) . '"',
			$content
		);

		$content = str_replace(
			'"image":"/images/' . $filename . '"',
			'"image":"' . esc_url_raw( $media['url'] ) . '"',
			$content
		);

		$content = str_replace( '/images/' . $filename, esc_url_raw( $media['url'] ), $content );
	}

	return $content;
}

/**
 * Run the import.
 *
 * @param array $args {
 *     @type bool $force   Overwrite pages an editor has changed.
 *     @type bool $menus   Build and assign the navigation menus.
 *     @type bool $dry_run Report without writing.
 * }
 * @return array Report lines.
 */
function splendid_import_run( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'force'   => false,
		'menus'   => true,
		'dry_run' => false,
	) );

	$manifest = splendid_import_manifest();
	$report   = array();

	if ( ! $manifest ) {
		return array( 'error' => __( 'No manifest found. Rebuild it with tools/build-content.py.', 'splendid-core' ) );
	}

	$media    = $args['dry_run'] ? get_option( SPLENDID_MEDIA_OPTION, array() ) : splendid_import_media();
	$imported = get_option( SPLENDID_IMPORT_OPTION, array() );

	if ( ! is_array( $imported ) ) {
		$imported = array();
	}

	// Parents must exist before their children.
	usort( $manifest, static function ( $a, $b ) {
		$a_depth = $a['parent'] ? 1 : 0;
		$b_depth = $b['parent'] ? 1 : 0;

		if ( $a_depth === $b_depth ) {
			return $a['menu_order'] <=> $b['menu_order'];
		}

		return $a_depth <=> $b_depth;
	} );

	$front_id = 0;

	foreach ( $manifest as $item ) {
		$file = SPLENDID_CORE_DIR . 'content/pages/' . $item['file'];

		if ( ! file_exists( $file ) ) {
			$report[] = sprintf( 'MISSING  %s (%s)', $item['route'], $item['file'] );
			continue;
		}

		$content = splendid_import_map_media(
			(string) file_get_contents( $file ), // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$media
		);

		$parent_id = 0;

		if ( $item['parent'] ) {
			$parent = get_page_by_path( $item['parent'] );

			if ( $parent ) {
				$parent_id = $parent->ID;
			}
		}

		$path     = $item['parent'] ? $item['parent'] . '/' . $item['slug'] : $item['slug'];
		$existing = get_page_by_path( $path );

		if ( ! $existing && ! empty( $imported[ $item['route'] ] ) ) {
			$candidate = get_post( (int) $imported[ $item['route'] ] );

			if ( $candidate && SPLENDID_LEAD_POST_TYPE !== $candidate->post_type ) {
				$existing = $candidate;
			}
		}

		$postarr = array(
			'post_type'    => 'page',
			'post_title'   => $item['title'],
			'post_name'    => $item['slug'],
			'post_content' => $content,
			'post_excerpt' => $item['excerpt'],
			'post_parent'  => $parent_id,
			'post_status'  => $item['status'],
			'menu_order'   => $item['menu_order'],
		);

		if ( $existing ) {
			$stored_hash = get_post_meta( $existing->ID, '_splendid_import_hash', true );
			$edited      = $stored_hash && md5( $existing->post_content ) !== $stored_hash;

			if ( $edited && ! $args['force'] ) {
				$report[] = sprintf( 'SKIPPED  %s (edited since import)', $item['route'] );
				continue;
			}

			$postarr['ID'] = $existing->ID;

			// An editor's publish decision is theirs: never republish or unpublish.
			$postarr['post_status'] = $existing->post_status;

			if ( $args['dry_run'] ) {
				$report[] = sprintf( 'UPDATE   %s', $item['route'] );
				continue;
			}

			$post_id = wp_update_post( $postarr, true );
			$action  = 'UPDATED ';
		} else {
			if ( $args['dry_run'] ) {
				$report[] = sprintf( 'CREATE   %s (%s)', $item['route'], $item['status'] );
				continue;
			}

			$post_id = wp_insert_post( $postarr, true );
			$action  = 'CREATED ';
		}

		if ( is_wp_error( $post_id ) ) {
			$report[] = sprintf( 'FAILED   %s: %s', $item['route'], $post_id->get_error_message() );
			continue;
		}

		update_post_meta( $post_id, '_splendid_import_hash', md5( $content ) );
		update_post_meta( $post_id, '_splendid_seo_title', $item['seo_title'] );
		update_post_meta( $post_id, '_splendid_meta_description', $item['meta_description'] );

		foreach ( $item['meta'] as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		// Local pages stay out of the index until real coverage is confirmed.
		if ( 'local-area' === $item['template'] ) {
			update_post_meta( $post_id, '_splendid_noindex', 1 );
		}

		if ( ! empty( $item['front_page'] ) ) {
			$front_id = $post_id;
		}

		$imported[ $item['route'] ] = $post_id;
		$report[]                   = sprintf( '%s %s', $action, $item['route'] );
	}

	if ( $args['dry_run'] ) {
		return $report;
	}

	update_option( SPLENDID_IMPORT_OPTION, $imported, false );

	if ( $front_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_id );
		$report[] = 'FRONT    / -> ' . $front_id;
	}

	// Path-based permalinks are required for the design's URLs.
	if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();
		$report[] = 'PERMALINKS /%postname%/';
	}

	if ( ! get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'Europe/London' );
		$report[] = 'TIMEZONE Europe/London';
	}

	if ( $args['menus'] ) {
		$report = array_merge( $report, splendid_import_menus( $imported ) );
	}

	return $report;
}

/**
 * Build the navigation menus from the imported pages.
 *
 * Existing menus are left alone, so a client's own menu edits survive a re-run.
 *
 * @param array $imported route => post id.
 * @return array Report lines.
 */
function splendid_import_menus( $imported ) {
	$report     = array();
	$structures = array(
		'primary'         => array(
			'name'  => 'Main navigation',
			'items' => array(
				array( 'route' => '/windows', 'description' => 'FRAME YOUR EVERYDAY', 'attr_title' => 'Explore all windows', 'children' => array( '/windows/double-glazing', '/windows/triple-glazing', '/windows/upvc-windows', '/windows/aluminium-windows', '/windows/sash-windows', '/windows/bay-windows' ) ),
				array( 'route' => '/doors', 'description' => 'MAKE AN ENTRANCE', 'attr_title' => 'Explore all doors', 'children' => array( '/doors/composite-doors', '/doors/upvc-doors', '/doors/aluminium-doors', '/doors/bifold-doors', '/doors/patio-doors', '/doors/french-doors' ) ),
				array( 'route' => '/conservatories' ),
				array( 'route' => '/gallery', 'label' => 'Inspiration' ),
				array( 'route' => '/about', 'label' => 'Our story' ),
			),
		),
		'mobile'          => array(
			'name'  => 'Mobile drawer',
			'items' => array(
				array( 'route' => '/windows', 'children' => array( '/windows/double-glazing', '/windows/triple-glazing', '/windows/upvc-windows', '/windows/aluminium-windows', '/windows/sash-windows', '/windows/bay-windows' ) ),
				array( 'route' => '/doors', 'children' => array( '/doors/composite-doors', '/doors/upvc-doors', '/doors/aluminium-doors', '/doors/bifold-doors', '/doors/patio-doors', '/doors/french-doors' ) ),
				array( 'route' => '/conservatories' ),
				array( 'route' => '/porches' ),
				array( 'route' => '/gallery', 'label' => 'Inspiration' ),
				array( 'route' => '/about', 'label' => 'Our story' ),
				array( 'route' => '/service-areas', 'label' => 'Areas we cover' ),
				array( 'route' => '/blog', 'label' => 'Advice' ),
				array( 'route' => '/contact', 'label' => 'Contact' ),
				array( 'route' => '/free-quote', 'label' => 'Start your quote' ),
			),
		),
		'footer_windows'  => array(
			'name'  => 'Windows',
			'items' => array(
				array( 'route' => '/windows/double-glazing' ),
				array( 'route' => '/windows/triple-glazing' ),
				array( 'route' => '/windows/upvc-windows' ),
				array( 'route' => '/windows/aluminium-windows' ),
				array( 'route' => '/windows/sash-windows' ),
				array( 'route' => '/windows/bay-windows' ),
			),
		),
		'footer_doors'    => array(
			'name'  => 'Doors & more',
			'items' => array(
				array( 'route' => '/doors/composite-doors' ),
				array( 'route' => '/doors/upvc-doors' ),
				array( 'route' => '/doors/aluminium-doors' ),
				array( 'route' => '/doors/bifold-doors' ),
				array( 'route' => '/doors/patio-doors' ),
				array( 'route' => '/doors/french-doors' ),
				array( 'route' => '/conservatories' ),
				array( 'route' => '/porches' ),
			),
		),
		'footer_discover' => array(
			'name'  => 'Discover Splendid',
			'items' => array(
				array( 'route' => '/about', 'label' => 'Our story' ),
				array( 'route' => '/gallery', 'label' => 'Inspiration gallery' ),
				array( 'route' => '/reviews', 'label' => 'Customer reviews' ),
				array( 'route' => '/blog', 'label' => 'Advice & ideas' ),
				array( 'route' => '/service-areas', 'label' => 'Areas we cover' ),
				array( 'route' => '/estimate-calculator', 'label' => 'Quote planner' ),
				array( 'route' => '/contact', 'label' => 'Contact us' ),
			),
		),
		'footer_legal'    => array(
			'name'  => 'Legal',
			'items' => array(
				array( 'route' => '/privacy-policy', 'label' => 'Privacy' ),
				array( 'route' => '/cookie-policy', 'label' => 'Cookies' ),
				array( 'route' => '/terms-of-service', 'label' => 'Terms' ),
			),
		),
	);

	$locations = get_nav_menu_locations();

	foreach ( $structures as $location => $structure ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			$report[] = sprintf( 'MENU     %s already assigned', $location );
			continue;
		}

		$menu = wp_get_nav_menu_object( $structure['name'] );

		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $structure['name'] );

			if ( is_wp_error( $menu_id ) ) {
				$report[] = sprintf( 'MENU     %s failed: %s', $location, $menu_id->get_error_message() );
				continue;
			}
		} else {
			$menu_id = $menu->term_id;
		}

		if ( ! wp_get_nav_menu_items( $menu_id ) ) {
			foreach ( $structure['items'] as $item ) {
				$parent_item = splendid_import_menu_item( $menu_id, $item, $imported, 0 );

				if ( ! empty( $item['children'] ) && $parent_item ) {
					foreach ( $item['children'] as $child_route ) {
						splendid_import_menu_item( $menu_id, array( 'route' => $child_route ), $imported, $parent_item );
					}
				}
			}
		}

		$locations[ $location ] = $menu_id;
		$report[]               = sprintf( 'MENU     %s -> %s', $location, $structure['name'] );
	}

	set_theme_mod( 'nav_menu_locations', $locations );

	return $report;
}

/**
 * Add one menu item.
 *
 * @param int   $menu_id   Menu term id.
 * @param array $item      Item definition.
 * @param array $imported  route => post id.
 * @param int   $parent_id Parent menu item id.
 * @return int Menu item id.
 */
function splendid_import_menu_item( $menu_id, $item, $imported, $parent_id ) {
	$post_id = isset( $imported[ $item['route'] ] ) ? (int) $imported[ $item['route'] ] : 0;

	if ( ! $post_id ) {
		return 0;
	}

	$menu_item_id = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'       => isset( $item['label'] ) ? $item['label'] : get_the_title( $post_id ),
		'menu-item-object'      => 'page',
		'menu-item-object-id'   => $post_id,
		'menu-item-type'        => 'post_type',
		'menu-item-status'      => 'publish',
		'menu-item-parent-id'   => $parent_id,
		'menu-item-description' => isset( $item['description'] ) ? $item['description'] : '',
		'menu-item-attr-title'  => isset( $item['attr_title'] ) ? $item['attr_title'] : '',
	) );

	return is_wp_error( $menu_item_id ) ? 0 : (int) $menu_item_id;
}

/**
 * The import panel on the settings screen.
 */
function splendid_importer_admin_panel() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$report = array();

	if ( isset( $_POST['splendid_import'] ) && check_admin_referer( 'splendid_import' ) ) {
		$report = splendid_import_run( array(
			'force'   => ! empty( $_POST['splendid_force'] ),
			'dry_run' => ! empty( $_POST['splendid_dry_run'] ),
		) );
	}

	$manifest = splendid_import_manifest();
	?>
	<p class="description">
		<?php
		printf(
			/* translators: %d: number of routes. */
			esc_html__( 'The package contains %d routes. Importing creates any that are missing and refreshes those that have not been edited since the last import. Local area pages and the three legal notices are imported as drafts on purpose.', 'splendid-core' ),
			count( $manifest )
		);
		?>
	</p>
	<form method="post">
		<?php wp_nonce_field( 'splendid_import' ); ?>
		<p>
			<label><input type="checkbox" name="splendid_dry_run" value="1"> <?php esc_html_e( 'Preview only (make no changes)', 'splendid-core' ); ?></label><br>
			<label><input type="checkbox" name="splendid_force" value="1"> <?php esc_html_e( 'Overwrite pages that have been edited since the last import', 'splendid-core' ); ?></label>
		</p>
		<?php submit_button( __( 'Import content', 'splendid-core' ), 'secondary', 'splendid_import', false ); ?>
	</form>
	<?php if ( $report ) : ?>
		<h3><?php esc_html_e( 'Result', 'splendid-core' ); ?></h3>
		<pre style="max-height:320px;overflow:auto;background:#fff;border:1px solid #dcdcde;padding:12px"><?php echo esc_html( implode( "\n", $report ) ); ?></pre>
	<?php endif; ?>
	<?php
}
