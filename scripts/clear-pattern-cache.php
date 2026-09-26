<?php
$theme = wp_get_theme();
if ( method_exists( $theme, 'delete_pattern_cache' ) ) {
	$theme->delete_pattern_cache();
	echo "delete_pattern_cache ok\n";
}
wp_cache_flush();
echo "done\n";
