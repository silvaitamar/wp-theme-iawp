<?php
$file = get_stylesheet_directory() . '/parts/header.html';
$blocks = parse_blocks( file_get_contents( $file ) );
$btn = null;
$walk = function ( $nodes ) use ( &$walk, &$btn ) {
	foreach ( $nodes as $x ) {
		if ( ( $x['blockName'] ?? '' ) === 'core/button' ) {
			$btn = $x;
			return;
		}
		if ( ! empty( $x['innerBlocks'] ) ) {
			$walk( $x['innerBlocks'] );
		}
	}
};
$walk( $blocks );
$ser = serialize_block( $btn );
$rep = parse_blocks( $ser )[0];
$o = $btn['innerHTML'] ?? '';
$n = $rep['innerHTML'] ?? '';
echo 'identical=' . ( $o === $n ? 'yes' : 'no' ) . "\n";
echo 'o=' . preg_replace( '/\s+/', ' ', trim( $o ) ) . "\n";
echo 'n=' . preg_replace( '/\s+/', ' ', trim( $n ) ) . "\n";
echo 'attrs=' . wp_json_encode( $btn['attrs'] ) . "\n";
