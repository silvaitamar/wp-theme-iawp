<?php
/**
 * IA+WP theme bootstrap.
 *
 * Loads modules from inc/. Runtime logic lives in those files — keep this thin.
 *
 * FONTES: theme.json fontFace + assets/fonts/ (sem Google Fonts enqueue).
 * Ícones: assets/icons/ → Icons API iawp/* (core/icon). Grain + #grad-brand em icons.php.
 * SEO meta/OG/JSON-LD: The SEO Framework — tema só cobre head cleanup + llms.txt.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

$iawp_inc = get_theme_file_path( 'inc' );

require_once $iawp_inc . '/style-surfaces.php';
require_once $iawp_inc . '/setup.php';
require_once $iawp_inc . '/icons.php';
require_once $iawp_inc . '/assets.php';
require_once $iawp_inc . '/blocks.php';
require_once $iawp_inc . '/head-seo.php';
require_once $iawp_inc . '/social.php';
