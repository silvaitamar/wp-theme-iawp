<?php
/**
 * Llms.txt, head cleanup, blog title fallback, category redirects.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Title do blog archive — fallback quando TSF não está ativo.
 *
 * O WP core usa "Blog" como título de page_for_posts. O The SEO Framework
 * sobrescreve via filtro de alta prioridade — este só atua como fallback.
 *
 * Canonical para is_home(): responsabilidade do TSF. O WP core também gera
 * canonical via wp_head priority 10 para page_for_posts — não duplicar aqui.
 */
add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( ! is_home() ) {
			return $title;
		}
		if ( class_exists( '\The_SEO_Framework\Load' ) ) {
			return $title;
		}
		return 'Blog sobre IA e WordPress &#8211; ' . get_bloginfo( 'name' );
	}
);

/**
 * Remove ruído do <head> — meta generator, REST API link, RSD, oEmbed, shortlink.
 *
 * Esses links não têm valor de SEO, revelam stack tecnológico e aumentam
 * o payload do <head>. Parsers de IA também se beneficiam de um <head> limpo.
 * Quando um plugin SEO estiver ativo, ele gerencia esses sinais diretamente.
 */
/**
 * Serve llms.txt na raiz do domínio.
 * Protocolo emergente (llmstxt.org) — sinaliza para LLMs o que pode ser indexado.
 */
add_action(
	'init',
	function () {
		add_rewrite_rule( '^llms\.txt$', 'index.php?dv_llms=1', 'top' );
	}
);
add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'dv_llms';
		return $vars;
	}
);
add_action(
	'template_redirect',
	function () {
		if ( ! get_query_var( 'dv_llms' ) ) {
			return;
		}
		$file = get_template_directory() . '/llms.txt';
		if ( ! file_exists( $file ) ) {
			status_header( 404 );
			exit; }
		header( 'Content-Type: text/plain; charset=UTF-8' );
		header( 'Cache-Control: public, max-age=86400' );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Local theme llms.txt.
		readfile( $file );
		exit;
	}
);

/**
 * 301 /category/… → /categoria/… when category_base is Portuguese.
 * Keeps old indexes and external links from 404ing after the base change.
 */
add_action(
	'template_redirect',
	function (): void {
		if ( 'categoria' !== (string) get_option( 'category_base' ) ) {
			return;
		}
		$uri  = isset( $_SERVER['REQUEST_URI'] )
			? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )
			: '';
		$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
		if ( ! preg_match( '#^/category(/.*)?$#', $path, $m ) ) {
			return;
		}
		$tail   = isset( $m[1] ) ? $m[1] : '/';
		$query  = (string) wp_parse_url( $uri, PHP_URL_QUERY );
		$target = home_url( '/categoria' . $tail );
		if ( '' !== $query ) {
			$target .= '?' . $query;
		}
		wp_safe_redirect( $target, 301 );
		exit;
	},
	0
);

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
