/**
 * Bluecadet Events — deactivate confirmation modal.
 *
 * On the Plugins screen, intercepts THIS plugin's Deactivate link and asks the
 * user whether to delete plugin data on a future uninstall (behavior A: records
 * the preference only). Deactivation always proceeds afterward, even if the
 * preference save fails — we never block deactivation.
 */
( function () {
	var cfg = window.bcEventsDeactivate;
	if ( ! cfg || ! cfg.pluginBasename ) {
		return;
	}

	var row = document.querySelector( 'tr[data-plugin="' + cfg.pluginBasename + '"]' );
	if ( ! row ) {
		return;
	}

	var link = row.querySelector( 'a[href*="action=deactivate"]' );
	var modal = document.getElementById( 'bc-events-deactivate-modal' );
	if ( ! link || ! modal ) {
		return;
	}

	var targetUrl = null;

	link.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		targetUrl = link.getAttribute( 'href' );
		modal.hidden = false;
	} );

	function proceed() {
		if ( targetUrl ) {
			window.location.assign( targetUrl );
		}
	}

	function choose( deleteData ) {
		var body = new URLSearchParams();
		body.append( 'action', cfg.action );
		body.append( 'nonce', cfg.nonce );
		body.append( 'delete', deleteData ? '1' : '0' );

		fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} )
			.catch( function () {} )
			.then( proceed );
	}

	modal.addEventListener( 'click', function ( e ) {
		var choice = e.target && e.target.getAttribute( 'data-bc-choice' );
		if ( ! choice ) {
			return;
		}
		if ( 'delete' === choice ) {
			choose( true );
		} else if ( 'keep' === choice ) {
			choose( false );
		} else {
			modal.hidden = true;
			targetUrl = null;
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && ! modal.hidden ) {
			modal.hidden = true;
			targetUrl = null;
		}
	} );
} )();
