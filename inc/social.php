<?php
/**
 * Core/social-link custom services + editor variations.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
 * EXTENSÃO DO BLOCO core/social-link COM SERVIÇOS PERSONALIZADOS
 * ============================================================
 *
 * Padrão reutilizável: adicionar qualquer rede social não nativa
 * ao bloco core/social-links sem plugin.
 *
 * Dois registros obrigatórios:
 * 1. PHP (filtro): injetar SVG no array de serviços do servidor
 *    — resolve o ícone no frontend e em e-mails/RSS
 * 2. JS (assets/social-variations.js): re-registrar variation.icon
 *    — o canvas do editor usa activeVariation.icon (não o SVG PHP)
 *
 * Para adicionar novos serviços: duplicar a entrada no mapa PHP
 * e no array JS (mesmo slug + arquivo em assets/icons/).
 * ============================================================
 */

/**
 * 1. PHP — registrar SVGs no servidor.
 * Filtro disponível desde WP 6.9 (block_core_social_link_get_services).
 * Em versões anteriores usava-se render_block para substituir o HTML.
 */
add_filter(
	'block_core_social_link_get_services',
	function ( array $services ): array {

		$theme_icons = get_stylesheet_directory() . '/assets/icons/';

		// Lucide stroke — fidelidade ao hub. Sobrescreve logos brand do core.
		// WordPress → blocks (Gutenberg); site → earth; buymeacoffee → coffee.
		$map = array(
			'linkedin'     => array(
				'name' => __( 'LinkedIn', 'iawp' ),
				'file' => 'linkedin.svg',
			),
			'wordpress'    => array(
				'name' => __( 'WordPress.org', 'iawp' ),
				'file' => 'blocks.svg',
			),
			'github'       => array(
				'name' => __( 'GitHub', 'iawp' ),
				'file' => 'github.svg',
			),
			'workana'      => array(
				'name' => __( 'Workana', 'iawp' ),
				'file' => 'users.svg',
			),
			'buymeacoffee' => array(
				'name' => __( 'Buy me a coffee', 'iawp' ),
				'file' => 'coffee.svg',
			),
			'site'         => array(
				'name' => __( 'Site', 'iawp' ),
				'file' => 'earth.svg',
			),
		);
		foreach ( $map as $slug => $meta ) {
			$file = $theme_icons . $meta['file'];
			if ( ! is_readable( $file ) ) {
				continue;
			}
			$services[ $slug ] = array(
				'name' => $meta['name'],
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme SVG.
				'icon' => (string) file_get_contents( $file ),
			);
		}

		return $services;
	}
);

/**
 * 2. JS — registrar variação no editor Gutenberg.
 *
 * Arquivo JS separado com dependências explícitas em wp-blocks e wp-element.
 * Os SVGs são passados via wp_localize_script para evitar duplicar strings.
 */
add_action(
	'enqueue_block_editor_assets',
	function (): void {

		$theme_icons = get_stylesheet_directory() . '/assets/icons/';
		$theme_uri   = get_stylesheet_directory_uri();

		// deps: block-library must register variations before we replace icons;
		// domReady + primitives so canvas <Icon> gets Lucide SVG components.
		wp_enqueue_script(
			'iawp-social-variations',
			$theme_uri . '/assets/social-variations.js',
			array( 'wp-blocks', 'wp-element', 'wp-dom-ready', 'wp-hooks', 'wp-primitives', 'wp-block-library' ),
			filemtime( get_stylesheet_directory() . '/assets/social-variations.js' ),
			true
		);

		// #grad-brand no iframe do canvas — sem isso stroke:url(#grad-brand) some no editor.
		wp_enqueue_script(
			'iawp-editor-grad-brand',
			$theme_uri . '/assets/editor-grad-brand.js',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/editor-grad-brand.js' ),
			true
		);

		// Lucide SVGs → editor (mesmo mapa do filtro PHP).
		$icons_data = array();
		$map        = array(
			'linkedin'     => 'linkedin.svg',
			'wordpress'    => 'blocks.svg',
			'github'       => 'github.svg',
			'workana'      => 'users.svg',
			'buymeacoffee' => 'coffee.svg',
			'site'         => 'earth.svg',
		);
		foreach ( $map as $slug => $file_name ) {
			$file = $theme_icons . $file_name;
			if ( is_readable( $file ) ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme SVG.
				$icons_data[ $slug ] = trim( (string) file_get_contents( $file ) );
			}
		}

		wp_localize_script( 'iawp-social-variations', 'iawpSocialIcons', $icons_data );
	}
);
