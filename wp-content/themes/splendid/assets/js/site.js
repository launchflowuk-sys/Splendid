/**
 * Splendid front-end behaviour.
 *
 * Everything here is an enhancement. With JavaScript disabled the content is
 * still visible, the menus are reachable (the drawer falls back to the footer
 * links and the desktop submenus open on hover/focus), the FAQ accordions work
 * natively as <details>, and the gallery shows every card.
 */
( function () {
	'use strict';

	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ---------------------------------------------------------------
	 * Reveal each section once as it enters the viewport.
	 * --------------------------------------------------------------- */
	function initReveal() {
		var targets = document.querySelectorAll( '.reveal' );

		if ( ! targets.length ) {
			return;
		}

		if ( reduced || ! ( 'IntersectionObserver' in window ) ) {
			Array.prototype.forEach.call( targets, function ( el ) {
				el.classList.add( 'visible' );
			} );
			return;
		}

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'visible' );
					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.08 } );

		Array.prototype.forEach.call( targets, function ( el ) {
			observer.observe( el );
		} );
	}

	/* ---------------------------------------------------------------
	 * Desktop submenus: hover still works, and now so do touch and keyboard.
	 * --------------------------------------------------------------- */
	function initSubmenus() {
		var groups = document.querySelectorAll( '[data-splendid-navgroup]' );

		Array.prototype.forEach.call( groups, function ( group ) {
			var toggle = group.querySelector( '.navgroup-toggle' );

			if ( ! toggle ) {
				return;
			}

			function close() {
				group.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}

			function open() {
				closeAll();
				group.classList.add( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'true' );
			}

			function closeAll() {
				Array.prototype.forEach.call( groups, function ( other ) {
					other.classList.remove( 'is-open' );
					var otherToggle = other.querySelector( '.navgroup-toggle' );
					if ( otherToggle ) {
						otherToggle.setAttribute( 'aria-expanded', 'false' );
					}
				} );
			}

			toggle.addEventListener( 'click', function () {
				if ( 'true' === toggle.getAttribute( 'aria-expanded' ) ) {
					close();
				} else {
					open();
				}
			} );

			group.addEventListener( 'keydown', function ( event ) {
				if ( 'Escape' === event.key ) {
					close();
					toggle.focus();
				}
			} );
		} );

		document.addEventListener( 'click', function ( event ) {
			Array.prototype.forEach.call( groups, function ( group ) {
				if ( ! group.contains( event.target ) ) {
					group.classList.remove( 'is-open' );
					var toggle = group.querySelector( '.navgroup-toggle' );
					if ( toggle ) {
						toggle.setAttribute( 'aria-expanded', 'false' );
					}
				}
			} );
		} );
	}

	/* ---------------------------------------------------------------
	 * Mobile drawer: focus trap, Escape, restore focus, close after a link.
	 * --------------------------------------------------------------- */
	function initDrawer() {
		var openButton = document.querySelector( '[data-splendid-open]' );
		var drawer = document.getElementById( 'splendid-mobile-nav' );
		var overlay = document.querySelector( '.mobile-overlay' );

		if ( ! openButton || ! drawer ) {
			return;
		}

		var lastFocus = null;

		function focusable() {
			return drawer.querySelectorAll( 'a[href], button:not([disabled])' );
		}

		function open() {
			lastFocus = document.activeElement;
			drawer.hidden = false;
			if ( overlay ) {
				overlay.hidden = false;
			}
			document.body.classList.add( 'splendid-drawer-open' );
			openButton.setAttribute( 'aria-expanded', 'true' );

			var first = drawer.querySelector( '[data-splendid-close]' ) || focusable()[ 0 ];
			if ( first ) {
				first.focus();
			}
		}

		function close() {
			drawer.hidden = true;
			if ( overlay ) {
				overlay.hidden = true;
			}
			document.body.classList.remove( 'splendid-drawer-open' );
			openButton.setAttribute( 'aria-expanded', 'false' );

			if ( lastFocus && lastFocus.focus ) {
				lastFocus.focus();
			}
		}

		openButton.addEventListener( 'click', open );

		Array.prototype.forEach.call( document.querySelectorAll( '[data-splendid-close]' ), function ( el ) {
			el.addEventListener( 'click', close );
		} );

		drawer.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a[href]' ) ) {
				close();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( drawer.hidden ) {
				return;
			}

			if ( 'Escape' === event.key ) {
				close();
				return;
			}

			if ( 'Tab' !== event.key ) {
				return;
			}

			var items = Array.prototype.filter.call( focusable(), function ( el ) {
				return null !== el.offsetParent;
			} );

			if ( ! items.length ) {
				return;
			}

			var first = items[ 0 ];
			var last = items[ items.length - 1 ];

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		} );

		Array.prototype.forEach.call( drawer.querySelectorAll( '.mobile-toggle' ), function ( toggle ) {
			var panel = document.getElementById( toggle.getAttribute( 'aria-controls' ) );

			if ( ! panel ) {
				return;
			}

			toggle.addEventListener( 'click', function () {
				var isOpen = 'true' === toggle.getAttribute( 'aria-expanded' );
				toggle.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
				panel.hidden = isOpen;
			} );
		} );
	}

	/* ---------------------------------------------------------------
	 * Inspiration filter. Cards are hidden by class, never removed.
	 * --------------------------------------------------------------- */
	function initGalleryFilter() {
		var rows = document.querySelectorAll( '.filter-row' );

		Array.prototype.forEach.call( rows, function ( row ) {
			var section = row.closest( '.gallery-section' ) || document;
			var cards = section.querySelectorAll( '[data-gallery-type]' );
			var empty = section.querySelector( '.gallery-empty' );

			row.addEventListener( 'click', function ( event ) {
				var button = event.target.closest( 'button[data-filter]' );

				if ( ! button ) {
					return;
				}

				var wanted = button.getAttribute( 'data-filter' );
				var shown = 0;

				Array.prototype.forEach.call( row.querySelectorAll( 'button[data-filter]' ), function ( other ) {
					var active = other === button;
					other.classList.toggle( 'active', active );
					other.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
				} );

				Array.prototype.forEach.call( cards, function ( card ) {
					var match = 'All' === wanted || card.getAttribute( 'data-gallery-type' ) === wanted;
					card.classList.toggle( 'is-filtered-out', ! match );
					if ( match ) {
						shown++;
					}
				} );

				if ( empty ) {
					empty.hidden = shown > 0;
				}
			} );
		} );
	}

	function ready( fn ) {
		if ( 'loading' !== document.readyState ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	ready( function () {
		initReveal();
		initSubmenus();
		initDrawer();
		initGalleryFilter();
	} );
}() );
