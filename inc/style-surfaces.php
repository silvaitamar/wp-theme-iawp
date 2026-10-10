<?php
/**
 * Theme Version helper + editor stylesheet contract.
 *
 * Front enqueue stays conditional in assets.php (FSE race — do not replace with
 * wp_enqueue_block_style for page surfaces). This file keeps editor parity and
 * Version practice in one place.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme Version from style.css header (release signal / docs).
 *
 * Per-file cache bust on the front still uses filemtime in assets.php.
 *
 * @return string
 */
function iawp_theme_version(): string {
	$v = wp_get_theme()->get( 'Version' );
	return $v ? (string) $v : '0.1.0';
}

/**
 * Stylesheets registered via add_editor_style (canvas parity).
 *
 * Order matters: hub.css last so .posts-hub beats blog.css on shared classes.
 *
 * @return string[] Paths relative to the theme root.
 */
function iawp_editor_style_files(): array {
	return array(
		'style.css',
		'assets/editor-canvas.css',
		'assets/blocks/cta.css',
		'assets/blocks/blog.css',
		'assets/blocks/single.css',
		'assets/blocks/legal.css',
		'assets/blocks/error404.css',
		'assets/blocks/hub.css',
	);
}
