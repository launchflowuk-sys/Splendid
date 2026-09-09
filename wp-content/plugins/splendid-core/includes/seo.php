<?php
/**
 * A single SEO implementation.
 *
 * If a dedicated SEO plugin is active this file stands down completely, so the
 * site never emits two titles, two descriptions, two canonicals or two schema
 * graphs. Nothing here invents ratings, prices, opening hours or certifications.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is another SEO plugin handling output?
 *
 * @return bool
 */
function splendid_seo_deferred() {
	$signals = array(
		'WPSEO_VERSION',                 // Yoast SEO.
		'RANK_MATH_VERSION',             // Rank Math.
		'AIOSEO_VERSION',                // All in One SEO.
		'SEOPRESS_VERSION',              // SEOPress.
		'SLIM_SEO_VERSION',              // Slim SEO.
	);

	foreach ( $signals as $constant ) {
		if ( defined( $constant ) ) {
			return true;
		}
	}

	/**
	 * Filter whether Splendid Core should stop emitting SEO tags.
	 *
	 * @param bool $deferred True to stand down.
	 */
	return (bool) apply_filters( 'splendid_seo_deferred', false );
}

/**
 * Register the SEO fields so they are editable and importable.
 */
function splendid_seo_register_meta() {
	$fields = array(
		'_splendid_seo_title'        => 'string',
		'_splendid_meta_description' => 'string',
		'_splendid_noindex'          => 'boolean',
	);

	foreach ( array( 'page', 'post' ) as $type ) {
		foreach ( $fields as $key => $data_type ) {
			register_post_meta( $type, $key, array(
				'type'          => $data_type,
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
			) );
		}
	}
}
add_action( 'init', 'splendid_seo_register_meta' );

/**
 * The editor panel for the SEO fields.
 */
function splendid_seo_meta_box() {
	if ( splendid_seo_deferred() ) {
		return;
	}

	add_meta_box(
		'splendid-seo',
		__( 'Search appearance', 'splendid-core' ),
		'splendid_seo_meta_box_render',
		array( 'page', 'post' ),
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'splendid_seo_meta_box' );

/**
 * Render the SEO panel.
 *
 * @param WP_Post $post Post.
 */
function splendid_seo_meta_box_render( $post ) {
	wp_nonce_field( 'splendid_seo_save', 'splendid_seo_nonce' );

	$title       = get_post_meta( $post->ID, '_splendid_seo_title', true );
	$description = get_post_meta( $post->ID, '_splendid_meta_description', true );
	$noindex     = get_post_meta( $post->ID, '_splendid_noindex', true );
	?>
	<p>
		<label for="splendid-seo-title"><strong><?php esc_html_e( 'Search engine title', 'splendid-core' ); ?></strong></label><br>
		<input type="text" class="widefat" id="splendid-seo-title" name="splendid_seo_title" value="<?php echo esc_attr( $title ); ?>">
		<span class="description"><?php esc_html_e( 'Leave empty to use the page title. Around 60 characters shows in full.', 'splendid-core' ); ?></span>
	</p>
	<p>
		<label for="splendid-seo-description"><strong><?php esc_html_e( 'Meta description', 'splendid-core' ); ?></strong></label><br>
		<textarea class="widefat" rows="3" id="splendid-seo-description" name="splendid_seo_description"><?php echo esc_textarea( $description ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Around 155 characters. Describe this page, not the whole business.', 'splendid-core' ); ?></span>
	</p>
	<p>
		<label>
			<input type="checkbox" name="splendid_seo_noindex" value="1" <?php checked( (bool) $noindex ); ?>>
			<?php esc_html_e( 'Ask search engines not to index this page', 'splendid-core' ); ?>
		</label><br>
		<span class="description"><?php esc_html_e( 'Use for pages whose local coverage or evidence is not yet confirmed. Draft and private pages are never indexed anyway.', 'splendid-core' ); ?></span>
	</p>
	<?php
}

/**
 * Save the SEO panel.
 *
 * @param int $post_id Post ID.
 */
function splendid_seo_save( $post_id ) {
	if ( ! isset( $_POST['splendid_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['splendid_seo_nonce'] ) ), 'splendid_seo_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_splendid_seo_title', sanitize_text_field( wp_unslash( isset( $_POST['splendid_seo_title'] ) ? $_POST['splendid_seo_title'] : '' ) ) );
	update_post_meta( $post_id, '_splendid_meta_description', sanitize_textarea_field( wp_unslash( isset( $_POST['splendid_seo_description'] ) ? $_POST['splendid_seo_description'] : '' ) ) );
	update_post_meta( $post_id, '_splendid_noindex', empty( $_POST['splendid_seo_noindex'] ) ? 0 : 1 );
}
add_action( 'save_post', 'splendid_seo_save' );

/**
 * Use the stored SEO title when one is set.
 *
 * @param string $title Title.
 * @return string
 */
function splendid_seo_document_title( $title ) {
	if ( splendid_seo_deferred() || ! is_singular() ) {
		return $title;
	}

	$custom = get_post_meta( get_queried_object_id(), '_splendid_seo_title', true );

	return $custom ? $custom : $title;
}
add_filter( 'pre_get_document_title', 'splendid_seo_document_title', 20 );

/**
 * Emit description, canonical and robots.
 */
function splendid_seo_head() {
	if ( splendid_seo_deferred() ) {
		return;
	}

	$description = '';
	$canonical   = '';
	$noindex     = false;

	if ( is_singular() ) {
		$post_id     = get_queried_object_id();
		$description = get_post_meta( $post_id, '_splendid_meta_description', true );
		$canonical   = get_permalink( $post_id );
		$noindex     = (bool) get_post_meta( $post_id, '_splendid_noindex', true );

		if ( ! $description ) {
			$description = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post_id ) ), 30, '' );
		}
	} elseif ( is_home() ) {
		$canonical   = get_permalink( (int) get_option( 'page_for_posts' ) );
		$description = get_bloginfo( 'description' );
	} elseif ( is_search() || is_404() ) {
		$noindex = true;
	} elseif ( is_archive() ) {
		$canonical = get_post_type_archive_link( get_post_type() );
	}

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	}

	if ( $noindex ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}

	splendid_seo_open_graph( $description, $canonical );
	splendid_seo_schema();
}
add_action( 'wp_head', 'splendid_seo_head', 2 );

/**
 * WordPress prints its own canonical for singular views; ours replaces it.
 */
function splendid_seo_remove_core_canonical() {
	if ( ! splendid_seo_deferred() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}
add_action( 'wp_head', 'splendid_seo_remove_core_canonical', 1 );

/**
 * Minimal social tags. No invented imagery or claims.
 *
 * @param string $description Description.
 * @param string $canonical   Canonical URL.
 */
function splendid_seo_open_graph( $description, $canonical ) {
	$title = wp_get_document_title();

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );

	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $canonical ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
	}

	if ( is_singular() && has_post_thumbnail() ) {
		$image = wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' );

		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		}
	}
}

/**
 * Structured data describing the actual business.
 *
 * Deliberately excludes aggregateRating, openingHours, offers, price and any
 * certification, none of which have been verified.
 */
function splendid_seo_schema() {
	$graph = array();

	$business = array(
		'@type'         => 'HomeAndConstructionBusiness',
		'@id'           => home_url( '/#business' ),
		'name'          => splendid_option( 'company_name' ),
		'url'           => home_url( '/' ),
		'telephone'     => splendid_option( 'phone' ),
		'email'         => splendid_option( 'email' ),
		'address'       => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => splendid_option( 'address_street' ),
			'addressLocality' => splendid_option( 'address_city' ),
			'postalCode'      => splendid_option( 'address_postcode' ),
			'addressCountry'  => splendid_option( 'address_country', 'GB' ),
		),
	);

	$vat = splendid_option( 'vat_number' );

	if ( $vat ) {
		$business['vatID'] = $vat;
	}

	/**
	 * Filter verified sameAs profile URLs.
	 *
	 * Only add profiles that have been confirmed as this business.
	 *
	 * @param array $same_as Profile URLs.
	 */
	$same_as = array_filter( (array) apply_filters( 'splendid_schema_same_as', array() ) );

	if ( $same_as ) {
		$business['sameAs'] = array_values( $same_as );
	}

	$graph[] = $business;

	// Breadcrumbs only where the page really sits beneath a parent.
	if ( is_singular() ) {
		$post = get_queried_object();

		if ( $post && $post->post_parent ) {
			$items    = array();
			$ancestors = array_reverse( get_post_ancestors( $post ) );
			$position = 1;

			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => __( 'Home', 'splendid-core' ),
				'item'     => home_url( '/' ),
			);

			foreach ( $ancestors as $ancestor_id ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => get_the_title( $ancestor_id ),
					'item'     => get_permalink( $ancestor_id ),
				);
			}

			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => get_the_title( $post ),
				'item'     => get_permalink( $post ),
			);

			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $items,
			);
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}

/**
 * Keep noindexed pages out of the XML sitemap.
 *
 * @param array $args Query args.
 * @return array
 */
function splendid_seo_sitemap_query( $args ) {
	$args['meta_query'] = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

	$args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => '_splendid_noindex',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => '_splendid_noindex',
			'value'   => '1',
			'compare' => '!=',
		),
	);

	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'splendid_seo_sitemap_query' );
