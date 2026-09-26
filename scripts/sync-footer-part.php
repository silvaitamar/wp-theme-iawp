<?php
/**
 * Always sync wp_template_part footer (ID by slug) from theme parts/footer.html.
 */
$file = get_stylesheet_directory() . '/parts/footer.html';
$html = file_get_contents( $file );
if ( $html === false || $html === '' ) {
	echo "error=empty_file\n";
	return;
}

$q = new WP_Query(
	array(
		'post_type'      => 'wp_template_part',
		'post_status'    => 'any',
		'posts_per_page' => 5,
		'name'           => 'footer',
	)
);

if ( ! $q->have_posts() ) {
	echo "override=none (theme file will be used)\n";
	return;
}

foreach ( $q->posts as $p ) {
	echo "before_id={$p->ID} before_len=" . strlen( $p->post_content ) . "\n";
	$r = wp_update_post(
		array(
			'ID'           => $p->ID,
			'post_content' => $html,
		),
		true
	);
	if ( is_wp_error( $r ) ) {
		echo "error=" . $r->get_error_message() . "\n";
		continue;
	}
	clean_post_cache( $p->ID );
	$fresh = get_post( $p->ID );
	echo "after_len=" . strlen( $fresh->post_content ) . "\n";
	echo "identical=" . ( $fresh->post_content === $html ? 'yes' : 'no' ) . "\n";
	echo "has_bg_base=" . ( str_contains( $fresh->post_content, '"backgroundColor":"base"' ) ? 'yes' : 'no' ) . "\n";
	echo "has_outline=" . ( str_contains( $fresh->post_content, 'outline' ) ? 'yes' : 'no' ) . "\n";
}
