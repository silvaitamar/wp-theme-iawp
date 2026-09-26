<?php
/**
 * Stricter: compare file HTML of each core/group to serialize_block() output.
 * Gutenberg invalidation ≈ saved markup ≠ save() markup.
 */
$theme = get_stylesheet_directory();
$file  = $theme . '/templates/front-page.html';
$html  = file_get_contents( $file );

function iawp_norm( string $s ): string {
	$s = preg_replace( '/\s+/', ' ', $s );
	return trim( $s );
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

		// Forbidden render-only classes in saved HTML
		$raw = ( $block['innerHTML'] ?? '' ) . ( $block['innerContent'] ? implode( '', array_filter( $block['innerContent'], 'is_string' ) ) : '' );
		if ( preg_match( '/\b(is-layout-|wp-block-group-is-layout-|has-global-padding|is-vertical|is-horizontal|is-nowrap|is-content-justification-|wp-container-)\b/', $raw, $m ) ) {
			$issues[] = "FORBIDDEN_CLASS {$m[1]} at $p";
		}

		$ser = serialize_block( $block );
		$rep = parse_blocks( $ser )[0] ?? null;
		if ( ! $rep || ( $rep['blockName'] ?? '' ) !== $name ) {
			$issues[] = "REPARSE_FAIL $p";
		} else {
			$o = iawp_norm( $block['innerHTML'] ?? '' );
			$n = iawp_norm( $rep['innerHTML'] ?? '' );
			if ( $o !== $n && in_array( $name, array( 'core/group', 'core/buttons', 'core/button' ), true ) ) {
				// Diff length / first mismatch — often means invalid in editor
				similar_text( $o, $n, $pct );
				if ( $pct < 99.5 ) {
					$issues[] = sprintf(
						"SAVE_MISMATCH %s similar=%.2f%% o_len=%d n_len=%d o0=%.80s n0=%.80s",
						$p,
						$pct,
						strlen( $o ),
						strlen( $n ),
						$o,
						$n
					);
				}
			}
		}

		// innerContent string pieces must open/close matching tags for groups
		if ( $name === 'core/group' && ! empty( $block['innerContent'] ) ) {
			$first = null;
			$last  = null;
			foreach ( $block['innerContent'] as $piece ) {
				if ( is_string( $piece ) && trim( $piece ) !== '' ) {
					if ( $first === null ) {
						$first = trim( $piece );
					}
					$last = trim( $piece );
				}
			}
			if ( $first && preg_match( '/^<(\w+)/', $first, $om ) ) {
				$tag = $om[1];
				if ( $last && ! preg_match( '/<\/' . $tag . '>\s*$/', $last ) && $last !== $first ) {
					// self-contained single piece OK; multi-piece must close
					if ( count( array_filter( $block['innerContent'], 'is_string' ) ) > 1 ) {
						$issues[] = "UNCLOSED_WRAPPER <$tag> at $p last=" . substr( iawp_norm( $last ), 0, 60 );
					}
				}
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$issues = array_merge( $issues, iawp_check( $block['innerBlocks'], $p ) );
		}
	}
	return $issues;
}

$blocks = parse_blocks( $html );
$issues = iawp_check( $blocks );

// Full-file serialize roundtrip on main only
foreach ( $blocks as $b ) {
	if ( ( $b['attrs']['anchor'] ?? '' ) === 'main-content' ) {
		$ser  = serialize_block( $b );
		$rep  = parse_blocks( $ser )[0];
		$c1   = count( $b['innerBlocks'] );
		$c2   = count( $rep['innerBlocks'] ?? array() );
		echo "MAIN roundtrip children: orig=$c1 reparse=$c2\n";
		if ( $c1 !== $c2 ) {
			$issues[] = "MAIN_CHILD_COUNT orig=$c1 reparse=$c2";
		}
		foreach ( $rep['innerBlocks'] as $j => $child ) {
			echo '  reparse[' . $j . '] ' . ( $child['blockName'] ?? 'null' ) . ' class=' . ( $child['attrs']['className'] ?? '' ) . "\n";
		}
	}
}

echo "\nSTRICT ISSUES (" . count( $issues ) . "):\n";
foreach ( $issues as $issue ) {
	echo "  - $issue\n";
}
exit( count( $issues ) > 0 ? 2 : 0 );
