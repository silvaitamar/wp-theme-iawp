<?php
/**
 * Dump footer template-part override + sync from theme file if requested.
 * Usage:
 *   studio wp eval-file …/probe-footer-sync.php --path …
 *   IAWP_FOOTER_SYNC=1 studio wp eval-file …  (PowerShell: $env:IAWP_FOOTER_SYNC=1)
 */
$theme = wp_get_theme();
$file  = get_stylesheet_directory() . '/parts/footer.html';
$file_html = file_get_contents( $file );

$q = new WP_Query(
	array(
		'post_type'      => 'wp_template_part',
		'post_status'    => 'any',
		'posts_per_page' => 20,
		'name'           => 'footer',
	)
);

echo "file_len=" . strlen( $file_html ) . "\n";
echo "override_found=" . $q->found_posts . "\n";

foreach ( $q->posts as $p ) {
	echo "ID={$p->ID} status={$p->post_status} slug={$p->post_name}\n";
	echo "db_len=" . strlen( $p->post_content ) . "\n";
	echo "db_has_site_footer=" . ( str_contains( $p->post_content, 'site-footer' ) ? 'yes' : 'no' ) . "\n";
	echo "db_has_backgroundColor_base=" . ( str_contains( $p->post_content, '"backgroundColor":"base"' ) ? 'yes' : 'no' ) . "\n";
	echo "db_has_is_layout=" . ( preg_match( '/is-layout-|wp-container-/', $p->post_content ) ? 'yes' : 'no' ) . "\n";
	echo "identical_to_file=" . ( $p->post_content === $file_html ? 'yes' : 'no' ) . "\n";
	echo "--- db head ---\n";
	echo substr( $p->post_content, 0, 400 ) . "\n";

	$sync = getenv( 'IAWP_FOOTER_SYNC' );
	if ( $sync === '1' || $sync === 'true' ) {
		$r = wp_update_post(
			array(
				'ID'           => $p->ID,
				'post_content' => $file_html,
			),
			true
		);
		if ( is_wp_error( $r ) ) {
			echo "sync_error=" . $r->get_error_message() . "\n";
		} else {
			$fresh = get_post( $p->ID );
			echo "sync_ok=yes new_len=" . strlen( $fresh->post_content ) . " identical=" . ( $fresh->post_content === $file_html ? 'yes' : 'no' ) . "\n";
		}
	}
}

// Precise !important count inside FOOTER allowlist comment block only
$css = file_get_contents( get_stylesheet_directory() . '/style.css' );
if ( preg_match( '/\/\* =+\s*\n\s*FOOTER — allowlist[\s\S]*?\n\/\* =+/', $css, $m ) ) {
	$block = $m[0];
	// strip the following section header from count window
	$block = preg_replace( '/\n\/\* =+[\s\S]*$/', '', $block );
	echo "footer_css_important_count=" . substr_count( $block, '!important' ) . "\n";
} else {
	echo "footer_css_block=NOT_FOUND\n";
}
