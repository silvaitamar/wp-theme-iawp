<?php
/**
 * Prove blog index template resolves + blog.css has zero !important.
 */
$theme = get_stylesheet_directory();

$blog_css = file_get_contents( $theme . '/assets/blocks/blog.css' );
$imp = preg_match_all( '/!important\s*;/', $blog_css );
echo "blog_css_important=$imp\n";

$file = $theme . '/templates/index.html';
$html = file_get_contents( $file );
$blocks = parse_blocks( $html );
if ( function_exists( 'resolve_pattern_blocks' ) ) {
	$blocks = resolve_pattern_blocks( $blocks );
}
$serialized = serialize_blocks( $blocks );

$marks = array(
	'page-hero'      => 'page-hero',
	'Artigos'        => 'Artigos',
	'section__label' => 'Leitura recomendada',
	'posts-hub'      => 'posts-hub',
	'post-featured'  => 'post-featured',
	'post-card'      => 'post-card',
	'newsletter'     => 'id="newsletter"',
);
echo 'resolved_len=' . strlen( $serialized ) . "\n";
foreach ( $marks as $k => $needle ) {
	echo 'mark_' . $k . '=' . ( str_contains( $serialized, $needle ) ? 'OK' : 'FAIL' ) . "\n";
}
if ( preg_match_all( '/wp:pattern\s*\{"slug":"([^"]+)"\}/', $serialized, $m ) ) {
	echo 'UNRESOLVED=' . implode( ',', array_unique( $m[1] ) ) . "\n";
} else {
	echo "UNRESOLVED=none\n";
}

$reg = WP_Block_Patterns_Registry::get_instance();
foreach ( array( 'iawp/blog-hero', 'iawp/blog-latest', 'iawp/query-cards-blog' ) as $slug ) {
	echo 'reg_' . str_replace( '/', '_', $slug ) . '=' . ( $reg->is_registered( $slug ) ? 'OK' : 'FAIL' ) . "\n";
}

$blog_id = (int) get_option( 'page_for_posts' );
echo 'page_for_posts=' . $blog_id . "\n";
if ( $blog_id ) {
	echo 'blog_url=' . get_permalink( $blog_id ) . "\n";
}
