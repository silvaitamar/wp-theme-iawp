# Arquitetura — tema IA+WP (`iawp`)

## Modelo de 4 camadas

```
Tokens (theme.json)
  → Comportamento (assets/ + functions.php bootstrap → inc/*)
    → Patterns (patterns/*.php)
      → Instância (BD: páginas Home/Sobre, templates FSE personalizados)
```

| Camada | Onde vive | Responsabilidade |
|--------|-----------|------------------|
| Tokens | `theme.json` | Cores, tipografia, spacing, layout, styles.blocks (ex. prosa) |
| Comportamento | `style.css`, `assets/blocks/*.css`, `assets/*.js`, `inc/*` | Chrome, CSS por superfície, motion, ícones, enqueue; PHP sob WPCS (`php-standards.md`) |
| Composição | `patterns/*.php` | Seções L1 + páginas L0 reutilizáveis |
| Instância | BD (Studio / prod) | `page_on_front` com L0 `iawp/page-home`; Sobre com `iawp/page-sobre`; overrides do Site Editor |

Produção = fonte da verdade de **conteúdo**. O tema no disco = tokens + comportamento + patterns.

## Home (decisão F5.10)

- **Não** usar `templates/front-page.html` como composição do hub.
- Página estática `page_on_front` + template de página `page-no-title` + pattern L0 `iawp/page-home` no `post_content`.
- Mesma abordagem para Sobre (`iawp/page-sobre`).

## CSS

| Folha | Papel |
|-------|--------|
| `style.css` | Metadados WP + chrome global (header/footer, icons, reveal, skip-link brand) |
| `assets/editor-canvas.css` | Só Site Editor (offset BHB, traps canvas) |
| `assets/blocks/hub.css` | Home / seções hub |
| `assets/blocks/blog.css` | Arquivo / cards (não na home sozinha) |
| `assets/blocks/single.css` | Single + prosa + sidebar |
| `assets/blocks/cta.css` | CTAs newsletter |
| `assets/blocks/legal.css` | Páginas legais |
| `assets/blocks/error404.css` | 404 |

Enqueue front: **condicional** por tipo de página em `inc/assets.php` (race FSE documentada). Editor: `iawp_editor_style_files()` → `add_editor_style`. Avaliação Onda 3: `wp_enqueue_block_style` = CSS **por tipo de bloco**; superfícies iawp ficam no mapa condicional (ver DESENVOLVEDORES §4).

## Natives first

Preferir atributos de bloco + presets `theme.json`. CSS residual só allowlist (documentada em audits GutenLab / headers dos CSS).  
Ícones de UI: `core/icon` + coleção `iawp/*`. Exceção documentada: `core/html` para hero-bg.

## Disco vs BD

Parts/templates no arquivo podem divergir do que o Site Editor salvou. Antes de sobrescrever `parts/` ou `templates/` a partir do disco, confirmar o que está ativo em prod/Studio.

## Próxima evolução estrutural

- Onda 2: `functions.php` → `inc/*` (bootstrap apenas na raiz).
- Onda 3 ✅: `Version` 0.1.1 + `inc/style-surfaces.php` + decisão block_style documentada.
- Maturidade estrutural: inventário GutenLab local (fora deste repo).
