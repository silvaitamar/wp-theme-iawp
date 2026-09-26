<?php
/**
 * Prove patterns registered + front-page expands to hub landmarks.
 */
$theme = wp_get_theme();
if ( method_exists( $theme, 'delete_pattern_cache' ) ) {
	$theme->delete_pattern_cache();
}
wp_cache_flush();

$needed = array(
	'iawp/post-featured',
	'iawp/post-card',
	'iawp/bento-item',
	'iawp/section-header',
	'iawp/section-header-lede',
	'iawp/query-featured',
	'iawp/query-cards',
	'iawp/query-cards-blog',
	'iawp/posts-hub',
	'iawp/bento-grid',
	'iawp/about-teaser',
	'iawp/home-hero',
	'iawp/home-latest',
	'iawp/home-eixos',
	'iawp/home-about',
	'iawp/blog-hero',
	'iawp/blog-latest',
	'iawp/newsletter',
);

$reg = WP_Block_Patterns_Registry::get_instance();
$missing = array();
foreach ( $needed as $slug ) {
	if ( ! $reg->is_registered( $slug ) ) {
		$missing[] = $slug;
	}
}
echo 'patterns_registered=' . ( count( $needed ) - count( $missing ) ) . '/' . count( $needed ) . "\n";
if ( $missing ) {
	echo 'MISSING=' . implode( ',', $missing ) . "\n";
}

// Template override?
$q = new WP_Query(
	array(
		'post_type'      => 'wp_template',
		'post_status'    => array( 'publish', 'draft', 'auto-draft' ),
		'posts_per_page' => 20,
		'name'           => 'front-page',
		'tax_query'      => array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => get_stylesheet(),
			),
		),
	)
);
echo 'front_page_override_count=' . $q->found_posts . "\n";
foreach ( $q->posts as $p ) {
	$has_pattern = str_contains( $p->post_content, 'wp:pattern' );
	$has_hero_inline = str_contains( $p->post_content, 'class="hero' ) || str_contains( $p->post_content, 'className":"hero' );
	echo "override ID={$p->ID} status={$p->post_status} len=" . strlen( $p->post_content ) . " has_wp_pattern=" . ( $has_pattern ? 'yes' : 'no' ) . " has_hero_inline=" . ( $has_hero_inline ? 'yes' : 'no' ) . "\n";
}

// Resolve file template like core does
$file = get_stylesheet_directory() . '/templates/front-page.html';
$html = file_get_contents( $file );
$blocks = parse_blocks( $html );
if ( function_exists( 'resolve_pattern_blocks' ) ) {
	$blocks = resolve_pattern_blocks( $blocks );
}
$serialized = serialize_blocks( $blocks );

$marks = array(
	'hero'           => 'class="wp-block-group alignfull hero',
	'latest'         => 'aria-label="Últimos artigos"',
	'posts-hub'      => 'posts-hub',
	'post-featured'  => 'post-featured',
	'post-card'      => 'post-card',
	'eixos'          => 'aria-label="Eixos do hub"',
	'bento-grid'     => 'bento-grid',
	'bento-item'     => 'bento-item',
	'about'          => 'aria-label="Sobre"',
	'about-cols'     => 'about-cols',
	'newsletter'     => 'id="newsletter"',
);
echo "resolved_len=" . strlen( $serialized ) . "\n";
foreach ( $marks as $k => $needle ) {
	$ok = str_contains( $serialized, $needle ) ? 'OK' : 'FAIL';
	echo "mark_$k=$ok\n";
}

// Residual unresolved pattern refs in resolved tree
if ( preg_match_all( '/wp:pattern\s*\{"slug":"([^"]+)"\}/', $serialized, $m ) ) {
	echo 'UNRESOLVED_PATTERNS=' . implode( ',', array_unique( $m[1] ) ) . "\n";
} else {
	echo "UNRESOLVED_PATTERNS=none\n";
}
