<?php
/**
 * Check if wp_template override exists for front-page (can keep old broken newsletter).
 */
$q = new WP_Query(
	array(
		'post_type'      => 'wp_template',
		'post_status'    => 'any',
		'posts_per_page' => 20,
		'no_found_rows'  => true,
	)
);
echo "wp_template count=" . count( $q->posts ) . "\n";
foreach ( $q->posts as $p ) {
	$slug = $p->post_name;
	$theme = get_stylesheet();
	echo "- id={$p->ID} status={$p->post_status} name={$slug} modified={$p->post_modified}\n";
	if ( str_contains( $slug, 'front-page' ) || str_contains( $p->post_title, 'front' ) || str_contains( $p->post_content, 'newsletter' ) ) {
		$has_cta_submit = str_contains( $p->post_content, 'cta-submit' );
		$has_transparent = str_contains( $p->post_content, 'background-color:transparent' );
		$has_pattern     = str_contains( $p->post_content, 'iawp/newsletter' );
		echo "  has_pattern_ref=" . ( $has_pattern ? 'yes' : 'no' ) . " cta-submit=" . ( $has_cta_submit ? 'yes' : 'no' ) . " transparent_btn=" . ( $has_transparent ? 'yes' : 'no' ) . " len=" . strlen( $p->post_content ) . "\n";
		if ( preg_match( '/wp:button[^\n]+/', $p->post_content, $m ) ) {
			echo '  button_comment=' . substr( $m[0], 0, 220 ) . "\n";
		}
	}
}
if ( ! $q->posts ) {
	echo "no wp_template overrides (theme file wins)\n";
}
