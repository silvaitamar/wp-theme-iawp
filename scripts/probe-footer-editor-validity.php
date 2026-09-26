<?php
/**
 * Editor-side validity without browser login:
 * load footer template part from DB/theme and assert parse/serialize + markers.
 */
$slug = 'footer';
$theme = get_stylesheet();

$posts = get_posts(
	array(
		'post_type'      => 'wp_template_part',
		'name'           => $slug,
		'post_status'    => 'any',
		'posts_per_page' => 1,
	)
);

$content = '';
$source  = 'none';
if ( $posts ) {
	$content = $posts[0]->post_content;
	$source  = 'db:' . $posts[0]->ID;
} else {
	$file = get_stylesheet_directory() . '/parts/footer.html';
	$content = file_get_contents( $file );
	$source  = 'file';
}

$blocks = parse_blocks( $content );
$fails  = array();

$has_unexpected = false;
$walk = function ( $nodes ) use ( &$walk, &$fails ) {
	foreach ( $nodes as $b ) {
		if ( empty( $b['blockName'] ) ) {
			continue;
		}
		$cn = $b['attrs']['className'] ?? '';
		if ( preg_match( '/is-layout-|wp-container-|has-global-padding/', $cn ) ) {
			$fails[] = "layout_in_className:{$b['blockName']}";
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$walk( $b['innerBlocks'] );
		}
	}
};
$walk( $blocks );

foreach ( array( 'site-footer', 'footer-brand', 'footer-nav', 'footer-email', 'ola@iaewp.com.br' ) as $m ) {
	if ( ! str_contains( $content, $m ) ) {
		$fails[] = "missing:{$m}";
	}
}

// Render smoke (no theme switch)
$rendered = do_blocks( $content );
if ( ! str_contains( $rendered, 'site-footer' ) ) {
	$fails[] = 'render_missing_site_footer';
}
if ( ! str_contains( $rendered, 'footer-brand' ) ) {
	$fails[] = 'render_missing_brand';
}

echo "source={$source}\n";
echo 'content_len=' . strlen( $content ) . "\n";
echo 'rendered_len=' . strlen( $rendered ) . "\n";
echo 'fails=' . count( $fails ) . "\n";
foreach ( $fails as $f ) {
	echo "FAIL {$f}\n";
}
echo 'ok=' . ( count( $fails ) === 0 ? 'yes' : 'no' ) . "\n";
