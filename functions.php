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
 *  Ícones: assets/icons/ → coleção WP Icons API (`iawp/*`) para core/icon;
 *  legado span→SVG via ob_start ainda cobre header/CTA pontuais.
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
	$deps = array();
	if ( wp_style_is( 'bhb-block-header-behavior', 'registered' ) ) {
		$deps[] = 'bhb-block-header-behavior';
	}
	wp_enqueue_style(
		'iawp-style',
		get_theme_file_uri( 'style.css' ),
		$deps,
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);

	// Proto motion.js — reveal + spotlight. scroll.js (reveal-up) era leftover DV; removido.
	wp_enqueue_script(
		'iawp-motion',
		get_theme_file_uri( 'assets/motion.js' ),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/motion.js' ),
		true
	);
	wp_script_add_data( 'iawp-motion', 'defer', true );

	wp_enqueue_script(
		'iawp-back-to-top',
		get_theme_file_uri( 'assets/back-to-top.js' ),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/back-to-top.js' ),
		true
	);
	wp_script_add_data( 'iawp-back-to-top', 'defer', true );
} );

/**
 * Back to top control (mobile CSS + back-to-top.js). Markup outside templates
 * so every view gets it without L0/page patterns.
 */
add_action( 'wp_footer', function () {
	$label = esc_attr__( 'Voltar ao topo', 'iawp' );
	echo '<a class="iawp-back-to-top" href="#main-content" aria-label="' . $label . '">';
	echo '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
	echo '</a>';
}, 20 );

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
 * Adapta SVG Lucide ao allowlist kses da Icons API (só svg/path/polygon).
 * circle → path; stroke/fill no root ficam a cargo do CSS do tema.
 *
 * @param string $svg Markup SVG cru de assets/icons/.
 * @return string Markup compatível com sanitize_icon_content do core.
 */
function iawp_icon_svg_for_core( string $svg ): string {
	return (string) preg_replace_callback(
		'/<circle\b([^>]*)\/?>/i',
		static function ( array $m ): string {
			$attrs = $m[1];
			if (
				! preg_match( '/\bcx="([^"]+)"/i', $attrs, $cx )
				|| ! preg_match( '/\bcy="([^"]+)"/i', $attrs, $cy )
				|| ! preg_match( '/\br="([^"]+)"/i', $attrs, $r )
			) {
				return '';
			}
			$cx_v = (float) $cx[1];
			$cy_v = (float) $cy[1];
			$r_v  = (float) $r[1];
			$fill = '';
			if ( preg_match( '/\bfill="([^"]+)"/i', $attrs, $f ) && 'none' !== strtolower( $f[1] ) ) {
				$fill = ' fill="' . esc_attr( $f[1] ) . '"';
			}
			// Dois arcos = círculo fechado (kses permite path, não circle).
			$d = sprintf(
				'M%s %s a%s %s 0 1 0 %s 0 a%s %s 0 1 0 %s 0',
				iawp_svg_num( $cx_v - $r_v ),
				iawp_svg_num( $cy_v ),
				iawp_svg_num( $r_v ),
				iawp_svg_num( $r_v ),
				iawp_svg_num( $r_v * 2 ),
				iawp_svg_num( $r_v ),
				iawp_svg_num( $r_v ),
				iawp_svg_num( -$r_v * 2 )
			);
			return '<path d="' . esc_attr( $d ) . '"' . $fill . '/>';
		},
		$svg
	);
}

/**
 * @param float $n Número para atributo d do path.
 */
function iawp_svg_num( float $n ): string {
	$s = rtrim( rtrim( sprintf( '%.4f', $n ), '0' ), '.' );
	return '' === $s || '-' === $s ? '0' : $s;
}

/**
 * Registra a biblioteca Lucide do tema na Icons API (WP 7.1+ / core/icon).
 *
 * Cada arquivo em assets/icons/{slug}.svg vira `iawp/{slug}` — mesma pasta
 * usada pelo inliner legado e pelos sociais. Prioridade sobre core/html.
 *
 * Armadilha: sanitize_icon_content do core só permite svg/path/polygon e
 * remove stroke* — por isso circle→path aqui e stroke via CSS (.bento-icon).
 */
add_action( 'init', function (): void {
	if ( ! function_exists( 'wp_register_icon_collection' ) || ! function_exists( 'wp_register_icon' ) ) {
		return;
	}

	$dir = get_stylesheet_directory() . '/assets/icons';
	if ( ! is_dir( $dir ) ) {
		return;
	}

	wp_register_icon_collection(
		'iawp',
		array(
			'label'       => __( 'IA+WP', 'iawp' ),
			'description' => __( 'Ícones Lucide do tema (assets/icons).', 'iawp' ),
		)
	);

	$files = glob( $dir . '/*.svg' );
	if ( ! is_array( $files ) ) {
		return;
	}

	foreach ( $files as $file ) {
		$slug = basename( $file, '.svg' );
		if ( ! preg_match( '/^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$/', $slug ) ) {
			continue;
		}
		$raw = (string) file_get_contents( $file );
		if ( '' === $raw ) {
			continue;
		}
		$label = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
		wp_register_icon(
			'iawp/' . $slug,
			array(
				'label'   => $label,
				'content' => iawp_icon_svg_for_core( $raw ),
			)
		);
	}
}, 11 );

/**
 * Injeta o <svg> hidden com <defs> do gradiente brand logo no body_open.
 * Emitido uma vez — qualquer SVG da página pode referenciar url(#grad-brand),
 * incluindo os SVGs nativos do core/social-links.
 */
add_action( 'wp_body_open', function (): void {
	echo '<div class="site-grain" aria-hidden="true"></div>';
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

	// Front page — hub editorial (hub.css traz listagem; blog.css conflita com featured/cards)
	if ( $post_id === $front_id && $front_id > 0 ) {
		wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
		wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
		return;
	}

	// Blog index + archives — hub listagem (.posts-hub) + page-hero / archive-hero slim
	if ( ( $post_id === $blog_id && $blog_id > 0 ) || is_home() || is_archive() ) {
		wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
		wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), array( 'iawp-hub' ), $mtime( 'assets/blocks/blog.css' ) );
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

	// Sobre — page-hero (blog.css) + about/pillars (hub) + newsletter (cta)
	if ( $obj instanceof WP_Post && $obj->post_name === 'sobre' ) {
		wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
		wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), array( 'iawp-hub' ), $mtime( 'assets/blocks/blog.css' ) );
		wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
		return;
	}

	// Páginas legais
	if ( $obj instanceof WP_Post && in_array( $obj->post_name, [ 'privacidade', 'termos' ], true ) ) {
		wp_enqueue_style( 'iawp-legal', get_theme_file_uri( 'assets/blocks/legal.css' ), $base, $mtime( 'assets/blocks/legal.css' ) );
		return;
	}

	// Demais páginas — page-hero (template page) + tipografia nativa
	if ( $obj instanceof WP_Post && $obj->post_type === 'page' ) {
		wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), $base, $mtime( 'assets/blocks/blog.css' ) );
		return;
	}

	// 404 editorial — wash/frame + botões (sem newsletter / hub listagem)
	if ( is_404() ) {
		wp_enqueue_style( 'iawp-error404', get_theme_file_uri( 'assets/blocks/error404.css' ), $base, $mtime( 'assets/blocks/error404.css' ) );
	}
} );

/**
 * Descrições dos templates custom na listagem do Site Editor.
 *
 * theme.json customTemplates só aceita name / title / postTypes — sem description.
 * Não usar default_template_types: isso marca is_custom=false e tira o template
 * do seletor de páginas.
 */
add_filter( 'get_block_templates', function ( array $templates, $query, string $template_type ): array {
	if ( 'wp_template' !== $template_type ) {
		return $templates;
	}

	$meta = array(
		'page-no-title' => array(
			'title'       => 'Página sem título',
			'description' => 'Exibe o conteúdo da página sem título automático — útil quando o cabeçalho e o layout vêm do próprio conteúdo.',
		),
		'page-legal'    => array(
			'title'       => 'Legal',
			'description' => 'Layout para páginas legais (privacidade, termos): título e prosa estreita, sem meta de post.',
		),
	);

	foreach ( $templates as $template ) {
		if ( ! isset( $meta[ $template->slug ] ) ) {
			continue;
		}
		$template->title       = $meta[ $template->slug ]['title'];
		$template->description = $meta[ $template->slug ]['description'];
	}

	return $templates;
}, 10, 3 );

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

/**
 * Single meta — sempre exibir “Atualizado” (fidelidade artigo.html).
 *
 * O core omite <time> do post-date modified quando modified ≤ publish.
 * O proto mostra os dois pares mesmo com a mesma data civil.
 */
add_filter( 'render_block_core/post-date', function ( $content, $block ) {
	$class = (string) ( $block['attrs']['className'] ?? '' );
	if ( ! str_contains( $class, 'wp-block-post-date__modified-date' ) ) {
		return $content;
	}
	if ( is_string( $content ) && str_contains( $content, '<time' ) ) {
		return $content;
	}

	$post = get_post();
	if ( ! $post instanceof WP_Post ) {
		return $content;
	}

	$format = $block['attrs']['format'] ?? get_option( 'date_format' );
	$display = get_the_modified_date( $format, $post );
	if ( ! is_string( $display ) || $display === '' ) {
		$display = get_the_date( $format, $post );
	}
	$datetime = get_the_modified_date( DATE_W3C, $post );
	if ( ! is_string( $datetime ) || $datetime === '' ) {
		$datetime = get_the_date( DATE_W3C, $post );
	}

	$classes = array( 'wp-block-post-date', 'single-author-date', 'wp-block-post-date__modified-date' );
	if ( ! empty( $block['attrs']['textColor'] ) ) {
		$classes[] = 'has-text-color';
		$classes[] = 'has-' . sanitize_title( $block['attrs']['textColor'] ) . '-color';
	}

	$style_bits = array();
	if ( ! empty( $block['attrs']['style']['typography']['fontSize'] ) ) {
		$style_bits[] = 'font-size:' . $block['attrs']['style']['typography']['fontSize'];
	}
	if ( isset( $block['attrs']['style']['spacing']['margin']['top'] ) ) {
		$style_bits[] = 'margin-top:' . $block['attrs']['style']['spacing']['margin']['top'];
	}
	if ( isset( $block['attrs']['style']['spacing']['margin']['bottom'] ) ) {
		$style_bits[] = 'margin-bottom:' . $block['attrs']['style']['spacing']['margin']['bottom'];
	}
	$style_attr = $style_bits ? ' style="' . esc_attr( implode( ';', $style_bits ) ) . '"' : '';

	return sprintf(
		'<div class="%s"%s><time datetime="%s">%s</time></div>',
		esc_attr( implode( ' ', $classes ) ),
		$style_attr,
		esc_attr( $datetime ),
		esc_html( $display )
	);
}, 10, 2 );

/**
 * Sidebar “Relacionados” — reforço do nativo excludeCurrent (WP 7.1+).
 *
 * O core usa get_the_ID(); no canvas/FSE às vezes o ID está só no queried object.
 * className no query NÃO chega ao filtro (build usa o bloco com context, não o
 * wrapper com className) — por isso o match por class falhava e o self entrava.
 */
add_filter( 'query_loop_block_query_vars', function ( array $query, $block ): array {
	$q = array();
	if ( $block instanceof WP_Block ) {
		$q = $block->context['query'] ?? ( $block->attributes['query'] ?? array() );
	}

	// Relacionados: excludeCurrent
	if ( ! empty( $q['excludeCurrent'] ) ) {
		$id = (int) get_the_ID();
		if ( $id <= 0 ) {
			$id = (int) get_queried_object_id();
		}
		if ( $id > 0 ) {
			$exclude = isset( $query['post__not_in'] ) ? (array) $query['post__not_in'] : array();
			$exclude[] = $id;
			$query['post__not_in'] = array_values( array_unique( array_map( 'intval', $exclude ) ) );
		}
	}

	/*
	 * Archive featured/cards: inherit:false + perPage/offset (offset some com inherit).
	 * Sem tax_query explícito no pattern — o filtro injeta o termo/autor/data atuais.
	 * Não usar className (não chega ao filtro; ver excludeCurrent acima).
	 */
	if ( empty( $q['inherit'] ) && ( is_category() || is_tag() || is_tax() || is_author() || is_date() ) ) {
		$already =
			! empty( $query['cat'] ) ||
			! empty( $query['tag_id'] ) ||
			! empty( $query['tax_query'] ) ||
			! empty( $query['author'] ) ||
			! empty( $query['year'] );
		if ( ! $already ) {
			if ( is_category() ) {
				$query['cat'] = (int) get_queried_object_id();
			} elseif ( is_tag() ) {
				$query['tag_id'] = (int) get_queried_object_id();
			} elseif ( is_tax() ) {
				$term = get_queried_object();
				if ( $term instanceof WP_Term ) {
					$query['tax_query'] = array(
						array(
							'taxonomy' => $term->taxonomy,
							'field'    => 'term_id',
							'terms'    => (int) $term->term_id,
						),
					);
				}
			} elseif ( is_author() ) {
				$query['author'] = (int) get_queried_object_id();
			} elseif ( is_date() ) {
				global $wp_query;
				foreach ( array( 'year', 'monthnum', 'day' ) as $key ) {
					if ( ! empty( $wp_query->query_vars[ $key ] ) ) {
						$query[ $key ] = $wp_query->query_vars[ $key ];
					}
				}
			}
		}
	}

	return $query;
}, 10, 2 );

/**
 * Archive hero title — proto accent na última parte após "+" (query-title é texto plano).
 * Só no front; o bloco no editor permanece nativo.
 */
add_filter( 'render_block_core/query-title', function ( string $html ): string {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $html;
	}
	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return $html;
	}
	return (string) preg_replace_callback(
		'/(<h1\b[^>]*>)(.*?)(<\/h1>)/ius',
		static function ( array $m ): string {
			$plain = trim( html_entity_decode( wp_strip_all_tags( $m[2] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			if ( ! preg_match( '/^(.+\+)\s+(.+)$/u', $plain, $parts ) ) {
				return $m[0];
			}
			return $m[1]
				. esc_html( trim( $parts[1] ) )
				. '<br><span class="text-accent">'
				. esc_html( trim( $parts[2] ) )
				. '</span>'
				. $m[3];
		},
		$html,
		1
	);
} );

/**
 * Meta do archive: "N artigos" em vez de "N resultados encontrados" (query-total i18n).
 */
add_filter( 'render_block_core/query-total', function ( string $html ): string {
	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return $html;
	}
	return (string) preg_replace(
		'/(\d+)\s+resultados?\s+encontrados?/iu',
		'$1 artigos',
		$html
	);
} );

// Seções semânticas: core/group com tagName:"section" e ariaLabel nos atributos do bloco.
// O WP renderiza <section aria-label> nativamente — não é necessário filtro render_block.

// Sociais do autor (Lucide stroke): cor via CSS stroke + #grad-brand no body.
// Não injetar fill em path — isso quebrava o traço e “completava” o ícone só no hover.

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

/**
 * 301 /category/… → /categoria/… when category_base is Portuguese.
 * Keeps old indexes and external links from 404ing after the base change.
 */
add_action( 'template_redirect', function (): void {
	if ( 'categoria' !== (string) get_option( 'category_base' ) ) {
		return;
	}
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	if ( ! preg_match( '#^/category(/.*)?$#', $path, $m ) ) {
		return;
	}
	$tail = isset( $m[1] ) ? $m[1] : '/';
	$query = (string) wp_parse_url( $uri, PHP_URL_QUERY );
	$target = home_url( '/categoria' . $tail );
	if ( $query !== '' ) {
		$target .= '?' . $query;
	}
	wp_safe_redirect( $target, 301 );
	exit;
}, 0 );

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
 * P0-1 variante A — depriorizar GTag do Site Kit (tracking preservado).
 *
 * - dataLayer / gtag() inline permanece no head (fila de eventos).
 * - google_gtagjs (arquivo grande) vai para o footer com fetchpriority=low.
 * - Remove dns-prefetch de googletagmanager (evita descoberta precoce).
 *
 * Inspirado em Weston Ruter / Site Kit GTag Script Deprioritization (GPLv2+).
 * Bypass smoke: ?disable_iawp_gtag_deprioritization=1
 *
 * @see https://github.com/westonruter/google-site-kit-gtag-script-deprioritization
 * @see https://github.com/google/site-kit-wp/issues/10825
 */
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( isset( $_GET['disable_iawp_gtag_deprioritization'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$handle = 'google_gtagjs';
		$wp_scripts = wp_scripts();
		$dep        = $wp_scripts->query( $handle, 'registered' );
		if ( ! ( $dep instanceof _WP_Dependency ) || ! wp_script_is( $handle, 'enqueued' ) ) {
			return;
		}

		// Footer group.
		$dep->add_data( 'group', 1 );

		$inline_script = null;
		foreach ( array( 'before', 'after' ) as $key ) {
			if ( empty( $dep->extra[ $key ] ) || ! is_array( $dep->extra[ $key ] ) ) {
				continue;
			}
			foreach ( $dep->extra[ $key ] as $i => $code ) {
				if ( is_string( $code ) && false !== strpos( $code, 'function gtag(' ) ) {
					$inline_script = $code;
					array_splice( $dep->extra[ $key ], $i, 1 );
					break 2;
				}
			}
		}

		if ( null !== $inline_script ) {
			$inline_handle = 'iawp-gtag-datalayer';
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- stub head-only.
			wp_register_script( $inline_handle, false, array(), null, false );
			wp_add_inline_script( $inline_handle, $inline_script );
			wp_enqueue_script( $inline_handle );
		}
	},
	100
);

add_filter(
	'wp_script_attributes',
	static function ( $attributes ) {
		if ( isset( $_GET['disable_iawp_gtag_deprioritization'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $attributes;
		}
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		if ( isset( $attributes['id'] ) && 'google_gtagjs-js' === $attributes['id'] ) {
			$attributes['fetchpriority'] = 'low';
		}
		return $attributes;
	}
);

add_filter(
	'wp_resource_hints',
	static function ( $urls, $relation_type ) {
		if ( isset( $_GET['disable_iawp_gtag_deprioritization'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $urls;
		}
		if ( 'dns-prefetch' !== $relation_type || ! is_array( $urls ) ) {
			return $urls;
		}
		return array_values(
			array_filter(
				$urls,
				static function ( $url ): bool {
					return ! is_string( $url ) || false === strpos( $url, 'www.googletagmanager.com' );
				}
			)
		);
	},
	100,
	2
);

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
add_filter( 'block_core_social_link_get_services', function ( array $services ): array {

	$theme_icons = get_stylesheet_directory() . '/assets/icons/';

	// Lucide stroke — fidelidade ao hub. Sobrescreve logos brand do core.
	// wordpress → blocks (Gutenberg); site → earth; buymeacoffee → coffee.
	$map = array(
		'linkedin'     => array( 'name' => __( 'LinkedIn', 'iawp' ), 'file' => 'linkedin.svg' ),
		'wordpress'    => array( 'name' => __( 'WordPress.org', 'iawp' ), 'file' => 'blocks.svg' ),
		'github'       => array( 'name' => __( 'GitHub', 'iawp' ), 'file' => 'github.svg' ),
		'workana'      => array( 'name' => __( 'Workana', 'iawp' ), 'file' => 'users.svg' ),
		'buymeacoffee' => array( 'name' => __( 'Buy me a coffee', 'iawp' ), 'file' => 'coffee.svg' ),
		'site'         => array( 'name' => __( 'Site', 'iawp' ), 'file' => 'earth.svg' ),
	);
	foreach ( $map as $slug => $meta ) {
		$file = $theme_icons . $meta['file'];
		if ( ! is_readable( $file ) ) {
			continue;
		}
		$services[ $slug ] = array(
			'name' => $meta['name'],
			'icon' => (string) file_get_contents( $file ),
		);
	}

	return $services;
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
			$icons_data[ $slug ] = trim( (string) file_get_contents( $file ) );
		}
	}

	wp_localize_script( 'iawp-social-variations', 'iawpSocialIcons', $icons_data );
} );

/**
 * Remove o emoji ✨ do heading de sucesso do Jetpack Forms.
 * A string é gerada por get_default_thank_you_heading() que concatena
 * o emoji após a string traduzida — interceptamos via gettext filter.
 */


