/**
 * Injeta #grad-brand no documento do canvas do editor (post + site editor).
 * Front já emite via wp_body_open; sem defs, stroke:url(#grad-brand) some.
 */
( function () {
	'use strict';

	var MARK = 'iawp-grad-brand';
	var SVG =
		'<svg id="' +
		MARK +
		'" aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">' +
		'<defs><linearGradient id="grad-brand" x1="0" y1="0" x2="1" y2="1">' +
		'<stop offset="0%" stop-color="#3B82F6"/>' +
		'<stop offset="100%" stop-color="#8B5CF6"/>' +
		'</linearGradient></defs></svg>';

	function inject( doc ) {
		if ( ! doc || ! doc.body ) {
			return;
		}
		if ( doc.getElementById( MARK ) || doc.getElementById( 'grad-brand' ) ) {
			return;
		}
		doc.body.insertAdjacentHTML( 'afterbegin', SVG );
	}

	function canvasDocs() {
		var docs = [];
		var nodes = document.querySelectorAll(
			'iframe[name="editor-canvas"], iframe.edit-site-visual-editor__editor-canvas, iframe.editor-canvas__iframe'
		);
		nodes.forEach( function ( frame ) {
			try {
				if ( frame.contentDocument ) {
					docs.push( frame.contentDocument );
				}
			} catch ( e ) {
				/* cross-origin — ignore */
			}
		} );
		return docs;
	}

	function sync() {
		canvasDocs().forEach( inject );
	}

	function watchFrame( frame ) {
		frame.addEventListener( 'load', sync );
		try {
			if ( frame.contentDocument && frame.contentDocument.readyState === 'complete' ) {
				inject( frame.contentDocument );
			}
		} catch ( e ) {
			/* ignore */
		}
	}

	function boot() {
		sync();
		document
			.querySelectorAll(
				'iframe[name="editor-canvas"], iframe.edit-site-visual-editor__editor-canvas, iframe.editor-canvas__iframe'
			)
			.forEach( watchFrame );

		var root = document.querySelector( '.interface-interface-skeleton__content, .edit-post-layout, .edit-site-layout' );
		if ( root && typeof MutationObserver !== 'undefined' ) {
			var obs = new MutationObserver( function () {
				sync();
				document
					.querySelectorAll(
						'iframe[name="editor-canvas"], iframe.edit-site-visual-editor__editor-canvas, iframe.editor-canvas__iframe'
					)
					.forEach( watchFrame );
			} );
			obs.observe( root, { childList: true, subtree: true } );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
	window.addEventListener( 'load', sync );
} )();
