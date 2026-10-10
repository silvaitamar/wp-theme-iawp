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
add_action( 'after_setup_theme', function () {
	// Explícito mesmo em block themes — garante compatibilidade com SEO plugins.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	] );

	// Stylesheet global no editor.
	add_editor_style( 'style.css' );
	// Canvas-only: offset BHB overlay + não prender .hero-bg no chrome do core/html.
	add_editor_style( 'assets/editor-canvas.css' );

	// CSS das seções no editor — paridade visual com o frontend.
	// No editor não há render_block on-demand, então registramos todos
	// via add_editor_style para que o canvas renderize corretamente.
	// hub.css por último: .posts-hub vence blog.css nas classes compartilhadas.
	// author.css removido: classes author-section/* eram LP Digital Vanguard; hub usa about-*.
	// hero.css removido: leftover LP (.hero-section) — hub usa hub.css (.hero).
	$section_styles = [
		'assets/blocks/cta.css',
		'assets/blocks/blog.css',
		'assets/blocks/single.css',
		'assets/blocks/legal.css',
		'assets/blocks/error404.css',
		'assets/blocks/hub.css',
	];
	foreach ( $section_styles as $file ) {
		add_editor_style( $file );
	}
} );

/**
 * Block Header Behavior no iframe do editor:
 * CSS (posição overlay) + JS (toggle .is-scrolled → fundo glass).
 * O plugin agora também enfileira em enqueue_block_assets; manter aqui
 * como rede de segurança se a versão do plugin no site for antiga.
 */
add_action( 'enqueue_block_assets', function () {
	if ( ! wp_style_is( 'bhb-block-header-behavior', 'registered' ) ) {
		return;
	}
	wp_enqueue_style( 'bhb-block-header-behavior' );
	if ( is_admin() && wp_script_is( 'bhb-block-header-behavior', 'registered' ) ) {
		wp_enqueue_script( 'bhb-block-header-behavior' );
	}
} );
