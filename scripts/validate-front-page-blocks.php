<?php
/**
 * Validate front-page template blocks with core parse_blocks + serialize roundtrip.
 */
$theme = get_stylesheet_directory();
$file  = $theme . '/templates/front-page.html';
$html  = file_get_contents( $file );
if ( false === $html ) {
	fwrite( STDERR, "Cannot read $file\n" );
	exit( 1 );
}

function iawp_validate_tree( array $blocks, string $path = '' ): array {
	$issues = array();
	foreach ( $blocks as $i => $block ) {
		$name = $block['blockName'] ?? '(null)';
		$p    = $path . '/' . $i . ':' . ( $name ?: 'freeform' );

		if ( empty( $block['blockName'] ) ) {
			$inner = trim( $block['innerHTML'] ?? '' );
			if ( $inner !== '' && ! preg_match( '/^\s*$/u', $inner ) ) {
				// Freeform/HTML leftover between blocks — often causes parent invalid.
				$preview = preg_replace( '/\s+/', ' ', substr( $inner, 0, 120 ) );
				$issues[] = "FREEFORM at $p :: $preview";
			}
			continue;
		}

		// Roundtrip: attrs + innerBlocks → serialize → parse → compare outer HTML loosely.
		$serialized = serialize_block( $block );
		$reparsed   = parse_blocks( $serialized );
		$again      = $reparsed[0] ?? null;
		if ( ! $again || ( $again['blockName'] ?? null ) !== $block['blockName'] ) {
			$issues[] = "REPARSE_FAIL at $p";
		} else {
			// Compare innerHTML normalized (layout classes injected at render differ —
			// compare static saved content from original vs what save would emit for attrs).
			$orig = preg_replace( '/\s+/', ' ', trim( $block['innerHTML'] ?? '' ) );
			$new  = preg_replace( '/\s+/', ' ', trim( $again['innerHTML'] ?? '' ) );
			// Only flag if outer wrapper tag differs wildly for groups.
			if ( $name === 'core/group' ) {
				preg_match( '/^<(\w+)/', $block['innerHTML'] ?? '', $om );
				preg_match( '/^<(\w+)/', $again['innerHTML'] ?? '', $nm );
				// innerHTML for group is content INSIDE the wrapper, not the wrapper itself.
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$issues = array_merge( $issues, iawp_validate_tree( $block['innerBlocks'], $p ) );
		}
	}
	return $issues;
}

$blocks = parse_blocks( $html );
$issues = iawp_validate_tree( $blocks );

// Top-level map
echo "TOP_LEVEL:\n";
foreach ( $blocks as $i => $b ) {
	$n = $b['blockName'] ?? 'FREEFORM/null';
	$c = count( $b['innerBlocks'] ?? array() );
	$h = substr( preg_replace( '/\s+/', ' ', trim( $b['innerHTML'] ?? '' ) ), 0, 80 );
	echo "  [$i] $n innerBlocks=$c html=$h\n";
}

echo "\nISSUES (" . count( $issues ) . "):\n";
foreach ( $issues as $issue ) {
	echo "  - $issue\n";
}

// Specifically inspect main
foreach ( $blocks as $b ) {
	if ( ( $b['blockName'] ?? '' ) === 'core/group' && ( $b['attrs']['anchor'] ?? '' ) === 'main-content' ) {
		echo "\nMAIN children:\n";
		foreach ( $b['innerBlocks'] as $j => $child ) {
			$n = $child['blockName'] ?? 'FREEFORM';
			$cls = $child['attrs']['className'] ?? '';
			$tag = $child['attrs']['tagName'] ?? '';
			echo "  [$j] $n tag=$tag class=$cls\n";
		}
		// freeform siblings inside main raw
		$raw_inner = $b['innerContent'] ?? array();
		foreach ( $raw_inner as $k => $piece ) {
			if ( is_string( $piece ) && trim( $piece ) !== '' ) {
				$preview = substr( preg_replace( '/\s+/', ' ', trim( $piece ) ), 0, 100 );
				echo "  innerContent[$k] STRING: $preview\n";
			}
		}
	}
}

exit( count( $issues ) > 0 ? 2 : 0 );
