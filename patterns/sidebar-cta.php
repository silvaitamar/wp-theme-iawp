<?php
/**
 * Title: Sidebar CTA
 * Slug: iawp/sidebar-cta
 * Categories: call-to-action, featured
 * Keywords: newsletter, sidebar, sticky, jetpack
 * Description: Compact sticky-sidebar newsletter form — copy from approved artigo.html proto.
 * Viewport Width: 400
 * Inserter: yes
 */
?>
<!-- wp:group {"className":"sidebar-cta","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|35","bottom":"var:preset|spacing|35","left":"var:preset|spacing|35","right":"var:preset|spacing|35"},"blockGap":"var:preset|spacing|20"},"border":{"width":"1px","color":"var:preset|color|outline","style":"solid"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch","flexWrap":"nowrap"}} -->
<div class="wp-block-group sidebar-cta has-border-color has-outline-border-color has-base-background-color has-background" style="border-color:var(--wp--preset--color--outline);border-style:solid;border-width:1px;padding-top:var(--wp--preset--spacing--35);padding-right:var(--wp--preset--spacing--35);padding-bottom:var(--wp--preset--spacing--35);padding-left:var(--wp--preset--spacing--35)"><!-- wp:heading {"level":4,"className":"sidebar-cta-title","fontFamily":"display","textColor":"contrast","style":{"typography":{"fontSize":"1.375rem","fontWeight":"900","letterSpacing":"-0.04em","lineHeight":"1.05","textTransform":"none"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<h4 class="wp-block-heading sidebar-cta-title has-contrast-color has-text-color has-display-font-family" style="margin-top:0;margin-bottom:0;font-size:1.375rem;font-weight:900;letter-spacing:-0.04em;line-height:1.05;text-transform:none">Novos artigos no e-mail</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sidebar-cta-desc","textColor":"muted","style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.6"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="sidebar-cta-desc has-muted-color has-text-color" style="margin-top:0;margin-bottom:0;font-size:0.9375rem;line-height:1.6">Sem waitlist. Aviso quando sair conteúdo novo no hub.</p>
<!-- /wp:paragraph -->

<!-- wp:jetpack/contact-form {"subject":"Newsletter IA+WP — novo inscrito (sidebar)","jetpackCRM":false,"variationName":"default","customThankyou":"message","customThankyouMessage":"Obrigado! Voc\u00ea receber\u00e1 aviso quando publicarmos novos artigos.","className":"sidebar-form","layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap"}} -->
<div class="wp-block-jetpack-contact-form sidebar-form"><!-- wp:jetpack/field-email {"required":true} -->
<div><!-- wp:jetpack/label /-->

<!-- wp:jetpack/input {"placeholder":"Seu melhor e-mail"} /--></div>
<!-- /wp:jetpack/field-email -->

<!-- wp:button {"tagName":"button","type":"submit","lock":{"move":false,"remove":true}} -->
<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Receber artigos →</button></div>
<!-- /wp:button --></div>
<!-- /wp:jetpack/contact-form --></div>
<!-- /wp:group -->
