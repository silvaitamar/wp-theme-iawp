<?php
/**
 * Playground seed — layout clone of the IA+WP hub (public editorial content).
 *
 * Run via blueprint: wp eval-file wp-content/themes/iawp/playground/setup-demo.php
 *
 * @package iawp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Create or update a page by slug.
 *
 * @param string $title    Title.
 * @param string $slug     Slug.
 * @param string $content  Block markup.
 * @param string $template Page template slug.
 * @return int Page ID.
 */
function iawp_pg_upsert_page( $title, $slug, $content, $template = 'page-no-title' ) {
	$existing = get_page_by_path( $slug );
	$args     = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => $content,
	);
	if ( $existing ) {
		$args['ID'] = (int) $existing->ID;
		$id         = wp_update_post( $args, true );
	} else {
		$id = wp_insert_post( $args, true );
	}
	if ( is_wp_error( $id ) ) {
		echo 'FAIL page ' . $slug . ': ' . $id->get_error_message() . "\n";
		exit( 1 );
	}
	update_post_meta( (int) $id, '_wp_page_template', $template );
	return (int) $id;
}

/**
 * Sideload a remote image and set as featured.
 *
 * @param int    $post_id Post ID.
 * @param string $url     Public image URL.
 * @return void
 */
function iawp_pg_set_featured_from_url( $post_id, $url ) {
	if ( ! $url || has_post_thumbnail( $post_id ) ) {
		return;
	}
	$att_id = media_sideload_image( $url, $post_id, null, 'id' );
	if ( is_wp_error( $att_id ) ) {
		echo 'WARN featured ' . $post_id . ': ' . $att_id->get_error_message() . "\n";
		return;
	}
	set_post_thumbnail( $post_id, (int) $att_id );
}

/**
 * Ensure a category exists; return term_id.
 *
 * @param string $name Name.
 * @param string $slug Slug.
 * @return int
 */
function iawp_pg_ensure_cat( $name, $slug ) {
	$term = get_term_by( 'slug', $slug, 'category' );
	if ( $term && ! is_wp_error( $term ) ) {
		return (int) $term->term_id;
	}
	$r = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
	if ( is_wp_error( $r ) ) {
		echo 'WARN cat ' . $slug . ': ' . $r->get_error_message() . "\n";
		return 0;
	}
	return (int) $r['term_id'];
}

// --- Jetpack contact form module (newsletter CTA) ---
if ( class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'activate_module' ) ) {
	Jetpack::activate_module( 'contact-form', false, false );
	echo "jetpack_contact_form=on\n";
} elseif ( function_exists( 'jetpack_activate_module' ) ) {
	jetpack_activate_module( 'contact-form', false, false );
	echo "jetpack_contact_form=on\n";
} else {
	echo "WARN: Jetpack contact-form not activated (class missing)\n";
}

update_option( 'blogname', 'IA+WP' );
update_option(
	'blogdescription',
	'Artigos e fluxos reais sobre inteligência artificial, WordPress moderno e soberania digital.'
);
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( false );

$home_id = iawp_pg_upsert_page(
	'Home',
	'home',
	'<!-- wp:pattern {"slug":"iawp/page-home"} /-->',
	'page-no-title'
);
$blog_id = iawp_pg_upsert_page(
	'Blog',
	'blog',
	'<!-- wp:pattern {"slug":"iawp/page-blog"} /-->',
	'page-no-title'
);
iawp_pg_upsert_page(
	'Sobre',
	'sobre',
	'<!-- wp:pattern {"slug":"iawp/page-sobre"} /-->',
	'page-no-title'
);

$legal_notice = '<!-- wp:group {"tagName":"main","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|75","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--75);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">%s</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>%s</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></main>
<!-- /wp:group -->';

iawp_pg_upsert_page(
	'Política de Privacidade',
	'privacidade',
	sprintf(
		$legal_notice,
		'Política de Privacidade',
		'Esta é uma cópia de demonstração do hub IA+WP no WordPress Playground. Em produção, a política completa está em iaewp.com.br/privacidade/.'
	),
	'page-no-title'
);
iawp_pg_upsert_page(
	'Termos de Uso',
	'termos',
	sprintf(
		$legal_notice,
		'Termos de Uso',
		'Esta é uma cópia de demonstração do hub IA+WP no WordPress Playground. Em produção, os termos completos estão em iaewp.com.br/termos/.'
	),
	'page-no-title'
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
update_option( 'page_for_posts', $blog_id );

$cat_gutenberg = iawp_pg_ensure_cat( 'IA + Gutenberg', 'ia-gutenberg' );
$cat_agentes  = iawp_pg_ensure_cat( 'IA + Agentes', 'ia-agentes' );

$posts = array(
	array(
		'title'   => 'Como criar sites com IA no WordPress moderno',
		'slug'    => 'criar-sites-ia-wordpress-moderno',
		'date'    => '2026-10-13 09:00:00',
		'excerpt' => 'Fluxo para criar ou refazer um site com IA no WordPress moderno: tema de blocos, fidelidade na página pública e no editor, painel sem depender da IA no dia a dia.',
		'cat'     => $cat_gutenberg,
		'image'   => 'https://iaewp.com.br/wp-content/uploads/2026/10/criar-sites-ia-wordpress-moderno-featured-21x9-1.png',
		'body'    => '<!-- wp:paragraph --><p>Demonstração no Playground do hub IA+WP. O artigo completo está em <a href="https://iaewp.com.br/criar-sites-ia-wordpress-moderno/">iaewp.com.br</a>.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Como gerar tutoriais WordPress em vídeo com um agente e Playwright',
		'slug'    => 'tutoriais-wordpress-video-agente-playwright',
		'date'    => '2026-09-22 09:00:00',
		'excerpt' => 'Do chat ao mp4: trilha, Playwright e render — sem OBS e sem gravar o desktop.',
		'cat'     => $cat_agentes,
		'image'   => 'https://iaewp.com.br/wp-content/uploads/2026/09/tutoriais-wp-video-featured-fluxo-21x9-1.png',
		'body'    => '<!-- wp:paragraph --><p>Demonstração no Playground. Artigo: <a href="https://iaewp.com.br/tutoriais-wordpress-video-agente-playwright/">iaewp.com.br</a>.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'WordPress e WebMCP: formulários como tools para agentes',
		'slug'    => 'wordpress-webmcp-formularios-agentes',
		'date'    => '2026-08-11 09:00:00',
		'excerpt' => 'Anotar formulários WordPress para agentes com WebMCP — lab OSS e Playground.',
		'cat'     => $cat_agentes,
		'image'   => 'https://iaewp.com.br/wp-content/uploads/2026/08/wordpress-webmcp-formularios-agentes-featured-v3-21x9-1.png',
		'body'    => '<!-- wp:paragraph --><p>Demonstração no Playground. Artigo: <a href="https://iaewp.com.br/wordpress-webmcp-formularios-agentes/">iaewp.com.br</a>.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Cursor no WordPress em produção: SSH, WP-CLI e MCP (sem plugin no site)',
		'slug'    => 'cursor-wordpress-producao-ssh-wp-cli-mcp',
		'date'    => '2026-08-05 20:08:22',
		'excerpt' => 'Operar WordPress em produção com o agente no IDE — SSH, WP-CLI e MCP, sem plugin no site.',
		'cat'     => $cat_agentes,
		'image'   => 'https://iaewp.com.br/wp-content/uploads/2026/08/cursor-wp-producao-featured.png',
		'body'    => '<!-- wp:paragraph --><p>Demonstração no Playground. Artigo: <a href="https://iaewp.com.br/cursor-wordpress-producao-ssh-wp-cli-mcp/">iaewp.com.br</a>.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'IA e WordPress: a combinação estratégica de 2026',
		'slug'    => 'ia-wordpress-combinacao-estrategica-2026',
		'date'    => '2026-05-19 23:25:36',
		'excerpt' => 'Tese do hub: inteligência artificial aplicada ao WordPress com soberania digital.',
		'cat'     => $cat_gutenberg,
		'image'   => 'https://iaewp.com.br/wp-content/uploads/2026/05/ia-wordpress-pilar.jpg',
		'body'    => '<!-- wp:paragraph --><p>Demonstração no Playground. Artigo: <a href="https://iaewp.com.br/ia-wordpress-combinacao-estrategica-2026/">iaewp.com.br</a>.</p><!-- /wp:paragraph -->',
	),
);

foreach ( $posts as $p ) {
	$existing = get_page_by_path( $p['slug'], OBJECT, 'post' );
	$args     = array(
		'post_title'   => $p['title'],
		'post_name'    => $p['slug'],
		'post_status'  => 'publish',
		'post_type'    => 'post',
		'post_content' => $p['body'],
		'post_excerpt' => $p['excerpt'],
		'post_date'    => $p['date'],
		'post_date_gmt'=> get_gmt_from_date( $p['date'] ),
	);
	if ( $existing ) {
		$args['ID'] = (int) $existing->ID;
		$pid        = wp_update_post( $args, true );
	} else {
		$pid = wp_insert_post( $args, true );
	}
	if ( is_wp_error( $pid ) ) {
		echo 'FAIL post ' . $p['slug'] . ': ' . $pid->get_error_message() . "\n";
		continue;
	}
	$pid = (int) $pid;
	if ( $p['cat'] ) {
		wp_set_post_categories( $pid, array( $p['cat'] ) );
	}
	iawp_pg_set_featured_from_url( $pid, $p['image'] );
	echo 'post=' . $p['slug'] . ' id=' . $pid . "\n";
}

$theme = wp_get_theme();
if ( method_exists( $theme, 'delete_pattern_cache' ) ) {
	$theme->delete_pattern_cache();
}
delete_transient( 'wp_block_patterns' );
wp_cache_flush();

echo 'home_id=' . $home_id . "\n";
echo 'blog_id=' . $blog_id . "\n";
echo "ok=true\n";
