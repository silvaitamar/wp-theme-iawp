<?php
/**
 * Strict serialization checks for parts/footer.html (TT5 / no layout classes).
 */
$file = get_stylesheet_directory() . '/parts/footer.html';
$html = file_get_contents( $file );
$blocks = parse_blocks( $html );

$fails = array();
$walk = function ( $nodes ) use ( &$walk, &$fails ) {
	foreach ( $nodes as $b ) {
		if ( empty( $b['blockName'] ) ) {
			continue;
		}
		$attrs = $b['attrs'] ?? array();
		$cn = $attrs['className'] ?? '';
		if ( preg_match( '/is-layout-|wp-container-|has-global-padding|is-content-justification/', $cn ) ) {
			$fails[] = "className_layout:{$b['blockName']}:{$cn}";
		}
		$inner = $b['innerHTML'] ?? '';
		if ( preg_match( '/\sgap\s*:/', $inner ) ) {
			$fails[] = "gap_in_html:{$b['blockName']}";
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$walk( $b['innerBlocks'] );
		}
	}
};
$walk( $blocks );

// Round-trip: parse → serialize should keep key markers
$serialized = serialize_blocks( $blocks );
$markers = array( 'site-footer', 'footer-brand', 'footer-nav', 'footer-email', 'backgroundColor":"base"' );
foreach ( $markers as $m ) {
	if ( ! str_contains( $html, $m ) ) {
		$fails[] = "file_missing:{$m}";
	}
	if ( ! str_contains( $serialized, $m ) && $m !== 'backgroundColor":"base"' ) {
		// serialize may reorder JSON; check looser
		if ( $m === 'backgroundColor":"base"' && ! str_contains( $serialized, 'base' ) ) {
			$fails[] = "serialize_missing_base";
		} elseif ( $m !== 'backgroundColor":"base"' ) {
			$fails[] = "serialize_missing:{$m}";
		}
	}
}

// No border attr (must stay CSS for sibling rule)
if ( preg_match( '/"border"\s*:/', $html ) ) {
	$fails[] = 'border_attr_present_use_css_sibling';
}

// !important in footer CSS properties (not comments)
$css = file_get_contents( get_stylesheet_directory() . '/style.css' );
if ( preg_match( '/FOOTER — allowlist[\s\S]*?(?=\n\/\* =+)/', $css, $m ) ) {
	$block = $m[0];
	if ( preg_match( '/!important\s*;/', $block ) ) {
		$fails[] = 'footer_css_has_important_decl';
	}
}

echo 'strict_fails=' . count( $fails ) . "\n";
foreach ( $fails as $f ) {
	echo "FAIL {$f}\n";
}
echo 'block_top=' . ( $blocks[0]['blockName'] ?? 'none' ) . "\n";
echo 'ok=' . ( count( $fails ) === 0 ? 'yes' : 'no' ) . "\n";
