<?php
/**
 * Title: Query cards (archive)
 * Slug: iawp/query-cards-archive
 * Categories: posts, featured
 * Keywords: query, cards, archive
 * Description: Query Loop — up to six posts (offset 1) in current archive, 2-column grid + post-card. Pagination backlog: see vault pendencias.
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:query {"queryId":63,"query":{"perPage":6,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","columnCount":2}} -->
<!-- wp:pattern {"slug":"iawp/post-card"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
