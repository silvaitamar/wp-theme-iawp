<?php
/**
 * Title: Post card
 * Slug: iawp/post-card
 * Categories: posts, featured
 * Keywords: post, card, article, hub
 * Description: Compact article card for Query Loop grid. Use inside a post template.
 * Viewport Width: 640
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"article","className":"post-card","backgroundColor":"base","style":{"border":{"color":"var:preset|color|outline","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"stretch"}} -->
<article class="wp-block-group post-card has-border-color has-outline-border-color has-base-background-color has-background" style="border-color:var(--wp--preset--color--outline);border-style:solid;border-width:1px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"21/9","width":"100%","scale":"cover","sizeSlug":"large","className":"post-card__img","style":{"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /-->

<!-- wp:group {"className":"post-card__body","style":{"spacing":{"padding":{"top":"var:preset|spacing|48","right":"var:preset|spacing|48","bottom":"var:preset|spacing|48","left":"var:preset|spacing|48"},"blockGap":"0px"},"@mobile":{"spacing":{"padding":{"top":"1.5rem","right":"1.5rem","bottom":"1.5rem","left":"1.5rem"}}}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"stretch"}} -->
<div class="wp-block-group post-card__body" style="padding-top:var(--wp--preset--spacing--48);padding-right:var(--wp--preset--spacing--48);padding-bottom:var(--wp--preset--spacing--48);padding-left:var(--wp--preset--spacing--48)"><!-- wp:post-terms {"term":"category","className":"post-card__cat","fontFamily":"display","fontSize":"2xs","textColor":"primary","style":{"typography":{"fontWeight":"900","letterSpacing":"0.2em","textTransform":"uppercase","lineHeight":"1"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|20","left":"0","right":"0"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /-->

<!-- wp:post-title {"isLink":true,"level":3,"className":"post-card__title","fontFamily":"display","fontSize":"2xl","textColor":"contrast","style":{"typography":{"fontWeight":"700","letterSpacing":"-0.025em","lineHeight":"1.25"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|20"}}}} /-->

<!-- wp:post-excerpt {"moreText":"","excerptLength":22,"className":"post-card__excerpt","fontSize":"sm","textColor":"muted","style":{"typography":{"lineHeight":"1.65"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|35"}}}} /-->

<!-- wp:group {"className":"post-card__footer","style":{"spacing":{"padding":{"top":"var:preset|spacing|30"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group post-card__footer" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--30)"><!-- wp:post-date {"format":"j M, Y","className":"post-card__byline","fontFamily":"display","fontSize":"2xs","textColor":"muted","style":{"typography":{"fontWeight":"900","letterSpacing":"0.15em","textTransform":"uppercase","lineHeight":"1"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /-->

<!-- wp:html -->
<span class="post-card__arrow" aria-hidden="true"></span>
<!-- /wp:html --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
