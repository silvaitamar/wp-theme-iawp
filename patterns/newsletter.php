<?php
/**
 * Title: Newsletter
 * Slug: iawp/newsletter
 * Categories: featured, call-to-action
 * Keywords: newsletter, email, hub, subscribe
 * Description: Full-width newsletter CTA — copy from the approved hub proto (newsletter of new articles).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"section","align":"full","anchor":"newsletter","className":"cta-section newsletter","backgroundColor":"primary","textColor":"base","ariaLabel":"Newsletter","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"margin":{"top":"0","bottom":"0"},"blockGap":"0px"},"@mobile":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull cta-section newsletter has-base-color has-primary-background-color has-text-color has-background" id="newsletter" aria-label="Newsletter" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","className":"cta-inner","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignwide cta-inner" style="margin-top:0;margin-bottom:0"><!-- wp:heading {"textAlign":"center","level":2,"anchor":"news-heading","className":"cta-heading","fontFamily":"display","textColor":"base","style":{"typography":{"fontSize":"clamp(2rem, 6vw, 6rem)","fontWeight":"900","letterSpacing":"-0.05em","lineHeight":"0.92","textTransform":"uppercase"},"spacing":{"margin":{"top":"0","bottom":"0"}},"@mobile":{"typography":{"fontSize":"clamp(2rem, 10vw, 3rem)"}}}} -->
<h2 class="wp-block-heading has-text-align-center cta-heading has-base-color has-text-color has-display-font-family" id="news-heading" style="margin-top:0;margin-bottom:0;font-size:clamp(2rem, 6vw, 6rem);font-weight:900;letter-spacing:-0.05em;line-height:0.92;text-transform:uppercase">Receba os artigos.<br><span class="cta-white">No seu e-mail.</span></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"cta-subheading","textColor":"base","style":{"typography":{"fontSize":"0.75rem","fontWeight":"600","letterSpacing":"0.4em","textTransform":"uppercase","lineHeight":"1.4"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="has-text-align-center cta-subheading has-base-color has-text-color" style="margin-top:0;margin-bottom:0;font-size:0.75rem;font-weight:600;letter-spacing:0.4em;line-height:1.4;text-transform:uppercase">Acompanhe o hub editorial: aviso quando publicarmos conteúdo novo sobre IA, WordPress e soberania digital.</p>
<!-- /wp:paragraph -->

<!-- wp:jetpack/contact-form {"subject":"Newsletter IA+WP — novo inscrito","customThankyou":"message","customThankyouMessage":"Obrigado! Você receberá aviso quando publicarmos novos artigos.","jetpackCRM":false,"variationName":"default","className":"cta-form","layout":{"type":"flex","flexWrap":"nowrap","orientation":"horizontal","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-jetpack-contact-form cta-form"><!-- wp:jetpack/field-email {"required":true} -->
<div><!-- wp:jetpack/label /-->

<!-- wp:jetpack/input {"placeholder":"Seu melhor e-mail"} /--></div>
<!-- /wp:jetpack/field-email -->

<!-- wp:button {"tagName":"button","type":"submit","lock":{"move":false,"remove":true}} -->
<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Receber artigos →</button></div>
<!-- /wp:button --></div>
<!-- /wp:jetpack/contact-form -->

<!-- wp:paragraph {"align":"center","className":"cta-privacy","fontSize":"2xs","style":{"typography":{"letterSpacing":"0.1em","textTransform":"uppercase","lineHeight":"1.5"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p class="has-text-align-center cta-privacy has-2-xs-font-size" style="margin-top:0;margin-bottom:0;letter-spacing:0.1em;line-height:1.5;text-transform:uppercase">Ao se inscrever, você concorda com nossos <a href="/termos/">termos</a> e <a href="/privacidade/">política de privacidade</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
