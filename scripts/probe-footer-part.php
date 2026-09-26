<?php
/**
 * Footer part: file vs DB override + serialization smoke.
 */
$theme = wp_get_theme();
echo "stylesheet=" . $theme->get_stylesheet() . "\n";

$file = get_stylesheet_directory() . '/parts/footer.html';
echo "file_exists=" . ( file_exists( $file ) ? 'yes' : 'no' ) . "\n";
$file_html = file_exists( $file ) ? file_get_contents( $file ) : '';
echo "file_len=" . strlen( $file_html ) . "\n";

$checks = array(
	'site-footer'           => str_contains( $file_html, 'site-footer' ),
	'backgroundColor_base'  => str_contains( $file_html, '"backgroundColor":"base"' ),
	'border_outline'        => str_contains( $file_html, 'outline' ),
	'spacing_50'            => str_contains( $file_html, 'spacing|50' ),
	'footer-brand'          => str_contains( $file_html, 'footer-brand' ),
	'footer-nav'            => str_contains( $file_html, 'footer-nav' ),
	'footer-email'          => str_contains( $file_html, 'footer-email' ),
	'no_is_layout'          => ! preg_match( '/is-layout-|wp-container-|has-global-padding/', $file_html ),
	'no_gap_in_style_attr'  => ! preg_match( '/style="[^"]*gap:/', $file_html ),
);
foreach ( $checks as $k => $ok ) {
	echo "check_{$k}=" . ( $ok ? 'ok' : 'FAIL' ) . "\n";
}

$q = new WP_Query(
	array(
		'post_type'      => 'wp_template_part',
		'post_status'    => 'any',
		'posts_per_page' => 20,
		's'              => 'footer',
	)
);
echo "override_count=" . $q->found_posts . "\n";
foreach ( $q->posts as $p ) {
	echo "ID={$p->ID} status={$p->post_status} slug={$p->post_name} title={$p->post_title} modified={$p->post_modified}\n";
	echo "  len=" . strlen( $p->post_content ) . "\n";
	if ( preg_match( '/is-layout-|wp-container-|className":"[^"]*is-layout/', $p->post_content ) ) {
		echo "  WARN=layout_classes_in_override\n";
	}
}

// !important audit in footer CSS block of style.css
$css = file_get_contents( get_stylesheet_directory() . '/style.css' );
if ( preg_match( '/FOOTER — allowlist.*?reduced-motion|FOOTER — allowlist.*?(?=\/\* ===)/s', $css, $m )
	|| preg_match( '/FOOTER — allowlist[\s\S]*?(?=\/\* ===|\z)/', $css, $m ) ) {
	$block = $m[0];
	$count = substr_count( $block, '!important' );
	echo "footer_css_important_count={$count}\n";
	echo "footer_css_len=" . strlen( $block ) . "\n";
} else {
	echo "footer_css_block=NOT_FOUND\n";
}
