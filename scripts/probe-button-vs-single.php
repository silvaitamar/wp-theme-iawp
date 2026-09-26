<?php
/**
 * Prove newsletter submit button uses minimal valid Jetpack/TT markup.
 */
$theme = get_stylesheet_directory();
ob_start();
include $theme . '/patterns/newsletter.php';
$news = (string) ob_get_clean();
$single = file_get_contents( $theme . '/templates/single.html' );

function iawp_find_submit_button( string $html ): ?array {
	$blocks = parse_blocks( $html );
	$stack  = $blocks;
	while ( $stack ) {
		$b = array_shift( $stack );
		if ( ( $b['blockName'] ?? '' ) === 'core/button' ) {
			$attrs = $b['attrs'] ?? array();
			if ( ( $attrs['type'] ?? '' ) === 'submit' || str_contains( $b['innerHTML'] ?? '', 'type="submit"' ) ) {
				return $b;
			}
		}
		foreach ( $b['innerBlocks'] ?? array() as $inner ) {
			$stack[] = $inner;
		}
	}
	return null;
}

$nb = iawp_find_submit_button( $news );
$sb = iawp_find_submit_button( $single );
if ( ! $nb || ! $sb ) {
	fwrite( STDERR, 'button block not found news=' . ( $nb ? 'y' : 'n' ) . ' single=' . ( $sb ? 'y' : 'n' ) . "\n" );
	exit( 1 );
}

$n_attrs = $nb['attrs'] ?? array();
$n_html  = trim( $nb['innerHTML'] ?? '' );
$s_html  = trim( $sb['innerHTML'] ?? '' );
$n_struct = preg_replace( '/>[^<]+</', '>●<', $n_html );
$s_struct = preg_replace( '/>[^<]+</', '>●<', $s_html );

echo 'news_attrs=' . wp_json_encode( $n_attrs ) . "\n";
echo 'news_html=' . $n_html . "\n";
echo 'struct_match_single=' . ( $n_struct === $s_struct ? 'yes' : 'no' ) . "\n";

$issues = 0;
if ( ( $n_attrs['tagName'] ?? '' ) !== 'button' || ( $n_attrs['type'] ?? '' ) !== 'submit' ) {
	echo "BAD tagName/type\n";
	++$issues;
}
if ( ! empty( $n_attrs['style'] ) || ! empty( $n_attrs['textColor'] ) || ! empty( $n_attrs['backgroundColor'] ) ) {
	echo "BAD style/color attrs on submit button (invalidates save)\n";
	++$issues;
}
if ( ! str_contains( $n_html, 'wp-element-button' ) || str_contains( $n_html, 'style=' ) ) {
	echo "BAD innerHTML has style or missing wp-element-button\n";
	++$issues;
}
if ( $n_struct !== $s_struct ) {
	echo "WARN struct differs from single (label text ok if structure same)\n";
	echo "news_struct=$n_struct\n";
	echo "single_struct=$s_struct\n";
	++$issues;
}
echo "ISSUES ($issues)\n";
exit( $issues > 0 ? 1 : 0 );
