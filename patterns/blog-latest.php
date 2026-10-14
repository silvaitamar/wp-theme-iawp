<?php
/**
 * Title: Blog latest
 * Slug: iawp/blog-latest
 * Categories: posts, featured
 * Keywords: blog, latest, leitura, recomendada
 * Description: Blog index list — label “Leitura recomendada” + posts-hub (featured + cards grid). Composes query-featured + query-cards-blog (no duplicate posts-hub wrapper pattern).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"section section--rule","backgroundColor":"base","ariaLabel":"Lista de artigos","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"0px"}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull section section--rule has-base-background-color has-background" aria-label="Lista de artigos" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","className":"wrap","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide wrap"><!-- wp:paragraph {"className":"section__label","fontFamily":"display","fontSize":"2xs","textColor":"primary","style":{"typography":{"fontWeight":"900","letterSpacing":"0.2em","textTransform":"uppercase","lineHeight":"1"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|30"}}}} -->
<p class="section__label has-primary-color has-text-color has-display-font-family has-2-xs-font-size" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--30);font-weight:900;letter-spacing:0.2em;line-height:1;text-transform:uppercase">Leitura recomendada</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"posts-hub","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"stretch"}} -->
<div class="wp-block-group posts-hub"><!-- wp:pattern {"slug":"iawp/query-featured"} /-->

<!-- wp:group {"className":"posts-hub__grid","layout":{"type":"default"}} -->
<div class="wp-block-group posts-hub__grid"><!-- wp:pattern {"slug":"iawp/query-cards-blog"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
