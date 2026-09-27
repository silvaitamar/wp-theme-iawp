<?php
/**
 * Title: Share links
 * Slug: iawp/share-links
 * Categories: featured
 * Keywords: share, social, linkedin, x, mail, outermost
 * Description: Social Sharing Block (outermost) — LinkedIn / X / e-mail. Reused in single meta + sticky sidebar. Proto labels via CSS (valid save).
 * Viewport Width: 480
 * Inserter: yes
 */
?>
<!-- wp:group {"className":"share-row","style":{"spacing":{"blockGap":"var:preset|spacing|15"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group share-row"><!-- wp:paragraph {"className":"single-meta-label","fontFamily":"display","fontSize":"2xs","textColor":"dim","style":{"typography":{"fontWeight":"600","letterSpacing":"0.15em","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="single-meta-label has-dim-color has-text-color has-display-font-family has-2-xs-font-size" style="margin-top:0;margin-bottom:0;font-weight:600;letter-spacing:0.15em;text-transform:uppercase">Compartilhe</p>
<!-- /wp:paragraph -->

<!-- wp:outermost/social-sharing {"className":"single-share-list","style":{"spacing":{"blockGap":{"left":"0.5rem"}}}} -->
<ul class="wp-block-outermost-social-sharing single-share-list"><!-- wp:outermost/social-sharing-link {"service":"linkedin"} /-->

<!-- wp:outermost/social-sharing-link {"service":"x"} /-->

<!-- wp:outermost/social-sharing-link {"service":"mail"} /--></ul>
<!-- /wp:outermost/social-sharing --></div>
<!-- /wp:group -->
