/**
 * Jetpack Form UX — auto-reset + no-scroll
 *
 * Carregado como ES module via wp_enqueue_script_module().
 * Importa @wordpress/interactivity para sobrescrever callbacks do store
 * "jetpack/form" ANTES de o view.js do Jetpack executar o scrollToWrapper.
 *
 * Comportamento:
 *  - Remove o scroll automático após submit
 *  - Aciona goBack() automaticamente após 4s
 */

import { store, getContext } from '@wordpress/interactivity';

const AUTO_RESET_MS = 5000;
const timers = new Map();

store( 'jetpack/form', {
	callbacks: {
		scrollToWrapper() {
			const ctx = getContext();
			if ( ! ctx ) return;

			const hash = ctx.formHash;
			if ( ! hash ) return;

			if ( ctx.submissionSuccess && ! ctx.hasClickedBack ) {
				// Cancela timer anterior
				if ( timers.has( hash ) ) {
					clearTimeout( timers.get( hash ) );
				}

				// Auto-reset após AUTO_RESET_MS
				const id = setTimeout( () => {
					const backBtn = document.querySelector(
						`#contact-form-success-${ hash } .go-back-message a`
					);
					if ( backBtn ) backBtn.click();
					timers.delete( hash );
				}, AUTO_RESET_MS );

				timers.set( hash, id );

			} else if ( ctx.hasClickedBack ) {
				// Usuário clicou Voltar manualmente — cancela timer
				if ( timers.has( hash ) ) {
					clearTimeout( timers.get( hash ) );
					timers.delete( hash );
				}
			}
		},
	},
} );
