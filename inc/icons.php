<?php
/**
 * SVG icons: Icons API collection (`iawp/*`) for core/icon.
 *
 * Onda 4: inliner legado span→SVG removido — patterns/BD usam core/icon.
 * Mantém grain + #grad-brand no body (sociais / stroke CSS).
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

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
 * Format a float for SVG path attributes.
 *
 * @param float $n Número para atributo d do path.
 */
function iawp_svg_num( float $n ): string {
	$s = rtrim( rtrim( sprintf( '%.4f', $n ), '0' ), '.' );
	return '' === $s || '-' === $s ? '0' : $s;
}

/**
 * Registra a biblioteca Lucide do tema na Icons API (WP 7.1+ / core/icon).
 *
 * Cada arquivo em assets/icons/{slug}.svg vira `iawp/{slug}`.
 *
 * Armadilha: sanitize_icon_content do core só permite svg/path/polygon e
 * remove stroke* — por isso circle→path aqui e stroke via CSS (.bento-icon).
 */
add_action(
	'init',
	function (): void {
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
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme SVG.
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
	},
	11
);

/**
 * Injeta grain + <svg> hidden com <defs> do gradiente brand no body_open.
 * Emitido uma vez — sociais e CSS podem referenciar url(#grad-brand).
 */
add_action(
	'wp_body_open',
	function (): void {
		echo '<div class="site-grain" aria-hidden="true"></div>';
		echo '<svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">' .
			'<defs><linearGradient id="grad-brand" x1="0" y1="0" x2="1" y2="1">' .
			'<stop offset="0%" stop-color="#3B82F6"/>' .
			'<stop offset="100%" stop-color="#8B5CF6"/>' .
			'</linearGradient></defs>' .
			'</svg>';
	}
);
