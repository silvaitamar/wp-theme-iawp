/**
 * Lucide icons for core/social-link — editor canvas + inserter/list view.
 *
 * Core SocialLinkEdit renders <Icon icon={activeVariation.icon} />.
 * PHP filter only covers the front; editor needs variation.icon replaced.
 * Run after block-library registers variations (domReady + wp-block-library).
 */
( function ( blocks, element, domReady, primitives ) {
	'use strict';

	var icons = window.iawpSocialIcons || {};
	var services = [
		{ slug: 'linkedin', title: 'LinkedIn' },
		{ slug: 'wordpress', title: 'WordPress.org' },
		{ slug: 'github', title: 'GitHub' },
		{ slug: 'workana', title: 'Workana' },
		{ slug: 'buymeacoffee', title: 'Buy me a coffee' },
		{ slug: 'site', title: 'Site' },
	];

	var SVG = ( primitives && primitives.SVG ) || 'svg';
	var Path = ( primitives && primitives.Path ) || 'path';
	var Rect = ( primitives && primitives.Rect ) || 'rect';
	var Circle = ( primitives && primitives.Circle ) || 'circle';

	function camelAttrs( attrs ) {
		var out = {};
		Object.keys( attrs || {} ).forEach( function ( key ) {
			var k = key;
			if ( k === 'class' ) {
				k = 'className';
			} else if ( k === 'stroke-width' ) {
				k = 'strokeWidth';
			} else if ( k === 'stroke-linecap' ) {
				k = 'strokeLinecap';
			} else if ( k === 'stroke-linejoin' ) {
				k = 'strokeLinejoin';
			} else if ( k === 'fill-rule' ) {
				k = 'fillRule';
			} else if ( k === 'clip-rule' ) {
				k = 'clipRule';
			} else if ( k.indexOf( '-' ) !== -1 ) {
				k = k.replace( /-([a-z])/g, function ( _, c ) {
					return c.toUpperCase();
				} );
			}
			out[ k ] = attrs[ key ];
		} );
		return out;
	}

	function nodeToElement( node ) {
		if ( node.nodeType === 3 ) {
			var t = node.textContent;
			return t && t.trim() ? t : null;
		}
		if ( node.nodeType !== 1 ) {
			return null;
		}
		var tag = node.tagName.toLowerCase();
		var type = tag;
		if ( tag === 'svg' ) {
			type = SVG;
		} else if ( tag === 'path' ) {
			type = Path;
		} else if ( tag === 'rect' ) {
			type = Rect;
		} else if ( tag === 'circle' ) {
			type = Circle;
		}
		var attrs = {};
		if ( node.attributes ) {
			for ( var i = 0; i < node.attributes.length; i++ ) {
				var a = node.attributes[ i ];
				attrs[ a.name ] = a.value;
			}
		}
		attrs = camelAttrs( attrs );
		var children = [];
		for ( var c = 0; c < node.childNodes.length; c++ ) {
			var child = nodeToElement( node.childNodes[ c ] );
			if ( child !== null && child !== undefined ) {
				children.push( child );
			}
		}
		return element.createElement.apply(
			element,
			[ type, attrs ].concat( children )
		);
	}

	function parseSvgRoot( svgHtml ) {
		if ( ! svgHtml || typeof DOMParser === 'undefined' ) {
			return null;
		}
		var doc = new DOMParser().parseFromString( svgHtml, 'image/svg+xml' );
		var root = doc.documentElement;
		if ( ! root || root.tagName.toLowerCase() !== 'svg' || doc.querySelector( 'parsererror' ) ) {
			return null;
		}
		return root;
	}

	/**
	 * Match core social icons: function component → Icon() special-cases SVG.
	 */
	function makeIcon( svgHtml ) {
		var root = parseSvgRoot( svgHtml );
		if ( ! root ) {
			return function IawpSocialIconFallback( props ) {
				return element.createElement( 'span', {
					dangerouslySetInnerHTML: { __html: svgHtml },
					style: {
						display: 'inline-flex',
						width: props && props.size ? props.size : 24,
						height: props && props.size ? props.size : 24,
					},
				} );
			};
		}
		var baseAttrs = camelAttrs(
			( function () {
				var attrs = {};
				for ( var i = 0; i < root.attributes.length; i++ ) {
					attrs[ root.attributes[ i ].name ] = root.attributes[ i ].value;
				}
				return attrs;
			} )()
		);
		var childEls = [];
		for ( var c = 0; c < root.childNodes.length; c++ ) {
			var child = nodeToElement( root.childNodes[ c ] );
			if ( child !== null && child !== undefined ) {
				childEls.push( child );
			}
		}
		return function IawpSocialIcon( props ) {
			var size = ( props && props.size ) || 24;
			var attrs = Object.assign( {}, baseAttrs, {
				width: size,
				height: size,
			} );
			return element.createElement.apply(
				element,
				[ SVG, attrs ].concat( childEls )
			);
		};
	}

	function applyLucideVariations() {
		if ( ! blocks.getBlockType || ! blocks.getBlockType( 'core/social-link' ) ) {
			return false;
		}
		var applied = 0;
		services.forEach( function ( service ) {
			var svg = icons[ service.slug ];
			if ( ! svg ) {
				return;
			}
			if ( typeof blocks.unregisterBlockVariation === 'function' ) {
				try {
					blocks.unregisterBlockVariation( 'core/social-link', service.slug );
				} catch ( e ) {
					/* ok — variation may be absent */
				}
			}
			blocks.registerBlockVariation( 'core/social-link', {
				name: service.slug,
				title: service.title,
				attributes: { service: service.slug },
				isActive: function ( blockAttributes, variationAttributes ) {
					return blockAttributes.service === variationAttributes.service;
				},
				icon: makeIcon( svg ),
			} );
			applied += 1;
		} );
		return applied > 0;
	}

	// If this script ever loads before block-library, patch at register time.
	if ( window.wp && wp.hooks && wp.hooks.addFilter ) {
		wp.hooks.addFilter(
			'blocks.registerBlockType',
			'iawp/social-lucide-icons',
			function ( settings, name ) {
				if ( name !== 'core/social-link' || ! settings.variations ) {
					return settings;
				}
				var map = {};
				services.forEach( function ( s ) {
					if ( icons[ s.slug ] ) {
						map[ s.slug ] = {
							title: s.title,
							icon: makeIcon( icons[ s.slug ] ),
						};
					}
				} );
				settings.variations = settings.variations.map( function ( variation ) {
					var patch = map[ variation.name ];
					if ( ! patch ) {
						return variation;
					}
					return Object.assign( {}, variation, {
						title: patch.title,
						icon: patch.icon,
					} );
				} );
				// Ensure custom services exist even if core never listed them.
				services.forEach( function ( s ) {
					if ( ! map[ s.slug ] ) {
						return;
					}
					var exists = settings.variations.some( function ( v ) {
						return v.name === s.slug;
					} );
					if ( ! exists ) {
						settings.variations.push( {
							name: s.slug,
							title: map[ s.slug ].title,
							attributes: { service: s.slug },
							isActive: function ( blockAttributes, variationAttributes ) {
								return blockAttributes.service === variationAttributes.service;
							},
							icon: map[ s.slug ].icon,
						} );
					}
				} );
				return settings;
			}
		);
	}

	function boot() {
		if ( applyLucideVariations() ) {
			return;
		}
		// Rare race: block type not ready yet.
		var tries = 0;
		var timer = setInterval( function () {
			tries += 1;
			if ( applyLucideVariations() || tries > 40 ) {
				clearInterval( timer );
			}
		}, 50 );
	}

	if ( domReady ) {
		domReady( boot );
	} else {
		boot();
	}
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.domReady,
	window.wp.primitives
);
