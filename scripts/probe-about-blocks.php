<?php
/**
 * Compare each about-section child: file innerHTML vs serialize_block reparse.
 */
$theme = get_stylesheet_directory();
$html  = file_get_contents( $theme . '/templates/front-page.html' );
$blocks = parse_blocks( $html );

function find_about( array $blocks ): ?array {
	foreach ( $blocks as $b ) {
		if ( ( $b['blockName'] ?? '' ) === 'core/group' ) {
			$cn = $b['attrs']['className'] ?? '';
			if ( str_contains( $cn, 'about-section' ) ) {
				return $b;
			}
			$inner = find_about( $b['innerBlocks'] ?? array() );
			if ( $inner ) {
				return $inner;
			}
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$inner = find_about( $b['innerBlocks'] );
			if ( $inner ) {
				return $inner;
			}
		}
	}
	return null;
}

function walk( array $block, string $path ): void {
	$name = $block['blockName'] ?? 'null';
	$cn   = $block['attrs']['className'] ?? '';
	$p    = $path . '/' . $name . ( $cn ? ".$cn" : '' );

	if ( $name ) {
		$ser  = serialize_block( $block );
		$rep  = parse_blocks( $ser )[0] ?? null;
		$o    = preg_replace( '/\s+/', ' ', trim( $block['innerHTML'] ?? '' ) );
		$n    = preg_replace( '/\s+/', ' ', trim( $rep['innerHTML'] ?? '' ) );
		$ok   = ( $o === $n );
		if ( ! $ok || in_array( $name, array( 'core/image', 'core/group', 'core/button', 'core/buttons', 'core/heading', 'core/paragraph' ), true ) ) {
			echo ( $ok ? 'OK  ' : 'DIFF' ) . " $p\n";
			if ( ! $ok ) {
				echo "  FILE: " . substr( $o, 0, 220 ) . "\n";
				echo "  SAVE: " . substr( $n, 0, 220 ) . "\n";
			}
		}
		// Forbidden classes
		if ( preg_match( '/\b(is-layout-|has-global-padding|wp-container-)\b/', $o ) ) {
			echo "FORBIDDEN $p\n";
		}
	}
	foreach ( $block['innerBlocks'] ?? array() as $i => $child ) {
		walk( $child, $p . "[$i]" );
	}
}

$about = find_about( $blocks );
if ( ! $about ) {
	fwrite( STDERR, "about-section not found\n" );
	exit( 1 );
}
echo "FOUND about-section\n";
walk( $about, 'about' );
