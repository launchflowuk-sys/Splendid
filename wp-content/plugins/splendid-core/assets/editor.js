/**
 * Editor controls for the Splendid blocks.
 *
 * Written without JSX so the plugin needs no build step: what ships in the ZIP
 * is exactly what runs, and a developer can edit it in place.
 */
( function ( blocks, element, components, blockEditor, serverSideRender, i18n ) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var __ = i18n.__;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;
	var SelectControl = components.SelectControl;
	var ToggleControl = components.ToggleControl;
	var RangeControl = components.RangeControl;
	var ServerSideRender = serverSideRender;

	/**
	 * Build a block whose preview is rendered by PHP and whose fields sit in the sidebar.
	 *
	 * @param {string}   name     Block name.
	 * @param {string}   title    Block title.
	 * @param {string}   icon     Dashicon slug.
	 * @param {string}   help     Sidebar description.
	 * @param {Function} controls Returns an array of control elements.
	 */
	function register( name, title, icon, help, controls ) {
		blocks.registerBlockType( name, {
			apiVersion: 3,
			title: title,
			icon: icon,
			category: 'splendid',
			edit: function ( props ) {
				return el(
					Fragment,
					{},
					el(
						InspectorControls,
						{},
						el(
							PanelBody,
							{ title: title, initialOpen: true },
							help ? el( 'p', { className: 'components-base-control__help' }, help ) : null,
							controls ? controls( props ) : null
						)
					),
					el( 'div', blockEditor.useBlockProps ? blockEditor.useBlockProps() : {},
						el( ServerSideRender, {
							block: name,
							attributes: props.attributes
						} )
					)
				);
			},
			save: function () {
				return null;
			}
		} );
	}

	function text( props, key, label, help ) {
		return el( TextControl, {
			label: label,
			help: help,
			value: props.attributes[ key ],
			onChange: function ( value ) {
				var next = {};
				next[ key ] = value;
				props.setAttributes( next );
			}
		} );
	}

	function textarea( props, key, label, help ) {
		return el( TextareaControl, {
			label: label,
			help: help,
			value: props.attributes[ key ],
			onChange: function ( value ) {
				var next = {};
				next[ key ] = value;
				props.setAttributes( next );
			}
		} );
	}

	blocks.registerBlockCollection && blocks.registerBlockCollection( 'splendid', { title: 'Splendid' } );

	register(
		'splendid/enquiry-form',
		__( 'Splendid enquiry form', 'splendid-core' ),
		'email',
		__( 'A real, server-backed enquiry. The recipient is set once in Settings → Splendid and is never taken from this page.', 'splendid-core' ),
		function ( props ) {
			return [
				el( SelectControl, {
					key: 'variant',
					label: __( 'Context', 'splendid-core' ),
					value: props.attributes.variant,
					options: [
						{ label: __( 'Contact', 'splendid-core' ), value: 'contact' },
						{ label: __( 'Free quote', 'splendid-core' ), value: 'quote' },
						{ label: __( 'Quote planner', 'splendid-core' ), value: 'planner' }
					],
					help: __( 'The planner adds the price note. No context calculates a price.', 'splendid-core' ),
					onChange: function ( value ) {
						props.setAttributes( { variant: value } );
					}
				} ),
				el( 'div', { key: 'eyebrow' }, text( props, 'eyebrow', __( 'Eyebrow', 'splendid-core' ) ) ),
				el( 'div', { key: 'heading' }, text( props, 'heading', __( 'Heading', 'splendid-core' ) ) ),
				el( 'div', { key: 'intro' }, textarea( props, 'intro', __( 'Introduction', 'splendid-core' ), __( 'Leave empty to use the approved wording.', 'splendid-core' ) ) )
			];
		}
	);

	register(
		'splendid/cta-band',
		__( 'Splendid CTA band', 'splendid-core' ),
		'megaphone',
		__( 'The closing call to action. The telephone number comes from Settings → Splendid.', 'splendid-core' ),
		function ( props ) {
			return [
				el( 'div', { key: 'eyebrow' }, text( props, 'eyebrow', __( 'Eyebrow', 'splendid-core' ) ) ),
				el( 'div', { key: 'heading' }, textarea( props, 'heading', __( 'Heading', 'splendid-core' ), __( 'Basic HTML is allowed: <br> and <em> for the italic emphasis.', 'splendid-core' ) ) ),
				el( 'div', { key: 'text' }, textarea( props, 'text', __( 'Supporting text', 'splendid-core' ) ) ),
				el( 'div', { key: 'ctaLabel' }, text( props, 'ctaLabel', __( 'Button label', 'splendid-core' ) ) ),
				el( 'div', { key: 'ctaUrl' }, text( props, 'ctaUrl', __( 'Button link', 'splendid-core' ) ) )
			];
		}
	);

	register(
		'splendid/contact-methods',
		__( 'Splendid contact methods', 'splendid-core' ),
		'phone',
		__( 'Phone, email and directions. Edit the values in Settings → Splendid so every page agrees.', 'splendid-core' ),
		function ( props ) {
			return [ el( 'div', { key: 'note' }, text( props, 'note', __( 'Note beneath', 'splendid-core' ) ) ) ];
		}
	);

	register(
		'splendid/gallery',
		__( 'Splendid inspiration grid', 'splendid-core' ),
		'format-gallery',
		__( 'Filterable image cards. Add only images the business owns and has permission to publish; label anything illustrative.', 'splendid-core' ),
		function ( props ) {
			return [
				el( 'div', { key: 'note' }, textarea( props, 'note', __( 'Image note', 'splendid-core' ), __( 'Shown beneath the grid. Keep the illustrative-image note while the images are design illustrations.', 'splendid-core' ) ) ),
				el( TextControl, {
					key: 'filters',
					label: __( 'Filters', 'splendid-core' ),
					help: __( 'Comma separated. The first is the default.', 'splendid-core' ),
					value: ( props.attributes.filters || [] ).join( ', ' ),
					onChange: function ( value ) {
						props.setAttributes( {
							filters: value.split( ',' ).map( function ( item ) {
								return item.trim();
							} ).filter( Boolean )
						} );
					}
				} ),
				el( TextareaControl, {
					key: 'items',
					label: __( 'Images', 'splendid-core' ),
					help: __( 'One per line: Category | Title | Image URL | Alt text | Link', 'splendid-core' ),
					value: ( props.attributes.items || [] ).map( function ( item ) {
						return [ item.type, item.title, item.image, item.alt, item.href ].join( ' | ' );
					} ).join( '\n' ),
					onChange: function ( value ) {
						props.setAttributes( {
							items: value.split( '\n' ).map( function ( line ) {
								var parts = line.split( '|' ).map( function ( part ) {
									return part.trim();
								} );

								return {
									type: parts[ 0 ] || '',
									title: parts[ 1 ] || '',
									image: parts[ 2 ] || '',
									alt: parts[ 3 ] || '',
									href: parts[ 4 ] || '/'
								};
							} ).filter( function ( item ) {
								return item.title || item.image;
							} )
						} );
					}
				} )
			];
		}
	);

	register(
		'splendid/product-grid',
		__( 'Splendid product grid', 'splendid-core' ),
		'grid-view',
		__( 'Lists the real child pages of a hub, so adding a product page adds a card.', 'splendid-core' ),
		function ( props ) {
			return [
				el( 'div', { key: 'parent' }, text( props, 'parent', __( 'Hub page slug', 'splendid-core' ), __( 'For example: windows or doors.', 'splendid-core' ) ) ),
				el( ToggleControl, {
					key: 'tinted',
					label: __( 'White cards (for tinted sections)', 'splendid-core' ),
					checked: !! props.attributes.tinted,
					onChange: function ( value ) {
						props.setAttributes( { tinted: value } );
					}
				} )
			];
		}
	);

	register(
		'splendid/area-links',
		__( 'Splendid area links', 'splendid-core' ),
		'location',
		__( 'Lists published local area pages only. Draft pages stay off the site until coverage is confirmed.', 'splendid-core' ),
		function ( props ) {
			return [
				el( RangeControl, {
					key: 'limit',
					label: __( 'How many', 'splendid-core' ),
					value: props.attributes.limit,
					min: 1,
					max: 20,
					onChange: function ( value ) {
						props.setAttributes( { limit: value } );
					}
				} )
			];
		}
	);

	register(
		'splendid/article-grid',
		__( 'Splendid article grid', 'splendid-core' ),
		'welcome-write-blog',
		__( 'Lists published advice articles.', 'splendid-core' ),
		function ( props ) {
			return [
				el( 'div', { key: 'parent' }, text( props, 'parent', __( 'Hub page slug', 'splendid-core' ) ) ),
				el( RangeControl, {
					key: 'limit',
					label: __( 'How many', 'splendid-core' ),
					value: props.attributes.limit,
					min: 1,
					max: 12,
					onChange: function ( value ) {
						props.setAttributes( { limit: value } );
					}
				} )
			];
		}
	);

	register(
		'splendid/link',
		__( 'Splendid link', 'splendid-core' ),
		'admin-links',
		__( 'A link or button with the design\'s trailing icon.', 'splendid-core' ),
		function ( props ) {
			return [
				el( 'div', { key: 'label' }, text( props, 'label', __( 'Label', 'splendid-core' ) ) ),
				el( 'div', { key: 'url' }, text( props, 'url', __( 'Link', 'splendid-core' ) ) ),
				el( SelectControl, {
					key: 'style',
					label: __( 'Style', 'splendid-core' ),
					value: props.attributes.className,
					options: [
						{ label: __( 'Text link', 'splendid-core' ), value: 'text-link' },
						{ label: __( 'Green button', 'splendid-core' ), value: 'button brand' },
						{ label: __( 'Deep green button', 'splendid-core' ), value: 'button forest' },
						{ label: __( 'Outlined button', 'splendid-core' ), value: 'button outlined' },
						{ label: __( 'White button', 'splendid-core' ), value: 'button white' }
					],
					onChange: function ( value ) {
						props.setAttributes( { className: value } );
					}
				} ),
				el( SelectControl, {
					key: 'icon',
					label: __( 'Icon', 'splendid-core' ),
					value: props.attributes.icon,
					options: [
						{ label: __( 'Arrow up right', 'splendid-core' ), value: 'arrow-up-right' },
						{ label: __( 'Arrow right', 'splendid-core' ), value: 'arrow-right' },
						{ label: __( 'Arrow down', 'splendid-core' ), value: 'arrow-down' },
						{ label: __( 'None', 'splendid-core' ), value: '' }
					],
					onChange: function ( value ) {
						props.setAttributes( { icon: value } );
					}
				} )
			];
		}
	);

	function mediaControl( props, label ) {
		return el( blockEditor.MediaUploadCheck, { key: 'media' },
			el( blockEditor.MediaUpload, {
				allowedTypes: [ 'image' ],
				value: props.attributes.id,
				onSelect: function ( media ) {
					props.setAttributes( {
						id: media.id,
						src: media.url,
						alt: media.alt || props.attributes.alt
					} );
				},
				render: function ( open ) {
					return el( components.Button, {
						variant: 'secondary',
						onClick: open.open
					}, label );
				}
			} )
		);
	}

	register(
		'splendid/image',
		__( 'Splendid image', 'splendid-core' ),
		'format-image',
		__( 'A plain image, without the figure wrapper the design\'s layout cannot use.', 'splendid-core' ),
		function ( props ) {
			return [
				mediaControl( props, __( 'Choose image', 'splendid-core' ) ),
				el( 'div', { key: 'alt' }, textarea( props, 'alt', __( 'Alt text', 'splendid-core' ), __( 'Describe the image for someone who cannot see it.', 'splendid-core' ) ) ),
				el( ToggleControl, {
					key: 'priority',
					label: __( 'Load immediately (hero image)', 'splendid-core' ),
					checked: !! props.attributes.priority,
					onChange: function ( value ) {
						props.setAttributes( { priority: value } );
					}
				} )
			];
		}
	);

	register(
		'splendid/image-card',
		__( 'Splendid image card', 'splendid-core' ),
		'images-alt2',
		__( 'A photographic card linking to another page.', 'splendid-core' ),
		function ( props ) {
			return [
				mediaControl( props, __( 'Choose image', 'splendid-core' ) ),
				el( 'div', { key: 'title' }, text( props, 'title', __( 'Title', 'splendid-core' ) ) ),
				el( 'div', { key: 'tag' }, text( props, 'tag', __( 'Caption', 'splendid-core' ) ) ),
				el( 'div', { key: 'href' }, text( props, 'href', __( 'Link', 'splendid-core' ) ) ),
				el( 'div', { key: 'number' }, text( props, 'number', __( 'Number', 'splendid-core' ) ) ),
				el( 'div', { key: 'alt' }, textarea( props, 'alt', __( 'Alt text', 'splendid-core' ) ) )
			];
		}
	);

	register(
		'splendid/benefits',
		__( 'Splendid benefit strip', 'splendid-core' ),
		'awards',
		__( 'The four-item strip beneath the hero.', 'splendid-core' ),
		function ( props ) {
			return [
				el( TextareaControl, {
					key: 'items',
					label: __( 'Items', 'splendid-core' ),
					help: __( 'One per line: icon | label. Icons: ruler, sun, layers, map-pin, check, phone, mail.', 'splendid-core' ),
					value: ( props.attributes.items || [] ).map( function ( item ) {
						return item.icon + ' | ' + item.label;
					} ).join( '\n' ),
					onChange: function ( value ) {
						props.setAttributes( {
							items: value.split( '\n' ).map( function ( line ) {
								var parts = line.split( '|' ).map( function ( part ) {
									return part.trim();
								} );

								return { icon: parts[ 0 ] || 'check', label: parts[ 1 ] || '' };
							} ).filter( function ( item ) {
								return item.label;
							} )
						} );
					}
				} )
			];
		}
	);
}(
	window.wp.blocks,
	window.wp.element,
	window.wp.components,
	window.wp.blockEditor,
	window.wp.serverSideRender,
	window.wp.i18n
) );
