<?php
/**
 * STRICT validate: each pattern file + resolved front-page tree.
 */
function iawp_norm( string $s ): string {
	return trim( preg_replace( '/\s+/', ' ', $s ) );
}

function iawp_check( array $blocks, string $path = '' ): array {
	$issues = array();
	foreach ( $blocks as $i => $block ) {
		$name = $block['blockName'] ?? null;
		$p    = $path . '/' . $i . ':' . ( $name ?: 'freeform' );

		if ( ! $name ) {
			$inner = trim( $block['innerHTML'] ?? '' );
			if ( $inner !== '' ) {
				$issues[] = "FREEFORM_NONEMPTY $p :: " . substr( iawp_norm( $inner ), 0, 100 );
			}
			continue;
		}

		$raw = ( $block['innerHTML'] ?? '' ) . ( $block['innerContent'] ? implode( '', array_filter( $block['innerContent'], 'is_string' ) ) : '' );
		if ( preg_match( '/\b(is-layout-|wp-block-group-is-layout-|has-global-padding|is-vertical|is-horizontal|is-nowrap|is-content-justification-|wp-container-)\b/', $raw, $m ) ) {
			$issues[] = "FORBIDDEN_CLASS {$m[1]} at $p";
		}

		$ser = serialize_block( $block );
		$rep = parse_blocks( $ser )[0] ?? null;
		if ( ! $rep || ( $rep['blockName'] ?? '' ) !== $name ) {
			$issues[] = "REPARSE_FAIL $p";
		} elseif ( in_array( $name, array( 'core/group', 'core/buttons', 'core/button' ), true ) ) {
			$o = iawp_norm( $block['innerHTML'] ?? '' );
			$n = iawp_norm( $rep['innerHTML'] ?? '' );
			if ( $o !== $n ) {
				similar_text( $o, $n, $pct );
				if ( $pct < 99.5 ) {
					$issues[] = sprintf( 'SAVE_MISMATCH %s similar=%.2f%%', $p, $pct );
				}
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$issues = array_merge( $issues, iawp_check( $block['innerBlocks'], $p ) );
		}
	}
	return $issues;
}

function iawp_strip_php_header( string $php ): string {
	if ( preg_match( '/\?>\s*(.*)$/s', $php, $m ) ) {
		return $m[1];
	}
	return $php;
}

$theme = get_stylesheet_directory();
$files = glob( $theme . '/patterns/*.php' );
$total = 0;
$fail  = 0;
foreach ( $files as $file ) {
	$base = basename( $file );
	$html = iawp_strip_php_header( file_get_contents( $file ) );
	$issues = iawp_check( parse_blocks( $html ), $base );
	$total++;
	if ( $issues ) {
		$fail++;
		echo "FAIL $base\n";
		foreach ( $issues as $iss ) {
			echo "  $iss\n";
		}
	} else {
		echo "OK $base\n";
	}
}

$front = file_get_contents( $theme . '/templates/front-page.html' );
$blocks = parse_blocks( $front );
if ( function_exists( 'resolve_pattern_blocks' ) ) {
	$blocks = resolve_pattern_blocks( $blocks );
}
$front_issues = iawp_check( $blocks, 'front-page-resolved' );
if ( $front_issues ) {
	$fail++;
	echo "FAIL front-page-resolved\n";
	foreach ( $front_issues as $iss ) {
		echo "  $iss\n";
	}
} else {
	echo "OK front-page-resolved\n";
}

echo 'summary patterns=' . $total . ' fails=' . $fail . "\n";
exit( $fail ? 1 : 0 );
