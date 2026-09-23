<?php
/**
 * Title: Newsletter
 * Slug: iawp/newsletter
 * Categories: featured, call-to-action
 * Keywords: newsletter, email, hub, subscribe
 * Description: Full-width newsletter CTA — copy from the approved hub proto (no waitlist / Lista VIP).
 * Viewport Width: 1344
 * Inserter: yes
 */
?>
<!-- wp:group {"tagName":"section","align":"full","anchor":"newsletter","className":"cta-section newsletter","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"margin":{"top":"0","bottom":"0"}},"color":{"background":"var:preset|color|primary","text":"var:preset|color|base"}},"layout":{"type":"constrained"},"ariaLabel":"Newsletter"} -->
<section aria-label="Newsletter" id="newsletter" class="wp-block-group alignfull cta-section newsletter has-primary-background-color has-base-color has-text-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80);color:var(--wp--preset--color--base);background-color:var(--wp--preset--color--primary)"><!-- wp:group {"align":"wide","className":"cta-inner","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group alignwide cta-inner"><!-- wp:heading {"textAlign":"center","level":2,"className":"cta-heading"} -->
<h2 class="wp-block-heading has-text-align-center cta-heading" id="news-heading">Receba os artigos.<br><span class="cta-white">No seu e-mail.</span></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"cta-subheading"} -->
<p class="has-text-align-center cta-subheading">Acompanhe o hub editorial: aviso quando publicarmos conteúdo novo sobre IA, WordPress e soberania digital.</p>
<!-- /wp:paragraph -->

<!-- wp:jetpack/contact-form {"subject":"Newsletter IA+WP — novo inscrito","customThankyou":"message","customThankyouMessage":"Obrigado! Você receberá aviso quando publicarmos novos artigos.","jetpackCRM":false,"variationName":"default","className":"cta-form","layout":{"type":"flex","flexWrap":"nowrap","orientation":"horizontal","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-jetpack-contact-form cta-form"><!-- wp:jetpack/field-email {"required":true,"placeholder":"Seu melhor e-mail"} -->
<div><!-- wp:jetpack/label {"label":"E-mail"} /-->

<!-- wp:jetpack/input {"placeholder":"Seu melhor e-mail"} /--></div>
<!-- /wp:jetpack/field-email -->

<!-- wp:button {"tagName":"button","type":"submit"} -->
<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Receber artigos →</button></div>
<!-- /wp:button --></div>
<!-- /wp:jetpack/contact-form -->

<!-- wp:paragraph {"align":"center","className":"cta-privacy"} -->
<p class="has-text-align-center cta-privacy">Ao se inscrever, você concorda com nossos <a href="/termos/">termos</a> e <a href="/privacidade/">política de privacidade</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
