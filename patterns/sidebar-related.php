<?php
/**
 * Title: Sidebar related
 * Slug: iawp/sidebar-related
 * Categories: posts, featured
 * Keywords: related, sidebar, latest
 * Description: Sticky-sidebar “Relacionados” — 2 other posts (excludes current). Structure from artigo.html proto.
 * Viewport Width: 400
 * Inserter: yes
 */
?>
<!-- wp:group {"className":"sidebar-related","style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group sidebar-related" style="margin-top:0;margin-bottom:0"><!-- wp:paragraph {"className":"sidebar-section-label","fontFamily":"display","fontSize":"2xs","textColor":"primary","style":{"typography":{"fontWeight":"900","letterSpacing":"0.3em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|25"}}}} -->
<p class="sidebar-section-label has-primary-color has-text-color has-display-font-family has-2-xs-font-size" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--25);font-weight:900;letter-spacing:0.3em;text-transform:uppercase">Relacionados</p>
<!-- /wp:paragraph -->

<!-- wp:query {"queryId":55,"query":{"perPage":2,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"excludeCurrent":true,"sticky":"exclude","inherit":false},"className":"sidebar-related-query"} -->
<div class="wp-block-query sidebar-related-query"><!-- wp:post-template {"className":"sidebar-related-list"} -->
<!-- wp:post-title {"isLink":true,"level":3,"fontFamily":"display","textColor":"contrast","style":{"typography":{"fontSize":"1rem","fontWeight":"700","lineHeight":"1.3","letterSpacing":"-0.02em"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
