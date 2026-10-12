<?php
/**
 * Theme supports, editor styles, BHB editor assets.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup: declare supported features.
 */
add_action(
	'after_setup_theme',
	function () {
		// Explícito mesmo em block themes — garante compatibilidade com SEO plugins.
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Canvas parity: lista canónica em iawp_editor_style_files() (inc/style-surfaces.php).
		// Front continua condicional em assets.php — não usar wp_enqueue_block_style para superfícies.
		foreach ( iawp_editor_style_files() as $file ) {
			add_editor_style( $file );
		}
	}
);

/**
 * Block Header Behavior no iframe do editor:
 * CSS (posição overlay) + JS (toggle .is-scrolled → fundo glass).
 * O plugin agora também enfileira em enqueue_block_assets; manter aqui
 * como rede de segurança se a versão do plugin no site for antiga.
 */
add_action(
	'enqueue_block_assets',
	function () {
		if ( ! wp_style_is( 'bhb-block-header-behavior', 'registered' ) ) {
			return;
		}
		wp_enqueue_style( 'bhb-block-header-behavior' );
		if ( is_admin() && wp_script_is( 'bhb-block-header-behavior', 'registered' ) ) {
			wp_enqueue_script( 'bhb-block-header-behavior' );
		}
	}
);
