<?php
/**
 * Server-rendered blocks.
 *
 * These cover the parts of the design that are behaviour or live data: the
 * enquiry form, the closing CTA, contact details from settings, the filtered
 * inspiration grid, and the card grids that list real pages. Everything else on
 * a page is core blocks, so an editor can change it without touching code.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

require_once SPLENDID_CORE_DIR . 'includes/form.php';

/**
 * Register every block and its editor script.
 */
function splendid_blocks_register() {
	$blocks = array(
		'enquiry-form'    => array(
			'render'     => 'splendid_block_enquiry_form',
			'attributes' => array(
				'variant' => array( 'type' => 'string', 'default' => 'contact' ),
				'eyebrow' => array( 'type' => 'string', 'default' => 'LET&rsquo;S GET THE DETAILS RIGHT' ),
				'heading' => array( 'type' => 'string', 'default' => 'Your next chapter starts here.' ),
				'intro'   => array( 'type' => 'string', 'default' => '' ),
			),
		),
		'cta-band'        => array(
			'render'     => 'splendid_block_cta_band',
			'attributes' => array(
				'eyebrow'  => array( 'type' => 'string', 'default' => 'LET&rsquo;S MAKE IT SPLENDID' ),
				'heading'  => array( 'type' => 'string', 'default' => 'Your home.<br><em>With a fresh perspective.</em>' ),
				'text'     => array( 'type' => 'string', 'default' => 'A single window or a complete transformation.<br>Tell us what you have in mind.' ),
				'ctaLabel' => array( 'type' => 'string', 'default' => 'Start your project' ),
				'ctaUrl'   => array( 'type' => 'string', 'default' => '/free-quote' ),
			),
		),
		'contact-methods' => array(
			'render'     => 'splendid_block_contact_methods',
			'attributes' => array(
				'note' => array( 'type' => 'string', 'default' => '' ),
			),
		),
		'gallery'         => array(
			'render'     => 'splendid_block_gallery',
			'attributes' => array(
				'items'   => array( 'type' => 'array', 'default' => array() ),
				'filters' => array( 'type' => 'array', 'default' => array( 'All', 'Windows', 'Doors', 'Living spaces' ) ),
				'note'    => array(
					'type'    => 'string',
					'default' => 'Illustrative design imagery created for inspiration; these are not photographs of completed Splendid installations.',
				),
			),
		),
		'product-grid'    => array(
			'render'     => 'splendid_block_product_grid',
			'attributes' => array(
				'parent' => array( 'type' => 'string', 'default' => 'windows' ),
				'tinted' => array( 'type' => 'boolean', 'default' => false ),
			),
		),
		'area-links'      => array(
			'render'     => 'splendid_block_area_links',
			'attributes' => array(
				'limit' => array( 'type' => 'number', 'default' => 6 ),
			),
		),
		'article-grid'    => array(
			'render'     => 'splendid_block_article_grid',
			'attributes' => array(
				'parent' => array( 'type' => 'string', 'default' => 'blog' ),
				'limit'  => array( 'type' => 'number', 'default' => 3 ),
			),
		),
	);

	wp_register_script(
		'splendid-blocks-editor',
		SPLENDID_CORE_URL . 'assets/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n' ),
		SPLENDID_CORE_VERSION,
		true
	);

	foreach ( $blocks as $name => $config ) {
		register_block_type( 'splendid/' . $name, array(
			'api_version'     => 3,
			'editor_script'   => 'splendid-blocks-editor',
			'attributes'      => $config['attributes'],
			'render_callback' => $config['render'],
			'supports'        => array( 'html' => false, 'anchor' => true ),
		) );
	}
}
add_action( 'init', 'splendid_blocks_register' );

/**
 * Front-end script for the enquiry form.
 */
function splendid_blocks_assets() {
	if ( ! has_block( 'splendid/enquiry-form' ) && ! is_admin() ) {
		return;
	}

	wp_enqueue_script(
		'splendid-enquiry',
		SPLENDID_CORE_URL . 'assets/enquiry.js',
		array(),
		SPLENDID_CORE_VERSION,
		true
	);

	wp_localize_script( 'splendid-enquiry', 'splendidEnquiry', array(
		'nonceUrl' => rest_url( 'splendid/v1/nonce' ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
		'strings'  => array(
			'summary' => wp_strip_all_tags( splendid_enquiry_copy( 'summary' ) ),
			'failure' => wp_strip_all_tags( splendid_enquiry_copy( 'failure' ) ),
			'offline' => __( 'You appear to be offline. Your details are still here — please try again when you are connected.', 'splendid-core' ),
		),
		'received' => splendid_form_received_markup(),
	) );
}
add_action( 'wp_enqueue_scripts', 'splendid_blocks_assets' );

/* -------------------------------------------------------------------------
 * Render callbacks
 * ---------------------------------------------------------------------- */

/**
 * Enquiry form block.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_enquiry_form( $atts ) {
	return splendid_form_markup( $atts );
}

/**
 * Closing CTA band.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_cta_band( $atts ) {
	if ( function_exists( 'splendid_cta_band_markup' ) ) {
		return splendid_cta_band_markup( $atts );
	}

	return '';
}

/**
 * Phone, email and directions, all from settings.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_contact_methods( $atts ) {
	$note = ! empty( $atts['note'] ) ? $atts['note'] : splendid_option( 'visit_note' );

	ob_start();
	?>
	<div class="contact-methods">
		<a href="<?php echo esc_url( splendid_tel_href() ); ?>">
			<?php echo splendid_icon( 'phone', 21 ); // phpcs:ignore ?>
			<span><?php esc_html_e( 'Call the team', 'splendid-core' ); ?><strong><?php echo esc_html( splendid_option( 'phone' ) ); ?></strong></span>
		</a>
		<a href="mailto:<?php echo esc_attr( splendid_option( 'email' ) ); ?>">
			<?php echo splendid_icon( 'mail', 21 ); // phpcs:ignore ?>
			<span><?php esc_html_e( 'Email us', 'splendid-core' ); ?><strong><?php echo esc_html( splendid_option( 'email' ) ); ?></strong></span>
		</a>
		<a href="<?php echo esc_url( splendid_option( 'maps_url' ) ); ?>" target="_blank" rel="noopener noreferrer">
			<?php echo splendid_icon( 'map-pin', 21 ); // phpcs:ignore ?>
			<span><?php esc_html_e( 'Find us', 'splendid-core' ); ?><strong><?php echo esc_html( splendid_option( 'address' ) ); ?></strong>
				<span class="screen-reader-text"><?php esc_html_e( '(opens Google Maps in a new tab)', 'splendid-core' ); ?></span>
			</span>
		</a>
	</div>
	<?php if ( $note ) : ?>
		<p class="small-note"><?php echo esc_html( $note ); ?></p>
	<?php endif; ?>
	<?php
	return (string) ob_get_clean();
}

/**
 * Inspiration grid with working filters.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_gallery( $atts ) {
	$items = ! empty( $atts['items'] ) && is_array( $atts['items'] ) ? $atts['items'] : splendid_gallery_default_items();

	if ( ! $items ) {
		return '<p class="gallery-empty">' . esc_html__( 'Inspiration images will appear here.', 'splendid-core' ) . '</p>';
	}

	$filters = ! empty( $atts['filters'] ) && is_array( $atts['filters'] ) ? $atts['filters'] : array( 'All' );

	ob_start();
	?>
	<div class="filter-row" aria-label="<?php esc_attr_e( 'Filter inspiration', 'splendid-core' ); ?>">
		<?php foreach ( $filters as $index => $filter ) : ?>
			<button type="button"
				data-filter="<?php echo esc_attr( $filter ); ?>"
				class="<?php echo 0 === $index ? 'active' : ''; ?>"
				aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>">
				<?php echo esc_html( $filter ); ?>
			</button>
		<?php endforeach; ?>
	</div>
	<div class="collection-grid">
		<?php foreach ( $items as $item ) : ?>
			<a class="image-card"
				data-gallery-type="<?php echo esc_attr( isset( $item['type'] ) ? $item['type'] : '' ); ?>"
				href="<?php echo esc_url( splendid_url( isset( $item['href'] ) ? $item['href'] : '/' ) ); ?>">
				<div class="card-photo">
					<img src="<?php echo esc_url( isset( $item['image'] ) ? $item['image'] : '' ); ?>"
						alt="<?php echo esc_attr( isset( $item['alt'] ) ? $item['alt'] : '' ); ?>"
						width="1200" height="800" loading="lazy" decoding="async">
					<span class="round-arrow"><?php echo splendid_icon( 'arrow-up-right', 22 ); // phpcs:ignore ?></span>
				</div>
				<div class="card-caption">
					<h3><?php echo esc_html( isset( $item['title'] ) ? $item['title'] : '' ); ?></h3>
					<span><?php echo esc_html( isset( $item['type'] ) ? $item['type'] : '' ); ?></span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
	<p class="gallery-empty" hidden><?php esc_html_e( 'No inspiration in this category yet. Choose another filter, or talk to the team about your ideas.', 'splendid-core' ); ?></p>
	<?php if ( ! empty( $atts['note'] ) ) : ?>
		<p class="small-note"><?php echo esc_html( $atts['note'] ); ?></p>
	<?php endif; ?>
	<?php
	return (string) ob_get_clean();
}

/**
 * The three supplied illustrations, resolved to Media Library URLs when imported.
 *
 * @return array
 */
function splendid_gallery_default_items() {
	return array(
		array(
			'type'  => 'Windows',
			'title' => 'Light, from every angle',
			'image' => splendid_media_url( 'bay-window.webp' ),
			'alt'   => 'Sunlit room with a white bay window',
			'href'  => '/windows/bay-windows',
		),
		array(
			'type'  => 'Doors',
			'title' => 'An entrance with character',
			'image' => splendid_media_url( 'composite-door.webp' ),
			'alt'   => 'Charcoal entrance door in a brick home',
			'href'  => '/doors/composite-doors',
		),
		array(
			'type'  => 'Living spaces',
			'title' => 'A connection to the outdoors',
			'image' => splendid_media_url( 'sliding-glazing.webp' ),
			'alt'   => 'Glazing inspiration for a contemporary home',
			'href'  => '/doors/patio-doors',
		),
	);
}

/**
 * Resolve an image to its Media Library URL, falling back to the theme copy.
 *
 * @param string $filename Image file name.
 * @return string
 */
function splendid_media_url( $filename ) {
	$map = get_option( 'splendid_media_map', array() );

	if ( is_array( $map ) && ! empty( $map[ $filename ]['url'] ) ) {
		return $map[ $filename ]['url'];
	}

	return get_template_directory_uri() . '/assets/images/' . $filename;
}

/**
 * Product cards listing the real child pages of a hub.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_product_grid( $atts ) {
	$parent = isset( $atts['parent'] ) ? sanitize_title( $atts['parent'] ) : 'windows';
	$hub    = get_page_by_path( $parent );

	if ( ! $hub ) {
		return '<p class="splendid-block-placeholder">' . esc_html__( 'Add the product pages beneath this hub to list them here.', 'splendid-core' ) . '</p>';
	}

	$children = get_pages( array(
		'parent'      => $hub->ID,
		'sort_column' => 'menu_order,post_title',
		'post_status' => array( 'publish', 'private' ),
	) );

	if ( ! $children ) {
		return '<p class="splendid-block-placeholder">' . esc_html__( 'No product pages found beneath this hub yet.', 'splendid-core' ) . '</p>';
	}

	ob_start();
	?>
	<div class="product-grid<?php echo ! empty( $atts['tinted'] ) ? ' tinted' : ''; ?>">
		<?php foreach ( $children as $index => $child ) : ?>
			<?php $tag = get_post_meta( $child->ID, '_splendid_tag', true ); ?>
			<a class="product-card" href="<?php echo esc_url( get_permalink( $child ) ); ?>">
				<span class="eyebrow">
					<?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?><?php echo $tag ? ' / ' . esc_html( strtoupper( $tag ) ) : ''; ?>
				</span>
				<h3><?php echo esc_html( get_the_title( $child ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $child ) ); ?></p>
				<span class="text-link">
					<?php
					/* translators: %s: product name in lower case. */
					echo esc_html( sprintf( __( 'Explore %s', 'splendid-core' ), strtolower( get_the_title( $child ) ) ) );
					?>
					<?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Links to the published local area pages.
 *
 * Unpublished area pages are never listed, which keeps unconfirmed coverage off
 * the live site until the client has confirmed it.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_area_links( $atts ) {
	$limit = isset( $atts['limit'] ) ? (int) $atts['limit'] : 6;

	$areas = get_pages( array(
		'meta_key'    => '_splendid_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'  => 'local-area', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'sort_column' => 'menu_order,post_title',
		'number'      => $limit > 0 ? $limit : 0,
		'post_status' => 'publish',
	) );

	if ( ! $areas ) {
		return '<p class="splendid-block-placeholder">' . esc_html__( 'Local area pages appear here once they are published.', 'splendid-core' ) . '</p>';
	}

	ob_start();
	?>
	<div class="area-links">
		<?php foreach ( $areas as $area ) : ?>
			<?php
			$label = get_post_meta( $area->ID, '_splendid_area_name', true );
			$label = $label ? $label : get_the_title( $area );
			?>
			<a href="<?php echo esc_url( get_permalink( $area ) ); ?>">
				<?php echo esc_html( $label ); ?><?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Advice article cards.
 *
 * @param array $atts Attributes.
 * @return string
 */
function splendid_block_article_grid( $atts ) {
	$parent = isset( $atts['parent'] ) ? sanitize_title( $atts['parent'] ) : 'blog';
	$limit  = isset( $atts['limit'] ) ? (int) $atts['limit'] : 3;
	$hub    = get_page_by_path( $parent );

	$articles = array();

	if ( $hub ) {
		$articles = get_pages( array(
			'parent'      => $hub->ID,
			'sort_column' => 'menu_order,post_date',
			'number'      => $limit > 0 ? $limit : 0,
			'post_status' => 'publish',
		) );
	}

	if ( ! $articles ) {
		$articles = get_posts( array( 'numberposts' => $limit ) );
	}

	if ( ! $articles ) {
		return '<p class="splendid-block-placeholder">' . esc_html__( 'Articles appear here once they are published.', 'splendid-core' ) . '</p>';
	}

	ob_start();
	?>
	<div class="product-grid">
		<?php foreach ( $articles as $article ) : ?>
			<?php $category = get_post_meta( $article->ID, '_splendid_category', true ); ?>
			<a class="product-card" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
				<?php if ( $category ) : ?>
					<span class="eyebrow"><?php echo esc_html( strtoupper( $category ) ); ?></span>
				<?php endif; ?>
				<h3><?php echo esc_html( get_the_title( $article ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $article ) ); ?></p>
				<span class="text-link">
					<?php esc_html_e( 'Read the article', 'splendid-core' ); ?> <?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * A category so the Splendid blocks are easy to find in the inserter.
 *
 * @param array $categories Block categories.
 * @return array
 */
function splendid_block_category( $categories ) {
	array_unshift( $categories, array(
		'slug'  => 'splendid',
		'title' => __( 'Splendid', 'splendid-core' ),
		'icon'  => null,
	) );

	return $categories;
}
add_filter( 'block_categories_all', 'splendid_block_category' );
