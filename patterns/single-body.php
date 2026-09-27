<?php
/**
 * Title: Single body
 * Slug: iawp/single-body
 * Categories: featured, posts
 * Keywords: single, article, prose, sidebar, sticky
 * Description: Article body — prose column + mid CTA + sticky sidebar (artigo.html proto).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"single-body","backgroundColor":"base","ariaLabel":"Conteúdo do Artigo","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","bottom":"var:preset|spacing|60","left":"0","right":"0"},"blockGap":"0px"}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull single-body has-base-background-color has-background" aria-label="Conteúdo do Artigo" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--60);padding-left:0"><!-- wp:columns {"align":"wide","className":"single-layout wrap","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide single-layout wrap"><!-- wp:column {"width":"66.66%","className":"single-content-col","style":{"spacing":{"blockGap":"3rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch","flexWrap":"nowrap"}} -->
<div class="wp-block-column single-content-col" style="flex-basis:66.66%"><!-- wp:post-content {"className":"single-prose","layout":{"type":"default"}} /-->

<!-- wp:pattern {"slug":"iawp/article-cta"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%","className":"single-sidebar-col"} -->
<div class="wp-block-column single-sidebar-col" style="flex-basis:33.33%"><!-- wp:pattern {"slug":"iawp/single-sidebar"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
