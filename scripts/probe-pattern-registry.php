<?php
$theme = wp_get_theme();
if ( method_exists( $theme, 'delete_pattern_cache' ) ) {
	$theme->delete_pattern_cache();
}
$patterns = $theme->get_block_patterns();
echo 'get_block_patterns_count=' . count( $patterns ) . "\n";
foreach ( array_keys( $patterns ) as $file ) {
	echo "file=$file slug=" . ( $patterns[ $file ]['slug'] ?? '?' ) . "\n";
}
if ( function_exists( '_register_theme_block_patterns' ) ) {
	_register_theme_block_patterns();
}
$reg = WP_Block_Patterns_Registry::get_instance();
$all = $reg->get_all_registered();
$iawp = array_filter( array_keys( $all ), function ( $s ) {
	return str_starts_with( $s, 'iawp/' );
} );
sort( $iawp );
echo 'registry_iawp=' . implode( ',', $iawp ) . "\n";
echo 'registry_iawp_count=' . count( $iawp ) . "\n";
