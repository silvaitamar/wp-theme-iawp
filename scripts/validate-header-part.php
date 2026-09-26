<?php
/**
 * Roundtrip serialize header button / part for invalid markup.
 */
$file = get_stylesheet_directory() . '/parts/header.html';
$html = file_get_contents( $file );
$blocks = parse_blocks( $html );

$fails = array();
$walk = function ( $nodes, $path = '' ) use ( &$walk, &$fails ) {
	foreach ( $nodes as $i => $b ) {
		$name = $b['blockName'] ?? '';
		$p = $path . '/' . $i . ':' . ( $name ?: 'null' );
		if ( ! $name ) {
			continue;
		}
		$ser = serialize_block( $b );
		$rep = parse_blocks( $ser )[0] ?? null;
		if ( ! $rep || ( $rep['blockName'] ?? '' ) !== $name ) {
			$fails[] = "REPARSE_FAIL $p";
		} elseif ( in_array( $name, array( 'core/button', 'core/buttons', 'core/group', 'core/paragraph', 'core/navigation' ), true ) ) {
			$o = preg_replace( '/\s+/', ' ', trim( $b['innerHTML'] ?? '' ) );
			$n = preg_replace( '/\s+/', ' ', trim( $rep['innerHTML'] ?? '' ) );
			similar_text( $o, $n, $pct );
			if ( $pct < 99.0 ) {
				$fails[] = sprintf( 'SAVE_MISMATCH %s similar=%.2f%%', $p, $pct );
				if ( $name === 'core/button' ) {
					$fails[] = '  o=' . substr( $o, 0, 160 );
					$fails[] = '  n=' . substr( $n, 0, 160 );
				}
			}
		}
		// preset duplicate: has-*-color class AND color:var(--wp--preset in style
		$raw = $b['innerHTML'] ?? '';
		if ( preg_match( '/has-[\w-]+-color[^>]*(?:style="[^"]*color\s*:\s*var\(--wp--preset--color)/', $raw ) ) {
			$fails[] = "PRESET_DUP_COLOR $p";
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$walk( $b['innerBlocks'], $p );
		}
	}
};
$walk( $blocks );
echo 'fails=' . count( $fails ) . "\n";
foreach ( $fails as $f ) {
	echo "$f\n";
}
echo 'ok=' . ( count( $fails ) === 0 ? 'yes' : 'no' ) . "\n";
