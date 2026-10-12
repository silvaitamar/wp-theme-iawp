<?php
/**
 * Block render filters: nav a11y, dates, query loop, archive titles.
 *
 * @package iawp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Descrições dos templates custom na listagem do Site Editor.
 *
 * Theme.json customTemplates só aceita name / title / postTypes — sem description.
 * Não usar default_template_types: isso marca is_custom=false e tira o template
 * do seletor de páginas.
 */
add_filter(
	'get_block_templates',
	function ( array $templates, $query, string $template_type ): array {
		if ( 'wp_template' !== $template_type ) {
			return $templates;
		}

		$meta = array(
			'page-no-title' => array(
				'title'       => 'Página sem título',
				'description' => 'Exibe o conteúdo da página sem título automático — útil quando o cabeçalho e o layout vêm do próprio conteúdo.',
			),
			'page-legal'    => array(
				'title'       => 'Legal',
				'description' => 'Layout para páginas legais (privacidade, termos): título e prosa estreita, sem meta de post.',
			),
		);

		foreach ( $templates as $template ) {
			if ( ! isset( $meta[ $template->slug ] ) ) {
				continue;
			}
			$template->title       = $meta[ $template->slug ]['title'];
			$template->description = $meta[ $template->slug ]['description'];
		}

		return $templates;
	},
	10,
	3
);

/**
 * Aria-label nas navegações do tema.
 *
 * O atributo ariaLabel no JSON do bloco core/navigation concatena com o ariaLabel
 * do data-wp-context do Interactivity API, duplicando o texto no aria-label do <nav>.
 * Solução: injetar via render_block com className como discriminador.
 */
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( 'core/navigation' !== $block['blockName'] ) {
			return $html;
		}
		$class = $block['attrs']['className'] ?? '';
		if ( strpos( $class, 'site-header__nav' ) !== false ) {
			// Proto: aria-label="Principal".
			$html = preg_replace( '/<nav\b/', '<nav aria-label="Principal"', $html, 1 );
		} elseif ( strpos( $class, 'footer-nav' ) !== false ) {
			$html = preg_replace( '/<nav\b/', '<nav aria-label="Rodapé"', $html, 1 );
		}
		return $html;
	},
	10,
	2
);

/**
 * Single meta — sempre exibir “Atualizado” (fidelidade artigo.html).
 *
 * O core omite <time> do post-date modified quando modified ≤ publish.
 * O proto mostra os dois pares mesmo com a mesma data civil.
 */
add_filter(
	'render_block_core/post-date',
	function ( $content, $block ) {
		$class = (string) ( $block['attrs']['className'] ?? '' );
		if ( ! str_contains( $class, 'wp-block-post-date__modified-date' ) ) {
			return $content;
		}
		if ( is_string( $content ) && str_contains( $content, '<time' ) ) {
			return $content;
		}

		$post = get_post();
		if ( ! $post instanceof WP_Post ) {
			return $content;
		}

		$format  = $block['attrs']['format'] ?? get_option( 'date_format' );
		$display = get_the_modified_date( $format, $post );
		if ( ! is_string( $display ) || '' === $display ) {
			$display = get_the_date( $format, $post );
		}
		$datetime = get_the_modified_date( DATE_W3C, $post );
		if ( ! is_string( $datetime ) || '' === $datetime ) {
			$datetime = get_the_date( DATE_W3C, $post );
		}

		$classes = array( 'wp-block-post-date', 'single-author-date', 'wp-block-post-date__modified-date' );
		if ( ! empty( $block['attrs']['textColor'] ) ) {
			$classes[] = 'has-text-color';
			$classes[] = 'has-' . sanitize_title( $block['attrs']['textColor'] ) . '-color';
		}

		$style_bits = array();
		if ( ! empty( $block['attrs']['style']['typography']['fontSize'] ) ) {
			$style_bits[] = 'font-size:' . $block['attrs']['style']['typography']['fontSize'];
		}
		if ( isset( $block['attrs']['style']['spacing']['margin']['top'] ) ) {
			$style_bits[] = 'margin-top:' . $block['attrs']['style']['spacing']['margin']['top'];
		}
		if ( isset( $block['attrs']['style']['spacing']['margin']['bottom'] ) ) {
			$style_bits[] = 'margin-bottom:' . $block['attrs']['style']['spacing']['margin']['bottom'];
		}
		$style_attr = $style_bits ? ' style="' . esc_attr( implode( ';', $style_bits ) ) . '"' : '';

		return sprintf(
			'<div class="%s"%s><time datetime="%s">%s</time></div>',
			esc_attr( implode( ' ', $classes ) ),
			$style_attr,
			esc_attr( $datetime ),
			esc_html( $display )
		);
	},
	10,
	2
);

/**
 * Sidebar “Relacionados” — reforço do nativo excludeCurrent (WP 7.1+).
 *
 * O core usa get_the_ID(); no canvas/FSE às vezes o ID está só no queried object.
 * className no query NÃO chega ao filtro (build usa o bloco com context, não o
 * wrapper com className) — por isso o match por class falhava e o self entrava.
 */
add_filter(
	'query_loop_block_query_vars',
	function ( array $query, $block ): array {
		$q = array();
		if ( $block instanceof WP_Block ) {
			$q = $block->context['query'] ?? ( $block->attributes['query'] ?? array() );
		}

		// Relacionados: excludeCurrent.
		if ( ! empty( $q['excludeCurrent'] ) ) {
			$id = (int) get_the_ID();
			if ( $id <= 0 ) {
				$id = (int) get_queried_object_id();
			}
			if ( $id > 0 ) {
				$exclude               = isset( $query['post__not_in'] ) ? (array) $query['post__not_in'] : array();
				$exclude[]             = $id;
				$query['post__not_in'] = array_values( array_unique( array_map( 'intval', $exclude ) ) );
			}
		}

		/*
		* Archive featured/cards: inherit:false + perPage/offset (offset some com inherit).
		* Sem tax_query explícito no pattern — o filtro injeta o termo/autor/data atuais.
		* Não usar className (não chega ao filtro; ver excludeCurrent acima).
		*/
		if ( empty( $q['inherit'] ) && ( is_category() || is_tag() || is_tax() || is_author() || is_date() ) ) {
			$already =
			! empty( $query['cat'] ) ||
			! empty( $query['tag_id'] ) ||
			! empty( $query['tax_query'] ) ||
			! empty( $query['author'] ) ||
			! empty( $query['year'] );
			if ( ! $already ) {
				if ( is_category() ) {
					$query['cat'] = (int) get_queried_object_id();
				} elseif ( is_tag() ) {
					$query['tag_id'] = (int) get_queried_object_id();
				} elseif ( is_tax() ) {
					$term = get_queried_object();
					if ( $term instanceof WP_Term ) {
						$query['tax_query'] = array(
							array(
								'taxonomy' => $term->taxonomy,
								'field'    => 'term_id',
								'terms'    => (int) $term->term_id,
							),
						);
					}
				} elseif ( is_author() ) {
					$query['author'] = (int) get_queried_object_id();
				} elseif ( is_date() ) {
					global $wp_query;
					foreach ( array( 'year', 'monthnum', 'day' ) as $key ) {
						if ( ! empty( $wp_query->query_vars[ $key ] ) ) {
							$query[ $key ] = $wp_query->query_vars[ $key ];
						}
					}
				}
			}
		}

		return $query;
	},
	10,
	2
);

/**
 * Archive hero title — proto accent na última parte após "+" (query-title é texto plano).
 * Só no front; o bloco no editor permanece nativo.
 */
add_filter(
	'render_block_core/query-title',
	function ( string $html ): string {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return $html;
		}
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return $html;
		}
		return (string) preg_replace_callback(
			'/(<h1\b[^>]*>)(.*?)(<\/h1>)/ius',
			static function ( array $m ): string {
				$plain = trim( html_entity_decode( wp_strip_all_tags( $m[2] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
				if ( ! preg_match( '/^(.+\+)\s+(.+)$/u', $plain, $parts ) ) {
					return $m[0];
				}
				return $m[1]
				. esc_html( trim( $parts[1] ) )
				. '<br><span class="text-accent">'
				. esc_html( trim( $parts[2] ) )
				. '</span>'
				. $m[3];
			},
			$html,
			1
		);
	}
);

/**
 * Meta do archive: "N artigos" em vez de "N resultados encontrados" (query-total i18n).
 */
add_filter(
	'render_block_core/query-total',
	function ( string $html ): string {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return $html;
		}
		return (string) preg_replace(
			'/(\d+)\s+resultados?\s+encontrados?/iu',
			'$1 artigos',
			$html
		);
	}
);
