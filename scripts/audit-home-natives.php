<?php
/**
 * Audit home/parts: !important buckets + native attr smoke on header/footer parts.
 */
$theme = get_stylesheet_directory();

function iawp_important_decls( string $css ): int {
	return preg_match_all( '/!important\s*;/', $css );
}

function iawp_slice_between( string $css, string $start, string $end ): string {
	$i = strpos( $css, $start );
	if ( $i === false ) {
		return '';
	}
	$j = strpos( $css, $end, $i + strlen( $start ) );
	if ( $j === false ) {
		return substr( $css, $i );
	}
	return substr( $css, $i, $j - $i );
}

$style = file_get_contents( $theme . '/style.css' );
$hub   = file_get_contents( $theme . '/assets/blocks/hub.css' );
$cta   = file_get_contents( $theme . '/assets/blocks/cta.css' );
$ed    = file_get_contents( $theme . '/assets/editor-canvas.css' );

$header_css = iawp_slice_between( $style, 'HEADER — BHB', 'FOOTER — allowlist' );
$footer_css = iawp_slice_between( $style, 'FOOTER — allowlist', 'REVEAL ANIMATIONS' );

echo "important_header_block=" . iawp_important_decls( $header_css ) . "\n";
echo "important_footer_block=" . iawp_important_decls( $footer_css ) . "\n";
echo "important_hub=" . iawp_important_decls( $hub ) . "\n";
echo "important_cta=" . iawp_important_decls( $cta ) . "\n";
echo "important_editor_canvas=" . iawp_important_decls( $ed ) . "\n";

// hub: only count real decls outside comments for about (should be 0)
if ( preg_match_all( '/\.about-[\s\S]*?\{[^}]*!important/', $hub, $m ) ) {
	echo "important_about_rules=" . count( $m[0] ) . "\n";
} else {
	echo "important_about_rules=0\n";
}

$header = file_get_contents( $theme . '/parts/header.html' );
$footer = file_get_contents( $theme . '/parts/footer.html' );
$front  = file_get_contents( $theme . '/templates/front-page.html' );

$checks = array(
	'header_nav_muted'     => str_contains( $header, '"textColor":"muted"' ),
	'header_nav_xs'        => str_contains( $header, '"fontSize":"xs"' ),
	'header_logo_display'  => str_contains( $header, '"fontFamily":"display"' ),
	'header_btn_natives'   => str_contains( $header, '"fontWeight":"800"' ) && str_contains( $header, 'btn-nav' ),
	'footer_bg_base'       => str_contains( $footer, '"backgroundColor":"base"' ),
	'front_has_newsletter' => str_contains( $front, 'iawp/newsletter' ),
	'front_no_pattern_hero'=> ! str_contains( $front, 'iawp/home-hero' ),
	'header_no_is_layout'  => ! preg_match( '/is-layout-|wp-container-/', $header ),
	'footer_no_is_layout'  => ! preg_match( '/is-layout-|wp-container-/', $footer ),
);
foreach ( $checks as $k => $ok ) {
	echo "check_{$k}=" . ( $ok ? 'ok' : 'FAIL' ) . "\n";
}

$patterns = glob( $theme . '/patterns/*.php' );
echo 'patterns_count=' . count( $patterns ) . "\n";
foreach ( $patterns as $p ) {
	echo 'pattern=' . basename( $p ) . "\n";
}
