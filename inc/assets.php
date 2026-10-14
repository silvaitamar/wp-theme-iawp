<?php
/**
 * Front enqueue, footer chrome, image attrs, script/style perf.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

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
add_action(
	'wp_enqueue_scripts',
	function (): void {
		if ( ! function_exists( 'wp_enqueue_script_module' ) ) {
			return;
		}
		wp_enqueue_script_module(
			'iawp/jetpack-form-ux',
			get_theme_file_uri( 'assets/jetpack-form-ux.js' ),
			array(
				array(
					'id'     => '@wordpress/interactivity',
					'import' => 'dynamic',
				),
			),
			(string) filemtime( get_stylesheet_directory() . '/assets/jetpack-form-ux.js' )
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
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
	}
);

/**
 * Back to top control (mobile CSS + back-to-top.js). Markup outside templates
 * so every view gets it without L0/page patterns.
 */
add_action(
	'wp_footer',
	function () {
		$label = esc_attr__( 'Voltar ao topo', 'iawp' );
		echo '<a class="iawp-back-to-top" href="#main-content" aria-label="' . esc_attr( $label ) . '">';
		echo '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
		echo '</a>';
	},
	20
);
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
 * Onda 3 (avaliação): superfícies ficam neste mapa. wp_enqueue_block_style
 * referência FSE = CSS por *tipo de bloco* (embed/image/…), não hub/blog/single.
 * Ver docs/DESENVOLVEDORES.md §4 — candidato só se surgir folha por bloco.
 *
 * Paridade no editor: iawp_editor_style_files() + add_editor_style (setup.php).
 * Cache-bust: filemtime por arquivo; bump Theme Version em style.css após CSS/JS.
 */
add_action(
	'wp_enqueue_scripts',
	function (): void {
		$base  = array( 'iawp-style' );
		$dir   = get_stylesheet_directory();
		$mtime = function ( string $file ) use ( $dir ): string {
			$path = $dir . '/' . $file;
			return file_exists( $path ) ? (string) filemtime( $path ) : '1';
		};

		// Discrimina por queried_object (resolvido antes de wp_enqueue_scripts no browser).
		$post_id  = get_queried_object_id();
		$front_id = (int) get_option( 'page_on_front' );
		$blog_id  = (int) get_option( 'page_for_posts' );

		// Front page — hub editorial (hub.css traz listagem; blog.css conflita com featured/cards).
		if ( $post_id === $front_id && $front_id > 0 ) {
			wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
			wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
			return;
		}

		// Blog index + archives — hub listagem (.posts-hub) + page-hero / archive-hero slim.
		if ( ( $post_id === $blog_id && $blog_id > 0 ) || is_home() || is_archive() ) {
			wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
			wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), array( 'iawp-hub' ), $mtime( 'assets/blocks/blog.css' ) );
			wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
			return;
		}

		// Single post.
		$obj = get_queried_object();
		if ( $obj instanceof WP_Post && 'post' === $obj->post_type ) {
			wp_enqueue_style( 'iawp-single', get_theme_file_uri( 'assets/blocks/single.css' ), $base, $mtime( 'assets/blocks/single.css' ) );
			wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
			return;
		}

		// Sobre — page-hero (blog.css) + about/pillars (hub) + newsletter (cta).
		if ( $obj instanceof WP_Post && 'sobre' === $obj->post_name ) {
			wp_enqueue_style( 'iawp-hub', get_theme_file_uri( 'assets/blocks/hub.css' ), $base, $mtime( 'assets/blocks/hub.css' ) );
			wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), array( 'iawp-hub' ), $mtime( 'assets/blocks/blog.css' ) );
			wp_enqueue_style( 'iawp-cta', get_theme_file_uri( 'assets/blocks/cta.css' ), $base, $mtime( 'assets/blocks/cta.css' ) );
			return;
		}

		// Páginas legais.
		if ( $obj instanceof WP_Post && in_array( $obj->post_name, array( 'privacidade', 'termos' ), true ) ) {
			wp_enqueue_style( 'iawp-legal', get_theme_file_uri( 'assets/blocks/legal.css' ), $base, $mtime( 'assets/blocks/legal.css' ) );
			return;
		}

		// Demais páginas — page-hero (template page) + tipografia nativa.
		if ( $obj instanceof WP_Post && 'page' === $obj->post_type ) {
			wp_enqueue_style( 'iawp-blog', get_theme_file_uri( 'assets/blocks/blog.css' ), $base, $mtime( 'assets/blocks/blog.css' ) );
			return;
		}

		// 404 editorial — wash/frame + botões (sem newsletter / hub listagem).
		if ( is_404() ) {
			wp_enqueue_style( 'iawp-error404', get_theme_file_uri( 'assets/blocks/error404.css' ), $base, $mtime( 'assets/blocks/error404.css' ) );
		}
	}
);
/**
 * Injeta loading="lazy" e decoding="async" em imagens do tema.
 *
 * Core/image save() emite apenas src + alt. Adicionar loading/decoding diretamente
 * no HTML do bloco invalida o bloco (divergência com save()). O filtro
 * wp_content_img_tag (WP 6.1+) é o ponto correto para esta injeção.
 *
 * Imagens da Media Library já recebem tratamento automático do WP.
 * A imagem hero-bg é above-the-fold e já tem loading="eager" no markup core/html.
 */
add_filter(
	'wp_content_img_tag',
	function ( $tag, $context, $attachment_id ) {
		if ( $attachment_id ) {
			return $tag;
		}
		if ( strpos( $tag, get_stylesheet_directory_uri() ) === false ) {
			return $tag;
		}
		if ( strpos( $tag, 'hero-bg' ) !== false ) {
			return $tag;
		}
		if ( strpos( $tag, 'loading=' ) === false ) {
			$tag = str_replace( '<img ', '<img loading="lazy" ', $tag );
		}
		if ( strpos( $tag, 'decoding=' ) === false ) {
			$tag = str_replace( '<img ', '<img decoding="async" ', $tag );
		}
		return $tag;
	},
	10,
	3
);

/**
 * Defer em scripts com o data flag — evita render-blocking sem quebrar dependências.
 */
add_filter(
	'script_loader_tag',
	function ( $tag, $handle ) {
		if ( wp_scripts()->get_data( $handle, 'defer' ) ) {
			$tag = str_replace( ' src=', ' defer src=', $tag );
		}
		return $tag;
	},
	10,
	2
);

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

		$handle     = 'google_gtagjs';
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
 * CSS below-fold late (P0-3 Jetpack Forms + P0-5 tema cta).
 *
 * - P0-3: grunion.css + jetpack-forms-layout
 * - P0-5: iawp-cta (só newsletter #newsletter — sempre após o fold)
 *
 * Fora de propósito (above-fold / FOUC): iawp-style, iawp-hub, iawp-blog,
 * iawp-single, BHB CSS, wp-block-navigation.
 *
 * Media=print + onload restaura all; noscript = fallback.
 *
 * Bypass smoke:
 * - ?disable_iawp_jetpack_forms_css_late=1
 * - ?disable_iawp_cta_css_late=1
 */
add_filter(
	'style_loader_tag',
	static function ( $tag, $handle, $href, $_media ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Signature of style_loader_tag.
		if ( is_admin() ) {
			return $tag;
		}

		$late_handles = array();
		if ( ! isset( $_GET['disable_iawp_jetpack_forms_css_late'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$late_handles[] = 'grunion.css';
			$late_handles[] = 'jetpack-forms-layout';
		}
		if ( ! isset( $_GET['disable_iawp_cta_css_late'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$late_handles[] = 'iawp-cta';
		}

		if ( ! in_array( $handle, $late_handles, true ) ) {
			return $tag;
		}

		$updated = preg_replace(
			"/\smedia=(['\"])all\\1/",
			' media="print" onload="this.media=\'all\'"',
			$tag,
			1
		);
		if ( ! is_string( $updated ) ) {
			return $tag;
		}
		$tag = $updated;

		if ( is_string( $href ) && '' !== $href && false === strpos( $tag, '<noscript' ) ) {
			// Noscript fallback for print/onload late CSS (handle already enqueued).
			// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
			$tag .= sprintf(
				'<noscript><link rel="stylesheet" href="%s" media="all" /></noscript>' . "\n",
				esc_url( $href )
			);
			// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		}

		return $tag;
	},
	10,
	4
);

/**
 * P0-4 — Chrome critical path (fatia segura).
 *
 * Mapa (Home):
 * - wp-polyfill + wp-hooks + wp-i18n são deps clássicas de jp-forms-blocks
 *   (Jetpack Forms view.js). O filho já vinha com defer; as deps ficavam blocking.
 * - bhb-block-header-behavior (frontend.js) não tem deps WP — só faltava defer.
 *
 * Fora desta fatia (FOUC / risco de header-menu):
 * - BHB CSS (position:fixed do overlay — tema não duplica o fixed)
 * - core navigation/style.min.css (menu mobile/overlay do bloco)
 *
 * Bypass smoke: ?disable_iawp_chrome_defer=1
 */
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		if ( isset( $_GET['disable_iawp_chrome_defer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$defer_handles = array(
			'bhb-block-header-behavior',
			'wp-polyfill',
			'wp-hooks',
			'wp-i18n',
			'jp-forms-blocks',
		);

		foreach ( $defer_handles as $handle ) {
			if ( ! wp_script_is( $handle, 'registered' ) ) {
				continue;
			}
			// WP 6.3+ strategy (prod 7.1.x). Não forçar attr defer via
			// script_loader_tag em wp-i18n/hooks: quebra inline *-js-after
			// (ReferenceError: wp is not defined) e derruba Best Practices.
			wp_script_add_data( $handle, 'strategy', 'defer' );
		}
	},
	100
);
