<?php
/**
 * Validate iawp/newsletter pattern serialization (file vs parse/serialize).
 */
$theme = get_stylesheet_directory();
$file  = $theme . '/patterns/newsletter.php';
if ( ! file_exists( $file ) ) {
	fwrite( STDERR, "missing pattern\n" );
	exit( 1 );
}

ob_start();
include $file;
$html = (string) ob_get_clean();

$blocks = parse_blocks( $html );
$issues = 0;

function iawp_news_norm( string $s ): string {
	return trim( preg_replace( '/\s+/', ' ', $s ) );
}

function iawp_news_forbidden( string $raw ): ?string {
	$needles = array(
		'is-layout-',
		'wp-block-group-is-layout-',
		'has-global-padding',
		'is-vertical',
		'is-horizontal',
		'is-nowrap',
		'is-content-justification-',
		'wp-container-',
	);
	foreach ( $needles as $n ) {
		if ( str_contains( $raw, $n ) ) {
			return $n;
		}
	}
	return null;
}

function iawp_news_walk( array $blocks, string $path = '' ): void {
	global $issues;
	foreach ( $blocks as $i => $block ) {
		$name = $block['blockName'] ?? null;
		$p    = $path . '/' . $i . ':' . ( $name ?: 'freeform' );
		if ( ! $name ) {
			continue;
		}
		$raw = (string) ( $block['innerHTML'] ?? '' );
		if ( ! empty( $block['innerContent'] ) ) {
			foreach ( $block['innerContent'] as $chunk ) {
				if ( is_string( $chunk ) ) {
					$raw .= $chunk;
				}
			}
		}
		$bad = iawp_news_forbidden( $raw );
		if ( $bad ) {
			echo "FORBIDDEN_CLASS {$bad} at {$p}\n";
			++$issues;
		}
		if ( $name === 'core/group' ) {
			$attrs = $block['attrs'] ?? array();
			$ih    = (string) ( $block['innerHTML'] ?? '' );
			if ( ! empty( $attrs['backgroundColor'] ) && str_contains( $ih, 'background-color:var(--wp--preset--color' ) ) {
				echo "DUP_BG_PRESET {$p}\n";
				++$issues;
			}
			if ( ! empty( $attrs['textColor'] ) && str_contains( $ih, 'color:var(--wp--preset--color' ) ) {
				echo "DUP_TEXT_PRESET {$p}\n";
				++$issues;
			}
		}
		$ser = serialize_block( $block );
		$rep = parse_blocks( $ser )[0] ?? null;
		if ( ! $rep || ( $rep['blockName'] ?? '' ) !== $name ) {
			echo "REPARSE_FAIL {$p}\n";
			++$issues;
		} elseif ( str_starts_with( $name, 'jetpack/' ) ) {
			echo "OK   {$p} (jetpack)\n";
		} else {
			echo "OK   {$p}\n";
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			iawp_news_walk( $block['innerBlocks'], $p );
		}
	}
}

echo 'PATTERN bytes=' . strlen( $html ) . "\n";
if ( ! str_contains( $html, 'id="newsletter"' ) ) {
	echo "MISSING newsletter anchor\n";
	++$issues;
}
if ( str_contains( $html, 'Lista VIP' ) || str_contains( $html, 'waitlist' ) ) {
	echo "BAD_COPY waitlist/VIP\n";
	++$issues;
}
iawp_news_walk( $blocks );
echo "ISSUES ({$issues})\n";
exit( $issues > 0 ? 1 : 0 );
