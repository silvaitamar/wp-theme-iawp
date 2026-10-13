<?php
/**
 * Title: Section header
 * Slug: iawp/section-header
 * Categories: featured
 * Keywords: section, header, latest, hub
 * Description: Section heading + aside (lede + more link). Default copy: Últimos artigos.
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"className":"section-header","style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"bottom":"3rem"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group section-header" style="margin-bottom:3rem"><!-- wp:heading {"level":2,"className":"section-heading","fontFamily":"display","fontSize":"section","textColor":"contrast","style":{"typography":{"fontWeight":"900","letterSpacing":"-0.05em","lineHeight":"0.9","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}},"@tablet":{"typography":{"fontSize":"clamp(2rem, 10vw, 3rem)"}},"@mobile":{"typography":{"fontSize":"clamp(2rem, 10vw, 3rem)"}}}} -->
<h2 class="wp-block-heading section-heading has-contrast-color has-text-color has-display-font-family has-section-font-size" id="latest-heading" style="margin-top:0;margin-bottom:0;font-weight:900;letter-spacing:-0.05em;line-height:0.9;text-transform:uppercase">Últimos<br><span class="text-accent">artigos</span></h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"section-header__aside","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"right","flexWrap":"nowrap"}} -->
<div class="wp-block-group section-header__aside"><!-- wp:group {"className":"section-lede","layout":{"type":"default"}} -->
<div class="wp-block-group section-lede"><!-- wp:paragraph {"textColor":"muted","fontSize":"sm","style":{"typography":{"fontWeight":"600","lineHeight":"1.6"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="has-muted-color has-text-color has-sm-font-size" style="margin-top:0;margin-bottom:0;font-weight:600;line-height:1.6">Novos artigos toda terça. Os três mais recentes abaixo.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"section-more","layout":{"type":"default"}} -->
<div class="wp-block-group section-more"><!-- wp:paragraph {"fontFamily":"display","fontSize":"xs","textColor":"primary","style":{"typography":{"fontWeight":"700","letterSpacing":"0.14em","textTransform":"uppercase","lineHeight":"1"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="has-primary-color has-text-color has-display-font-family has-xs-font-size" style="margin-top:0;margin-bottom:0;font-weight:700;letter-spacing:0.14em;line-height:1;text-transform:uppercase"><a href="/blog/">Todos os artigos <span aria-hidden="true">→</span></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
