/**
 * Enquiry form enhancement.
 *
 * Without this file the form still posts to admin-post.php and re-renders with
 * server-side errors. With it, the same server validation runs over REST and the
 * page updates in place.
 */
( function () {
	'use strict';

	var config = window.splendidEnquiry || {};
	var strings = config.strings || {};

	function fieldFor( form, key ) {
		return form.querySelector( '[name="' + key + '"]' );
	}

	function clearErrors( form ) {
		Array.prototype.forEach.call( form.querySelectorAll( '.field.has-error' ), function ( field ) {
			field.classList.remove( 'has-error' );
		} );
		Array.prototype.forEach.call( form.querySelectorAll( '.field-error' ), function ( error ) {
			error.remove();
		} );
		Array.prototype.forEach.call( form.querySelectorAll( '[aria-invalid="true"]' ), function ( input ) {
			input.removeAttribute( 'aria-invalid' );
			input.removeAttribute( 'aria-describedby' );
		} );
	}

	function showErrors( form, message, errors ) {
		var summary = form.querySelector( '[data-summary]' );
		var list = '';

		clearErrors( form );

		Object.keys( errors || {} ).forEach( function ( key ) {
			var input = fieldFor( form, key );

			if ( ! input ) {
				return;
			}

			var wrapper = input.closest( '.field' );
			var errorId = 'enquiry-' + key + '-error';

			if ( wrapper ) {
				wrapper.classList.add( 'has-error' );

				var note = document.createElement( 'span' );
				note.className = 'field-error';
				note.id = errorId;
				note.textContent = errors[ key ];
				wrapper.appendChild( note );
			}

			input.setAttribute( 'aria-invalid', 'true' );
			input.setAttribute( 'aria-describedby', errorId );

			list += '<li><a href="#enquiry-' + key + '">' + errors[ key ] + '</a></li>';
		} );

		if ( ! summary ) {
			return;
		}

		summary.innerHTML = message + ( list ? '<ul>' + list + '</ul>' : '' );
		summary.hidden = false;
		summary.focus();
	}

	function showReceived( form ) {
		var panel = form.closest( '.quote-panel' );

		if ( ! panel || ! config.received ) {
			form.submit();
			return;
		}

		panel.innerHTML = config.received;

		var received = panel.querySelector( '.enquiry-received' );

		if ( received ) {
			received.focus();
			received.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		}
	}

	function setSending( form, sending ) {
		var button = form.querySelector( '.submit' );
		var text = form.querySelector( '[data-submit-text]' );

		form.setAttribute( 'data-state', sending ? 'sending' : '' );

		if ( button ) {
			button.disabled = sending;
		}

		if ( text ) {
			text.textContent = sending
				? button.getAttribute( 'data-sending-label' )
				: button.getAttribute( 'data-submit-label' );
		}
	}

	/**
	 * A nonce that has not been through any cache. Falls back to the one in
	 * the page if the refresh itself fails.
	 */
	function freshNonce() {
		if ( ! config.nonceUrl ) {
			return Promise.resolve( config.nonce || '' );
		}

		var url = config.nonceUrl + ( config.nonceUrl.indexOf( '?' ) === -1 ? '?' : '&' ) + '_=' + Date.now();

		return fetch( url, { credentials: 'omit', cache: 'no-store' } )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( body ) {
				if ( body && body.nonce ) {
					config.nonce = body.nonce;
				}

				return config.nonce || '';
			} )
			.catch( function () {
				return config.nonce || '';
			} );
	}

	function submit( form ) {
		var endpoint = form.getAttribute( 'data-rest' );

		if ( ! endpoint || ! window.fetch ) {
			return false;
		}

		var data = new FormData( form );

		// The form carries the no-JavaScript fallback's own nonce as _wpnonce.
		// WordPress's REST API reads a _wpnonce field BEFORE the X-WP-Nonce
		// header, checks it as a REST nonce, and rejects every submission with
		// "Cookie check failed". The REST request is verified by the header.
		data.delete( '_wpnonce' );
		data.delete( '_wp_http_referer' );

		setSending( form, true );

		function post( nonce ) {
			return fetch( endpoint, {
				method: 'POST',
				// No cookies either way: this is a logged-out form. A signed-in
				// editor's cookie would make WordPress reject the logged-out nonce.
				credentials: 'omit',
				headers: { 'X-WP-Nonce': nonce },
				body: data
			} ).then( function ( response ) {
				return response.json().then( function ( body ) {
					return { status: response.status, body: body };
				} );
			} );
		}

		// A page cache keeps the nonce printed into the page far longer than
		// WordPress keeps it valid, so fetch a fresh one for every submission.
		// If WordPress still rejects it, fetch again and retry once -- the
		// rejected request never reached the enquiry handler, so nothing doubles.
		freshNonce()
			.then( post )
			.then( function ( result ) {
				if ( result.body && 'rest_cookie_invalid_nonce' === result.body.code ) {
					return freshNonce().then( post );
				}

				return result;
			} )
			.then( function ( result ) {
				setSending( form, false );

				if ( result.body && result.body.ok ) {
					showReceived( form );
					return;
				}

				showErrors(
					form,
					( result.body && result.body.message ) || strings.failure,
					( result.body && result.body.errors ) || {}
				);
			} )
			.catch( function () {
				setSending( form, false );
				showErrors( form, navigator.onLine === false ? strings.offline : strings.failure, {} );
			} );

		return true;
	}

	function initQuantity( form ) {
		var input = form.querySelector( '.splendid-quantity-input' );

		if ( ! input ) {
			return;
		}

		Array.prototype.forEach.call( form.querySelectorAll( '[data-quantity]' ), function ( button ) {
			button.addEventListener( 'click', function () {
				var step = parseInt( button.getAttribute( 'data-quantity' ), 10 );
				var next = ( parseInt( input.value, 10 ) || 1 ) + step;

				input.value = Math.min( 100, Math.max( 1, next ) );
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
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
		var forms = document.querySelectorAll( '[data-splendid-form]' );

		if ( ! forms.length ) {
			return;
		}

		Array.prototype.forEach.call( forms, function ( form ) {
			initQuantity( form );

			form.addEventListener( 'submit', function ( event ) {
				if ( submit( form ) ) {
					event.preventDefault();
				}
			} );
		} );
	} );
}() );
