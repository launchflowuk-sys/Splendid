<?php
/**
 * Navigation data and rendering.
 *
 * Every menu is editable at Appearance -> Menus. When a location has no menu
 * assigned the theme falls back to the approved structure from
 * briefs/02-navigation-and-page-templates.md so a fresh install is never empty.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

/**
 * The approved fallback structure for every menu location.
 *
 * @param string $location Menu location.
 * @return array
 */
function splendid_default_nav( $location ) {
	$windows = array(
		array( 'label' => 'Double glazing', 'url' => '/windows/double-glazing' ),
		array( 'label' => 'Triple glazing', 'url' => '/windows/triple-glazing' ),
		array( 'label' => 'uPVC windows', 'url' => '/windows/upvc-windows' ),
		array( 'label' => 'Aluminium windows', 'url' => '/windows/aluminium-windows' ),
		array( 'label' => 'Sash windows', 'url' => '/windows/sash-windows' ),
		array( 'label' => 'Bay windows', 'url' => '/windows/bay-windows' ),
	);

	$doors = array(
		array( 'label' => 'Composite doors', 'url' => '/doors/composite-doors' ),
		array( 'label' => 'uPVC doors', 'url' => '/doors/upvc-doors' ),
		array( 'label' => 'Aluminium doors', 'url' => '/doors/aluminium-doors' ),
		array( 'label' => 'Bifold doors', 'url' => '/doors/bifold-doors' ),
		array( 'label' => 'Sliding patio doors', 'url' => '/doors/patio-doors' ),
		array( 'label' => 'French doors', 'url' => '/doors/french-doors' ),
	);

	switch ( $location ) {
		case 'primary':
			return array(
				array(
					'label'       => 'Windows',
					'url'         => '/windows',
					'description' => 'FRAME YOUR EVERYDAY',
					'overview'    => 'Explore all windows',
					'children'    => $windows,
				),
				array(
					'label'       => 'Doors',
					'url'         => '/doors',
					'description' => 'MAKE AN ENTRANCE',
					'overview'    => 'Explore all doors',
					'children'    => $doors,
				),
				array( 'label' => 'Conservatories', 'url' => '/conservatories' ),
				array( 'label' => 'Inspiration', 'url' => '/gallery' ),
				array( 'label' => 'Our story', 'url' => '/about' ),
			);

		case 'mobile':
			return array(
				array( 'label' => 'Windows', 'url' => '/windows', 'children' => $windows ),
				array( 'label' => 'Doors', 'url' => '/doors', 'children' => $doors ),
				array( 'label' => 'Conservatories', 'url' => '/conservatories' ),
				array( 'label' => 'Porches', 'url' => '/porches' ),
				array( 'label' => 'Inspiration', 'url' => '/gallery' ),
				array( 'label' => 'Our story', 'url' => '/about' ),
				array( 'label' => 'Areas we cover', 'url' => '/service-areas' ),
				array( 'label' => 'Advice', 'url' => '/blog' ),
				array( 'label' => 'Contact', 'url' => '/contact' ),
				array( 'label' => 'Start your quote', 'url' => '/free-quote' ),
			);

		case 'footer_windows':
			return $windows;

		case 'footer_doors':
			return array_merge(
				$doors,
				array(
					array( 'label' => 'Conservatories', 'url' => '/conservatories' ),
					array( 'label' => 'Porches', 'url' => '/porches' ),
				)
			);

		case 'footer_discover':
			return array(
				array( 'label' => 'Our story', 'url' => '/about' ),
				array( 'label' => 'Inspiration gallery', 'url' => '/gallery' ),
				array( 'label' => 'Customer reviews', 'url' => '/reviews' ),
				array( 'label' => 'Advice &amp; ideas', 'url' => '/blog' ),
				array( 'label' => 'Areas we cover', 'url' => '/service-areas' ),
				array( 'label' => 'Quote planner', 'url' => '/estimate-calculator' ),
				array( 'label' => 'Contact us', 'url' => '/contact' ),
			);

		case 'footer_legal':
			return array(
				array( 'label' => 'Privacy', 'url' => '/privacy-policy' ),
				array( 'label' => 'Cookies', 'url' => '/cookie-policy' ),
				array( 'label' => 'Terms', 'url' => '/terms-of-service' ),
			);
	}

	return array();
}

/**
 * Normalised menu items for a location: assigned menu first, approved fallback second.
 *
 * @param string $location Menu location.
 * @return array
 */
function splendid_nav_items( $location ) {
	$items    = array();
	$menu_id  = 0;
	$assigned = get_nav_menu_locations();

	if ( isset( $assigned[ $location ] ) ) {
		$menu_id = (int) $assigned[ $location ];
	}

	if ( $menu_id ) {
		$objects = wp_get_nav_menu_items( $menu_id );

		if ( $objects ) {
			_wp_menu_item_classes_by_context( $objects );

			$by_parent = array();
			foreach ( $objects as $object ) {
				$by_parent[ (int) $object->menu_item_parent ][] = $object;
			}

			$build = static function ( $parent_id ) use ( &$build, $by_parent ) {
				$out = array();

				if ( empty( $by_parent[ $parent_id ] ) ) {
					return $out;
				}

				foreach ( $by_parent[ $parent_id ] as $object ) {
					$out[] = array(
						'label'       => $object->title,
						'url'         => $object->url,
						'description' => $object->description,
						'overview'    => $object->attr_title,
						'current'     => ! empty( $object->current ) || ! empty( $object->current_item_ancestor ),
						'children'    => $build( (int) $object->ID ),
					);
				}

				return $out;
			};

			$items = $build( 0 );
		}
	}

	if ( ! $items ) {
		$items = splendid_default_nav( $location );
	}

	/**
	 * Filter the normalised navigation items for a location.
	 *
	 * @param array  $items    Items.
	 * @param string $location Menu location.
	 */
	return apply_filters( 'splendid_nav_items', $items, $location );
}

/**
 * Is this menu item the page currently being viewed?
 *
 * @param array $item Menu item.
 * @return bool
 */
function splendid_nav_is_current( $item ) {
	if ( ! empty( $item['current'] ) ) {
		return true;
	}

	$url = isset( $item['url'] ) ? $item['url'] : '';

	if ( '' === $url ) {
		return false;
	}

	$target  = untrailingslashit( wp_parse_url( splendid_url( $url ), PHP_URL_PATH ) );
	$current = untrailingslashit( wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) );

	return '' !== $target && $target === $current;
}

/**
 * Render one megamenu column for a parent item that has children.
 *
 * @param array $item Parent item.
 */
function splendid_render_megamenu( $item ) {
	$eyebrow  = isset( $item['description'] ) ? $item['description'] : '';
	$overview = ! empty( $item['overview'] ) ? $item['overview'] : sprintf( 'Explore all %s', strtolower( $item['label'] ) );
	?>
	<div class="megamenu" id="<?php echo esc_attr( 'megamenu-' . sanitize_title( $item['label'] ) ); ?>">
		<?php if ( $eyebrow ) : ?>
			<p><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>">
			<?php echo esc_html( $overview ); ?> <?php echo splendid_icon( 'arrow-up-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<?php foreach ( $item['children'] as $child ) : ?>
			<a href="<?php echo esc_url( splendid_url( $child['url'] ) ); ?>"><?php echo esc_html( $child['label'] ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Desktop navigation.
 */
function splendid_render_desktop_nav() {
	$items = splendid_nav_items( 'primary' );
	?>
	<nav class="desktop-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'splendid' ); ?>">
		<?php
		foreach ( $items as $item ) :
			$has_children = ! empty( $item['children'] );
			$current      = splendid_nav_is_current( $item ) ? ' aria-current="page"' : '';

			if ( ! $has_children ) :
				?>
				<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>"<?php echo $current; // phpcs:ignore ?>><?php echo esc_html( $item['label'] ); ?></a>
				<?php
				continue;
			endif;

			$menu_id = 'megamenu-' . sanitize_title( $item['label'] );
			?>
			<div class="navgroup" data-splendid-navgroup>
				<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>"<?php echo $current; // phpcs:ignore ?>><?php echo esc_html( $item['label'] ); ?></a>
				<button type="button"
					class="navgroup-toggle"
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $menu_id ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Show %s pages', 'splendid' ), $item['label'] ) ); ?>">
					<span aria-hidden="true">&#8964;</span>
				</button>
				<?php splendid_render_megamenu( $item ); ?>
			</div>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Mobile drawer.
 */
function splendid_render_mobile_nav() {
	$items = splendid_nav_items( 'mobile' );
	?>
	<div class="mobile-sheet" id="splendid-mobile-nav" hidden>
		<div class="mobile-sheet-head">
			<div>
				<h2 id="splendid-mobile-title"><?php esc_html_e( 'Explore Splendid', 'splendid' ); ?></h2>
				<p><?php esc_html_e( 'Windows, doors and a brighter home.', 'splendid' ); ?></p>
			</div>
			<button type="button" class="mobile-close" data-splendid-close aria-label="<?php esc_attr_e( 'Close navigation', 'splendid' ); ?>">
				<?php echo splendid_icon( 'x', 22 ); // phpcs:ignore ?>
			</button>
		</div>
		<?php foreach ( $items as $item ) : ?>
			<?php if ( empty( $item['children'] ) ) : ?>
				<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>">
					<?php echo esc_html( $item['label'] ); ?><?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
				</a>
			<?php else : ?>
				<?php $panel = 'mobile-' . sanitize_title( $item['label'] ); ?>
				<div class="mobile-group">
					<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>">
						<?php echo esc_html( $item['label'] ); ?><?php echo splendid_icon( 'arrow-up-right', 18 ); // phpcs:ignore ?>
					</a>
					<button type="button" class="mobile-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel ); ?>">
						<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Show %s pages', 'splendid' ), $item['label'] ) ); ?></span>
						<span aria-hidden="true">&#8964;</span>
					</button>
					<div class="mobile-children" id="<?php echo esc_attr( $panel ); ?>" hidden>
						<?php foreach ( $item['children'] as $child ) : ?>
							<a href="<?php echo esc_url( splendid_url( $child['url'] ) ); ?>"><?php echo esc_html( $child['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * One footer link column.
 *
 * @param string $location Menu location.
 * @param string $heading  Column heading.
 */
function splendid_render_footer_column( $location, $heading ) {
	$items = splendid_nav_items( $location );
	?>
	<div>
		<h3><?php echo esc_html( $heading ); ?></h3>
		<?php foreach ( $items as $item ) : ?>
			<a href="<?php echo esc_url( splendid_url( $item['url'] ) ); ?>"><?php echo wp_kses_post( $item['label'] ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php
}
