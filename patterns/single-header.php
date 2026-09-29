<?php
/**
 * Title: Single header
 * Slug: iawp/single-header
 * Categories: featured, banner
 * Keywords: single, article, header, meta, featured
 * Description: Article header — category, read time, title, dates, share, featured image (artigo.html proto).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"header","align":"full","className":"single-header","backgroundColor":"base","ariaLabel":"Cabeçalho do Artigo","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"},"blockGap":"0px"}},"layout":{"type":"constrained"}} -->
<header class="wp-block-group alignfull single-header has-base-background-color has-background" aria-label="Cabeçalho do Artigo" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"align":"wide","className":"single-header__inner wrap","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide single-header__inner wrap"><!-- wp:group {"className":"single-cat-row","style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"0","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group single-cat-row" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:post-terms {"term":"category","separator":" ","className":"single-cat"} /-->

<!-- wp:post-time-to-read {"displayAsRange":false,"className":"single-read-time","fontFamily":"display","fontSize":"2xs","textColor":"muted","style":{"typography":{"fontWeight":"700","letterSpacing":"0.2em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":1,"className":"single-title","fontFamily":"display","textColor":"contrast","style":{"typography":{"fontSize":"clamp(2rem, 5vw, 4.5rem)","fontWeight":"900","letterSpacing":"-0.05em","lineHeight":"1.05","textTransform":"none"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|35"}}}} /-->

<!-- wp:group {"className":"single-meta-bar","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|35","bottom":"var:preset|spacing|35"},"margin":{"top":"0","bottom":"var:preset|spacing|48"}}},"layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"center","flexWrap":"wrap"}} -->
<div class="wp-block-group single-meta-bar" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--48);padding-top:var(--wp--preset--spacing--35);padding-bottom:var(--wp--preset--spacing--35)"><!-- wp:group {"className":"single-meta-dates","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group single-meta-dates"><!-- wp:group {"className":"single-meta-pair","style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group single-meta-pair"><!-- wp:paragraph {"className":"single-meta-label","fontFamily":"display","fontSize":"2xs","textColor":"dim","style":{"typography":{"fontWeight":"600","letterSpacing":"0.15em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="single-meta-label has-dim-color has-text-color has-display-font-family has-2-xs-font-size" style="margin-top:0;margin-bottom:0;font-weight:600;letter-spacing:0.15em;text-transform:uppercase">Publicado</p>
<!-- /wp:paragraph -->

<!-- wp:post-date {"format":"j \\d\\e F, Y","textColor":"muted","style":{"typography":{"fontSize":"0.8125rem"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"className":"single-author-date"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"single-meta-pair single-meta-updated","style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group single-meta-pair single-meta-updated"><!-- wp:paragraph {"className":"single-meta-label","fontFamily":"display","fontSize":"2xs","textColor":"dim","style":{"typography":{"fontWeight":"600","letterSpacing":"0.15em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="single-meta-label has-dim-color has-text-color has-display-font-family has-2-xs-font-size" style="margin-top:0;margin-bottom:0;font-weight:600;letter-spacing:0.15em;text-transform:uppercase">Atualizado</p>
<!-- /wp:paragraph -->

<!-- wp:post-date {"format":"j \\d\\e F, Y","metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"modified"}}}},"textColor":"muted","style":{"typography":{"fontSize":"0.8125rem"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"className":"wp-block-post-date__modified-date single-author-date"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"iawp/share-links"} /--></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"aspectRatio":"21/9","scale":"cover","className":"single-featured"} /--></div>
<!-- /wp:group --></header>
<!-- /wp:group -->
