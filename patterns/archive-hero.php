<?php
/**
 * Title: Archive hero
 * Slug: iawp/archive-hero
 * Categories: featured, banner
 * Keywords: archive, category, cluster, page-hero
 * Description: Category/tag archive hero — crumb, icon, query-title, term description, meta (categoria.html proto).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"header","align":"full","className":"page-hero page-hero--archive","backgroundColor":"base","ariaLabel":"Cabeçalho do arquivo","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|75","bottom":"var:preset|spacing|50"},"blockGap":"0px"}},"layout":{"type":"constrained"}} -->
<header class="wp-block-group alignfull page-hero page-hero--archive has-base-background-color has-background" aria-label="Cabeçalho do arquivo" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--75);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"align":"wide","className":"wrap","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignwide wrap"><!-- wp:paragraph {"className":"page-crumb","fontFamily":"display","fontSize":"2xs","textColor":"dim","style":{"typography":{"fontWeight":"700","letterSpacing":"0.14em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="page-crumb has-dim-color has-text-color has-display-font-family has-2-xs-font-size" style="margin-top:0;margin-bottom:0;font-weight:700;letter-spacing:0.14em;text-transform:uppercase"><a href="/blog/">Artigos</a> <span class="page-crumb__sep" aria-hidden="true">/</span> <span>Cluster</span></p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"archive-hero__top","style":{"spacing":{"blockGap":"var:preset|spacing|25"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-group archive-hero__top"><!-- wp:icon {"icon":"iawp/zap","className":"archive-hero__icon bento-icon","style":{"dimensions":{"width":"2.25rem"},"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /-->

<!-- wp:query-title {"type":"archive","showPrefix":false,"level":1,"fontFamily":"display","fontSize":"page","textColor":"contrast","style":{"typography":{"fontWeight":"900","letterSpacing":"-0.05em","lineHeight":"0.9","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:term-description {"textColor":"muted","fontSize":"xl","style":{"typography":{"lineHeight":"1.65"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /-->

<!-- wp:group {"className":"archive-meta","style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"var:preset|spacing|20","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group archive-meta" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:0"><!-- wp:query {"queryId":61,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"className":"archive-meta-count"} -->
<div class="wp-block-query archive-meta-count"><!-- wp:query-total {"displayType":"total-results","fontFamily":"display","fontSize":"2xs","textColor":"dim","style":{"typography":{"fontWeight":"600","letterSpacing":"0.14em","textTransform":"uppercase"}}} /--></div>
<!-- /wp:query -->

<!-- wp:paragraph {"className":"archive-meta__dot","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="archive-meta__dot" style="margin-top:0;margin-bottom:0" aria-hidden="true"></p>
<!-- /wp:paragraph -->

<!-- wp:query {"queryId":64,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"archive-meta-updated-query"} -->
<div class="wp-block-query archive-meta-updated-query"><!-- wp:post-template {"className":"archive-meta-updated"} -->
<!-- wp:post-date {"format":"j M, Y","fontFamily":"display","fontSize":"2xs","textColor":"dim","style":{"typography":{"fontWeight":"600","letterSpacing":"0.14em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></header>
<!-- /wp:group -->
