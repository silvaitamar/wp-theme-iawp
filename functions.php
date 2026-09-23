<?php
/**
 * IA+WP theme functions.
 *
 * Responsabilidades:
 *  - Suporte a funcionalidades do tema (title-tag, thumbnails, embeds)
 *  - Enqueue do stylesheet e scripts do tema
 *  - Semântica HTML via render_block (nav aria-label)
 *  - Performance de imagens do tema via wp_content_img_tag
 *  - Defer em scripts do tema
 *
 * FONTES: carregadas via theme.json fontFace + arquivos locais em assets/fonts/.
 *  O WP gera os @font-face automaticamente e os serve inline no global stylesheet.
 *  Não usar wp_enqueue_style para Google Fonts — gera request externo bloqueante.
 *  Não usar add_editor_style para Google Fonts — o editor lê fontFace do theme.json.
 *  Ícones: SVGs locais em assets/icons/ (Lucide) via ob_start filter.
 *
 * NÃO inclui: meta description, Open Graph, Twitter Card, JSON-LD por post,
 * canonical por post, sitemap — responsabilidade do plugin The SEO Framework
 * (autodescription). O tema gerencia apenas o que o plugin não cobre:
 * remoção de ruído do <head> (generator, rsd, oembed), llms.txt,
 * e o <title> do blog archive como fallback quando TSF não está ativo.
 *
 * @package iawp
 */

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

	// CSS das seções no editor — paridade visual com o frontend.
	// No editor não há render_block on-demand, então registramos todos
	// via add_editor_style para que o canvas renderize corretamente.
	$section_styles = [
		'assets/blocks/hub.css',
		'assets/blocks/hero.css',
		'assets/blocks/author.css',
		'assets/blocks/cta.css',
		'assets/blocks/blog.css',
		'assets/blocks/single.css',
		'assets/blocks/legal.css',
	];
	foreach ( $section_styles as $file ) {
		add_editor_style( $file );
	}
} );

/**
 * Enqueue stylesheet e scripts no frontend.
 *
 * Geist e Inter são carregadas via theme.json fontFace + arquivos locais.
 * Ícones SVG locais (Lucide) — zero request externo de fonte de ícones.
 */
/**
 * Jetpack Form UX — auto-reset + no-scroll.
 * Registrado como ES module para ter acesso ao @wordpress/interactivity store.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! function_exists( 'wp_enqueue_script_module' ) ) return;
	wp_enqueue_script_module(
		'iawp/jetpack-form-ux',
		get_theme_file_uri( 'assets/jetpack-form-ux.js' ),
		[ [ 'id' => '@wordpress/interactivity', 'import' => 'dynamic' ] ],
		(string) filemtime( get_stylesheet_directory() . '/assets/jetpack-form-ux.js' )
	);
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'iawp-style',
		get_theme_file_uri( 'style.css' ),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);

	wp_enqueue_script(
		'iawp-scroll',
		get_theme_file_uri( 'assets/scroll.js' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
	wp_script_add_data( 'iawp-scroll', 'defer', true );

	wp_enqueue_script(
		'iawp-motion',
		get_theme_file_uri( 'assets/motion.js' ),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/motion.js' ),
		true
	);
	wp_script_add_data( 'iawp-motion', 'defer', true );
} );

/**
 * Substitui <span class="icon">nome</span> e <span class="bento-icon">nome</span>
 * por SVG inline local — elimina request externo de ícones.
 *
 * Os SVGs são armazenados em assets/icons/{nome}.svg e injetados no HTML
 * via ob_start/ob_end_clean no frontend. Zero dependência externa de fonte.
 *
 * Compatível com editor: o editor não executa ob_start, os spans permanecem
 * como texto no canvas — sem impacto na experiência de edição.
 */
/**
 * Gradiente SVG reutilizável para ícones bento.
 * Definido uma vez como <defs> dentro do SVG — sem request externo.
 */
function iawp_svg_grad_defs(): string {
	return '<defs><linearGradient id="grad-brand" x1="0" y1="0" x2="1" y2="1">' .
		'<stop offset="0%" stop-color="#3B82F6"/>' .
		'<stop offset="100%" stop-color="#8B5CF6"/>' .
		'</linearGradient></defs>';
}

function iawp_inline_icons( string $html ): string {
	$icons_dir = get_stylesheet_directory() . '/assets/icons/';

	// Classes que recebem gradiente brand nos ícones
	$gradient_classes = array( 'bento-icon', 'author-avatar', 'icon' );

	return preg_replace_callback(
		'/<(span|p) class="([^"]*(?:bento-icon|author-avatar|author-avatar-label|\bicon\b)[^"]*)">([a-z0-9_]+)<\/\1>/',
		function ( array $matches ) use ( $icons_dir, $gradient_classes ): string {
			$tag     = $matches[1];
			$classes = $matches[2];
			$name    = $matches[3];
			$file    = $icons_dir . $name . '.svg';

			if ( ! file_exists( $file ) ) {
				return $matches[0];
			}

			$svg        = file_get_contents( $file );
			$use_grad   = false;
			foreach ( $gradient_classes as $gc ) {
				if ( strpos( $classes, $gc ) !== false ) {
					$use_grad = true;
					break;
				}
			}

			if ( $use_grad ) {
				// Referenciar grad-brand global (injetado via wp_body_open) — sem defs inline
				$svg = preg_replace( '/<svg/', '<svg class="' . esc_attr( $classes ) . '" aria-hidden="true" focusable="false"', $svg, 1 );
				// Substituir fill nos path/rect para referenciar o gradiente global
				$svg = preg_replace( '/<path/', '<path fill="url(#grad-brand)"', $svg );
			} else {
				// Ícones regulares: fill=currentColor (herda a cor do CSS)
				$svg = preg_replace(
					'/<svg/',
					'<svg class="' . esc_attr( $classes ) . '" aria-hidden="true" focusable="false"',
					$svg,
					1
				);
			}

			return $svg;
		},
		$html
	);
}

add_action( 'template_redirect', function (): void {
	if ( is_admin() ) return;
	ob_start( 'iawp_inline_icons' );
} );

/**
 * Injeta o <svg> hidden com <defs> do gradiente brand logo no body_open.
 * Emitido uma vez — qualquer SVG da página pode referenciar url(#grad-brand),
 * incluindo os SVGs nativos do core/social-links.
 */
add_action( 'wp_body_open', function (): void {
	echo '<svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">' .
		'<defs><linearGradient id="grad-brand" x1="0" y1="0" x2="1" y2="1">' .
		'<stop offset="0%" stop-color="#3B82F6"/>' .
		'<stop offset="100%" stop-color="#8B5CF6"/>' .
		'</linearGradient></defs>' .
		'</svg>';
} );

/**
 * CSS das seções: enqueue direto por tipo de página.
 *
 * O sistema anterior de bundle on-demand via render_block_core/group
 * não funcionava em block themes FSE porque o template é renderizado
 * APÓS wp_head — quando wp_enqueue_scripts já foi disparado, os <link>
 * não entram no <head>.
 *
 * Solução: conditional tags no wp_enqueue_scripts. Simples, confiável,
 * sem race condition entre render_block e wp_head.
 *
 * Paridade no editor: add_editor_style() no after_setup_theme
 * registra todos os arquivos individualmente.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	$base  = [ 'iawp-style' ];
	$dir   = get_stylesheet_directory();
	$mtime = function( string $file ) use ( $dir ): string {
		$path = $dir . '/' . $file;
		return file_exists( $path ) ? (string) filemtime( $path ) : '1';
	};

	// Discrimina por queried_object (resolvido antes de wp_enqueue_scripts no browser).
	$post_id  = get_queried_object_id();
	$front_id = (int) get_option( 'page_on_front' );
	$blog_id  = (int) get_option( 'page_for_posts' );

	// Front page — hub editorial
	if ( $post_id === $front_id && $front_id > 0 ) {
		wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
		wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), $base, $mtime( 'assets/blocks/blog.css' ) );
		wp_enqueue_style( 'iawp-author', get_theme_file_uri( 'assets/blocks/author.css' ), $base, $mtime( 'assets/blocks/author.css' ) );
		wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
		return;
	}

	// Blog index + category/tag archives (mesma listagem do proto)
	if ( ( $post_id === $blog_id && $blog_id > 0 ) || is_category() || is_tag() || is_tax() ) {
		wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), $base, $mtime( 'assets/blocks/blog.css' ) );
		wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
		return;
	}

	// Single post
	$obj = get_queried_object();
	if ( $obj instanceof WP_Post && $obj->post_type === 'post' ) {
		wp_enqueue_style( 'iawp-single', get_theme_file_uri( 'assets/blocks/single.css' ), $base, $mtime( 'assets/blocks/single.css' ) );
		wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
		return;
	}

	// Sobre — autor + hub
	if ( $obj instanceof WP_Post && $obj->post_name === 'sobre' ) {
		wp_enqueue_style( 'iawp-author', get_theme_file_uri( 'assets/blocks/author.css' ), $base, $mtime( 'assets/blocks/author.css' ) );
		wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
		return;
	}

	// Páginas legais
	if ( $obj instanceof WP_Post && in_array( $obj->post_name, [ 'privacidade', 'termos' ], true ) ) {
		wp_enqueue_style( 'iawp-legal', get_theme_file_uri( 'assets/blocks/legal.css' ), $base, $mtime( 'assets/blocks/legal.css' ) );
		return;
	}

	// 404
	if ( is_404() ) {
		wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
	}
} );

/**
 * aria-label nas navegações do tema.
 *
 * O atributo ariaLabel no JSON do bloco core/navigation concatena com o ariaLabel
 * do data-wp-context do Interactivity API, duplicando o texto no aria-label do <nav>.
 * Solução: injetar via render_block com className como discriminador.
 */
add_filter( 'render_block', function ( $html, $block ) {
	if ( $block['blockName'] !== 'core/navigation' ) return $html;
	$class = $block['attrs']['className'] ?? '';
	if ( strpos( $class, 'site-header__nav' ) !== false ) {
		// Proto: aria-label="Principal"
		$html = preg_replace( '/<nav\b/', '<nav aria-label="Principal"', $html, 1 );
	} elseif ( strpos( $class, 'footer-nav' ) !== false ) {
		$html = preg_replace( '/<nav\b/', '<nav aria-label="Rodapé"', $html, 1 );
	}
	return $html;
}, 10, 2 );

// Seções semânticas: core/group com tagName:"section" e ariaLabel nos atributos do bloco.
// O WP renderiza <section aria-label> nativamente — não é necessário filtro render_block.

/**
 * Injeta <defs> do gradiente brand e fill="url(#grad-brand)" nos SVGs
 * renderizados pelo core/social-link — mesmo padrão dos bento-icons.
 *
 * O fill: url(#grad-brand) via CSS não funciona cross-SVG (o <defs> precisa
 * estar dentro do mesmo elemento <svg>). Solução: injetar via render_block.
 */
add_filter( 'render_block', function ( $html, $block ) {
	if ( $block['blockName'] !== 'core/social-link' ) return $html;

	// Serviços que já trazem o gradiente embutido no SVG (via <mask> + <defs> inline)
	// não precisam do filtro — injetar por cima quebraria a estrutura.
	$self_contained = array( 'workana' );
	$service = $block['attrs']['service'] ?? '';
	if ( in_array( $service, $self_contained, true ) ) {
		return $html;
	}

	// Demais serviços: injetar <defs> + fill="url(#grad-brand)" em paths sem fill
	$html = preg_replace( '/(<svg[^>]*>)/', '$1' . iawp_svg_grad_defs(), $html, 1 );
	$html = preg_replace( '/<path\b(?![^>]*\bfill=)/', '<path fill="url(#grad-brand)"', $html );
	return $html;
}, 10, 2 );

/**
 * Title do blog archive — fallback quando TSF não está ativo.
 *
 * O WP core usa "Blog" como título de page_for_posts. O The SEO Framework
 * sobrescreve via filtro de alta prioridade — este só atua como fallback.
 *
 * Canonical para is_home(): responsabilidade do TSF. O WP core também gera
 * canonical via wp_head priority 10 para page_for_posts — não duplicar aqui.
 */
add_filter( 'pre_get_document_title', function ( $title ) {
	if ( ! is_home() ) return $title;
	if ( class_exists( '\The_SEO_Framework\Load' ) ) return $title;
	return 'Blog sobre IA e WordPress &#8211; ' . get_bloginfo( 'name' );
} );

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
add_action( 'init', function () {
	add_rewrite_rule( '^llms\.txt$', 'index.php?dv_llms=1', 'top' );
} );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'dv_llms'; return $vars;
} );
add_action( 'template_redirect', function () {
	if ( ! get_query_var( 'dv_llms' ) ) return;
	$file = get_template_directory() . '/llms.txt';
	if ( ! file_exists( $file ) ) { status_header( 404 ); exit; }
	header( 'Content-Type: text/plain; charset=UTF-8' );
	header( 'Cache-Control: public, max-age=86400' );
	readfile( $file );
	exit;
} );

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );

/**
 * Injeta loading="lazy" e decoding="async" em imagens do tema.
 *
 * core/image save() emite apenas src + alt. Adicionar loading/decoding diretamente
 * no HTML do bloco invalida o bloco (divergência com save()). O filtro
 * wp_content_img_tag (WP 6.1+) é o ponto correto para esta injeção.
 *
 * Imagens da Media Library já recebem tratamento automático do WP.
 * A imagem hero-bg é above-the-fold e já tem loading="eager" no markup core/html.
 */
add_filter( 'wp_content_img_tag', function ( $tag, $context, $attachment_id ) {
	if ( $attachment_id ) return $tag;
	if ( strpos( $tag, get_stylesheet_directory_uri() ) === false ) return $tag;
	if ( strpos( $tag, 'hero-bg' ) !== false ) return $tag;
	if ( strpos( $tag, 'loading=' ) === false )
		$tag = str_replace( '<img ', '<img loading="lazy" ', $tag );
	if ( strpos( $tag, 'decoding=' ) === false )
		$tag = str_replace( '<img ', '<img decoding="async" ', $tag );
	return $tag;
}, 10, 3 );

/**
 * Defer em scripts com o data flag — evita render-blocking sem quebrar dependências.
 */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	if ( wp_scripts()->get_data( $handle, 'defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}, 10, 2 );

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
 * 2. JS (wp_add_inline_script): registrar variação no editor
 *    — aparece no seletor de ícone do bloco inspector
 *
 * Para adicionar novos serviços: duplicar a entrada em ambos os
 * arrays ($custom_services e o objeto JS) usando o mesmo slug.
 * ============================================================
 */

/**
 * 1. PHP — registrar SVGs no servidor.
 * Filtro disponível desde WP 6.9 (block_core_social_link_get_services).
 * Em versões anteriores usava-se render_block para substituir o HTML.
 */
add_filter( 'block_core_social_link_get_services', function ( array $services ): array {

	$theme_icons = get_stylesheet_directory() . '/assets/icons/';

	$custom_services = array(
		'workana' => array(
			'name' => 'Workana',
			// SVG lido do arquivo local — mesma convenção dos ícones do tema
			'icon' => file_exists( $theme_icons . 'workana.svg' )
				? file_get_contents( $theme_icons . 'workana.svg' )
				: '',
		),
		// Para adicionar mais serviços no futuro:
		// 'slug' => array( 'name' => 'Nome', 'icon' => file_get_contents( $theme_icons . 'slug.svg' ) ),
	);

	return array_merge( $services, $custom_services );
} );

/**
 * 2. JS — registrar variação no editor Gutenberg.
 *
 * Arquivo JS separado com dependências explícitas em wp-blocks e wp-element.
 * Os SVGs são passados via wp_localize_script para evitar duplicar strings.
 */
add_action( 'enqueue_block_editor_assets', function (): void {

	$theme_icons = get_stylesheet_directory() . '/assets/icons/';
	$theme_uri   = get_stylesheet_directory_uri();

	wp_enqueue_script(
		'iawp-social-variations',
		$theme_uri . '/assets/social-variations.js',
		array( 'wp-blocks', 'wp-element' ),
		filemtime( get_stylesheet_directory() . '/assets/social-variations.js' ),
		true
	);

	// SVGs passados do servidor para o JS
	$icons_data   = array();
	$custom_slugs = array( 'workana' ); // adicionar futuros slugs aqui
	foreach ( $custom_slugs as $slug ) {
		$file = $theme_icons . $slug . '.svg';
		if ( file_exists( $file ) ) {
			$icons_data[ $slug ] = trim( file_get_contents( $file ) );
		}
	}

	wp_localize_script( 'iawp-social-variations', 'digitalVanguardSocialIcons', $icons_data );
} );

/**
 * Remove o emoji ✨ do heading de sucesso do Jetpack Forms.
 * A string é gerada por get_default_thank_you_heading() que concatena
 * o emoji após a string traduzida — interceptamos via gettext filter.
 */


