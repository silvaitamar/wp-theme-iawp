<?php
/**
 * Title: Posts hub
 * Slug: iawp/posts-hub
 * Categories: posts, featured
 * Keywords: posts, hub, featured, cards
 * Description: Featured query + two-card grid for the home latest section.
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"className":"posts-hub","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"stretch"}} -->
<div class="wp-block-group posts-hub"><!-- wp:pattern {"slug":"iawp/query-featured"} /-->

<!-- wp:group {"className":"posts-hub__grid","layout":{"type":"default"}} -->
<div class="wp-block-group posts-hub__grid"><!-- wp:pattern {"slug":"iawp/query-cards"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
