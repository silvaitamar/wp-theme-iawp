/**
 * Registra variações personalizadas do bloco core/social-link.
 *
 * Enfileirado via enqueue_block_editor_assets com dependência em
 * 'wp-blocks' e 'wp-element' — garante que ambos estão disponíveis.
 *
 * Para adicionar novos serviços: duplicar o objeto em customServices
 * e colocar o SVG correspondente em assets/icons/<slug>.svg.
 * O slug deve ser idêntico ao usado no atributo "service" do bloco
 * e no filtro PHP block_core_social_link_get_services.
 */
( function ( blocks, element ) {
	'use strict';

	var customServices = [
		{
			slug: 'workana',
			title: 'Workana',
			// SVG inline — sem request extra; injetado pelo PHP via wp_localize_script
			svg: window.digitalVanguardSocialIcons && window.digitalVanguardSocialIcons.workana
				? window.digitalVanguardSocialIcons.workana
				: '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M2 5h2.8l2.35 8.64L9.5 5h2.1l2.35 8.64L16.3 5H19l-3.6 14h-2.1l-2.4-8.8L8.5 19H6.4z"/></svg>',
		},
		// Adicionar futuros serviços aqui:
		// { slug: 'behance', title: 'Behance', svg: '...' },
	];

	customServices.forEach( function ( service ) {
		var iconEl = element.createElement( 'span', {
			dangerouslySetInnerHTML: { __html: service.svg },
			style: { display: 'flex', alignItems: 'center', justifyContent: 'center' },
		} );

		blocks.registerBlockVariation( 'core/social-link', {
			name: service.slug,
			title: service.title,
			attributes: { service: service.slug },
			isActive: [ 'service' ],
			icon: iconEl,
		} );
	} );

} )( window.wp.blocks, window.wp.element );
