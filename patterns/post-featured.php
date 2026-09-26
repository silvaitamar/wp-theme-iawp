<?php
/**
 * Title: Post featured
 * Slug: iawp/post-featured
 * Categories: posts, featured
 * Keywords: post, featured, article, hub
 * Description: Featured article row for Query Loop (image + body). Use inside a post template.
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"article","className":"post-featured","backgroundColor":"base","style":{"border":{"color":"var:preset|color|outline","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0px"}},"layout":{"type":"flex","flexWrap":"nowrap","orientation":"horizontal","verticalAlignment":"stretch"}} -->
<article class="wp-block-group post-featured has-border-color has-outline-border-color has-base-background-color has-background" style="border-color:var(--wp--preset--color--outline);border-style:solid;border-width:1px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"className":"post-featured__img","backgroundColor":"surface-mid","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group post-featured__img has-surface-mid-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"100%","scale":"cover","sizeSlug":"large","style":{"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /-->

<!-- wp:post-terms {"term":"category","className":"post-featured__cat","fontFamily":"display","fontSize":"2xs","backgroundColor":"primary","textColor":"base","style":{"typography":{"fontWeight":"900","letterSpacing":"0.15em","textTransform":"uppercase","lineHeight":"1.5"},"spacing":{"padding":{"top":"0.25rem","right":"0.85rem","bottom":"0.25rem","left":"0.85rem"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"radius":"2px"}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"post-featured__body","backgroundColor":"base","style":{"spacing":{"padding":{"top":"3rem","right":"3rem","bottom":"3rem","left":"3rem"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"stretch","verticalAlignment":"center"}} -->
<div class="wp-block-group post-featured__body has-base-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:3rem;padding-right:3rem;padding-bottom:3rem;padding-left:3rem"><!-- wp:group {"className":"post-featured__meta","style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"0","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group post-featured__meta" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:post-date {"format":"j M, Y","className":"post-featured__date","fontFamily":"display","fontSize":"2xs","textColor":"muted","style":{"typography":{"fontWeight":"900","letterSpacing":"0.15em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /-->

<!-- wp:post-time-to-read {"displayAsRange":false,"className":"post-featured__read-time","fontFamily":"display","fontSize":"2xs","textColor":"muted","style":{"typography":{"fontWeight":"900","letterSpacing":"0.15em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"isLink":true,"level":3,"className":"post-featured__title","fontFamily":"display","fontSize":"featured","textColor":"contrast","style":{"typography":{"fontWeight":"700","letterSpacing":"-0.025em","lineHeight":"1"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:post-excerpt {"moreText":"","excerptLength":32,"className":"post-featured__excerpt","fontSize":"sm","textColor":"muted","style":{"typography":{"lineHeight":"1.65"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|48"}}}} /-->

<!-- wp:read-more {"content":"Ler artigo completo","className":"post-featured__cta","fontFamily":"display","fontSize":"2xs","textColor":"primary","style":{"typography":{"fontWeight":"900","letterSpacing":"0.15em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
