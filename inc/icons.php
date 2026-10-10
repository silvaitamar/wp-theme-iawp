<?php
/**
 * SVG icons: core/icon collection + legacy span→SVG inliner.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

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
