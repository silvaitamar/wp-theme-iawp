<?php
/**
 * List wp_template overrides for front-page / iawp.
 */
$q = new WP_Query(
	array(
		'post_type'      => 'wp_template',
		'post_status'    => 'any',
		'posts_per_page' => 20,
		's'              => 'front-page',
	)
);
echo "count=" . $q->found_posts . "\n";
foreach ( $q->posts as $p ) {
	echo "ID={$p->ID} status={$p->post_status} slug={$p->post_name} title={$p->post_title} modified={$p->post_modified}\n";
	$content = $p->post_content;
	$has_invalid_hint = str_contains( $content, 'about-visual' );
	echo "  len=" . strlen( $content ) . " has_about_visual=" . ( $has_invalid_hint ? 'yes' : 'no' ) . "\n";
	if ( preg_match( '/wp:image[^\n]+/', $content, $m ) ) {
		echo "  image_comment=" . substr( $m[0], 0, 240 ) . "\n";
	}
}
// Also theme templates from filesystem are used when no custom.
$theme = wp_get_theme();
echo "stylesheet=" . $theme->get_stylesheet() . "\n";
