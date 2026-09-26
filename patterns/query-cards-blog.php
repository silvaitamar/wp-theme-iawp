<?php
/**
 * Title: Query cards (blog)
 * Slug: iawp/query-cards-blog
 * Categories: posts, featured
 * Keywords: query, cards, blog, grid
 * Description: Query Loop — three posts (offset 1) in a 2-column grid using post-card (blog index).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:query {"queryId":31,"query":{"perPage":3,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","columnCount":2}} -->
<!-- wp:pattern {"slug":"iawp/post-card"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
