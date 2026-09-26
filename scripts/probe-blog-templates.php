<?php
$theme = wp_get_theme();
if ( method_exists( $theme, 'delete_pattern_cache' ) ) {
	$theme->delete_pattern_cache();
	echo "cache_cleared\n";
}
$reg = WP_Block_Patterns_Registry::get_instance();
foreach ( array( 'iawp/blog-hero', 'iawp/blog-latest', 'iawp/query-cards-blog', 'iawp/newsletter' ) as $s ) {
	echo $s . '=' . ( $reg->is_registered( $s ) ? 'OK' : 'MISS' ) . "\n";
}

$q = new WP_Query(
	array(
		'post_type'      => 'wp_template',
		'post_status'    => 'any',
		'posts_per_page' => 50,
	)
);
echo "templates_count={$q->found_posts}\n";
foreach ( $q->posts as $p ) {
	$terms = wp_get_post_terms( $p->ID, 'wp_theme', array( 'fields' => 'names' ) );
	echo "T ID={$p->ID} status={$p->post_status} slug={$p->post_name} theme=" . implode( ',', $terms ) . " len=" . strlen( $p->post_content ) . "\n";
	if ( in_array( $p->post_name, array( 'index', 'home', 'front-page' ), true ) ) {
		echo "  has_blog_hero=" . ( str_contains( $p->post_content, 'blog-hero' ) ? 'yes' : 'no' ) . "\n";
		echo "  has_page_hero=" . ( str_contains( $p->post_content, 'page-hero' ) ? 'yes' : 'no' ) . "\n";
		echo "  has_blog-hero_class=" . ( str_contains( $p->post_content, 'blog-hero' ) ? 'yes' : 'no' ) . "\n";
		echo "  snippet=" . substr( preg_replace( '/\s+/', ' ', $p->post_content ), 0, 180 ) . "\n";
	}
}

$blog_id = (int) get_option( 'page_for_posts' );
echo "page_for_posts=$blog_id url=" . get_permalink( $blog_id ) . "\n";
echo "show_on_front=" . get_option( 'show_on_front' ) . "\n";
