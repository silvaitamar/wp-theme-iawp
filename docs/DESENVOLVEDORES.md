# Documentação técnica — desenvolvedores

Guia para quem mantém o tema **IA+WP** (`iawp`, block theme / FSE).

**Público:** desenvolvedores WordPress.  
**Conteúdo:** [EDITORES.md](EDITORES.md).  
**Ops / plugins:** [ECOSSISTEMA.md](ECOSSISTEMA.md).  
**Agentes:** [AGENTES.md](AGENTES.md).  
**Arquitetura:** [ARCHITECTURE.md](ARCHITECTURE.md).

---

## 1. Visão geral

| Item | Valor |
|------|--------|
| Tipo | Block theme (FSE) |
| Path | `wp-content/themes/iawp/` |
| Site prod | https://iaewp.com.br |
| Studio | `Studio/ia-wordpress` · tipicamente `http://localhost:8886/` |
| Requires | WP 6.7+ · PHP 8.0+ |
| Text domain | `iawp` |
| CTA canônico | Receber novos artigos (nunca Lista VIP / curso) |

---

## 2. Árvore relevante

```
iawp/
├── style.css                 # Metadados + chrome global
├── theme.json                # Tokens + styles.blocks
├── functions.php             # Bootstrap: require_once inc/*
├── inc/
│   ├── style-surfaces.php    # Version helper + lista editor styles
│   ├── setup.php             # theme supports + add_editor_style + BHB canvas
│   ├── icons.php             # coleção core/icon iawp/* (+ grain/grad-brand)
│   ├── assets.php            # enqueue front condicional, footer, perf
│   ├── blocks.php            # render_block / query filters
│   ├── head-seo.php          # llms.txt, head cleanup, redirects
│   └── social.php            # social-link services + editor JS
├── AGENTS.md · README.md
├── docs/                     # Esta suite
├── patterns/                 # L0 page-* + L1 seções
├── templates/                # index, archive, single, 404, page, page-no-title, page-legal
├── parts/                    # header, footer
├── assets/
│   ├── blocks/               # hub, blog, single, cta, legal, error404
│   ├── fonts/geist|inter/
│   ├── icons/                # coleção core/icon
│   ├── motion.js · back-to-top.js · jetpack-form-ux.js
│   ├── editor-canvas.css · editor-grad-brand.js · social-variations.js
│   └── media/
└── llms.txt                  # manifesto do hub (conteúdo/GEO), não arquitetura
```

**Ausente por desenho (por agora):** `styles/` variations, `languages/`, `phpcs.xml.dist` (Onda 5).

**PHP / WPCS (sempre):** prefixo `iawp_`; escape na saída; `ABSPATH` em includes; enqueue só via APIs WP. Consolida: `Studio/.gutenlab/php-standards.md`. Código novo → `inc/`, não inchando `functions.php`.

---

## 3. Tokens (`theme.json`)

- Palette: `base`, `surface*`, `contrast`, `muted`, `dim`, `primary`, `secondary`, `outline`
- Spacing: slugs `10`…`80` (ver nomes no JSON)
- Tipografia: Geist (display) + Inter (body); font sizes `2xs`…`page` (inclui `2xl` legado — não migrar sem plano)
- Layout: `contentSize` 680px · `wideSize` 1344px · RPA ligado
- Prosa (single): `core/post-content` blockGap `|30`; `core/heading` margin top `|48` / bottom `|20`; **sem** zerar margin de `core/paragraph` (mata o gap)

---

## 4. CSS e enqueue

| Contexto | Como |
|----------|------|
| Front | `inc/assets.php` — conditional tags por queried object (home, blog, single, sobre, legal, 404, archive) |
| Editor | `iawp_editor_style_files()` → `add_editor_style` (hub.css **por último**) |
| BHB | `enqueue_block_assets` para header overlay no canvas |
| Cache-bust | `filemtime` por arquivo no front |
| Release | bump `Version` em `style.css` após CSS/JS relevantes (`iawp_theme_version()`) |

### Matriz front (não alterar sem smoke)

| Superfície | Folhas |
|------------|--------|
| Home | `hub` + `cta` (**sem** `blog`) |
| Blog / archive | `hub` + `blog` + `cta` |
| Single | `single` + `cta` |
| Sobre | `hub` + `blog` + `cta` |
| Legal | `legal` |
| Outras pages | `blog` (page-hero) |
| 404 | `error404` |

### `wp_enqueue_block_style` — avaliação Onda 3 (embasamento)

A Onda 3 pedia **avaliar** onde a API cabe **sem** regressão do mapa condicional — não “proibir para sempre”.

| Fonte | O que diz |
|-------|-----------|
| `inc/assets.php` (histórico iawp) | Bundle/`render_block` on-demand **já falhou** em FSE: template renderiza **depois** de `wp_head` → `<link>` não entra no head. Por isso o mapa por `queried_object` / conditional tags. |
| Screenshot `inc/assets/block-styles.php` | Usa `wp_enqueue_block_style` para CSS **amarrado a um tipo de bloco** (`core/image` logo rodapé, `core/embed`, `core/media-text`, GutSlider, Fluent, WPM…) — **não** para folhas de superfície de página (`hub.css` / “toda a home”). |
| `.gutenlab/PROMPT.md` | Limitação da API: enfileira por **tipo** de bloco, não por instância. Mapear `hub.css` → `core/group` carregaria hub em **qualquer** página com Group. |
| `.gutenlab/css-delivery.md` | Foco = **1 request** (bundle) / race FSE; é eixo de delivery, não “trocar condicional por block_style”. |

**Conclusão desta avaliação (superfícies atuais):**

- `hub.css`, `blog.css`, `single.css`, `cta.css`, `legal.css`, `error404.css` são **CSS de superfície/template** (matriz home≠blog: home **não** pode levar `blog.css`). Isso **não** mapeia 1:1 para um `block_type` sem regressão ou sem voltar a detectar seções tarde demais.
- **Manter** o enqueue condicional para essas folhas.
- **Candidato futuro** (só se surgir arquivo CSS realmente por bloco, modelo Screenshot): `wp_enqueue_block_style` **em paralelo**, sem substituir o mapa de superfície. Hoje o tema **não tem** esse tipo de folha em `assets/blocks/` (só superfícies).

Não confundir com o late-load de `iawp-cta` / Jetpack via `style_loader_tag` (perf P0) — isso já é on-demand de *quando* o CSS aplica, não troca de API de superfície.

### Allowlist CSS residual (não é “todo CSS livre”)

Permitido fora de attrs nativos / `theme.json`: wash/grain/reveal, hero-bg escape, chrome header/footer, motion, Jetpack form overrides pontuais (`!important` só contra Jetpack), editor-canvas traps. **Proibido:** zerar `margin` de `core/paragraph` no `theme.json`; reinventar gap/padding nativo na classe.

---

## 5. Patterns

| Tipo | Exemplos | Uso |
|------|----------|-----|
| L0 página | `iawp/page-home`, `page-sobre`, `page-blog`, `page-single`, `page-archive`, `page-404` | Composição da página no BD |
| L1 seção | hero, latest, bento, about-teaser, newsletter, single-*, … | Inserter / composição L0 |

- Slug: `iawp/…`
- Classes em uso (`.hero`, `.posts-hub`, `.post-featured`, `.single-prose`, …) = **API pública** — não renomear sem migrar conteúdo
- Após add/rename em `patterns/`: invalidar cache de patterns do tema no Studio

---

## 6. PHP

`functions.php` = bootstrap (`require_once` de `inc/*`). Módulos: `style-surfaces`, `setup`, `icons`, `assets`, `blocks`, `head-seo`, `social`. Código novo → `inc/`. WPCS sempre (`Studio/.gutenlab/php-standards.md`).

---

## 7. Fidelidade e skills

- Proto: `Downloads/iawp-hub-proto/`
- Design system: vault `04-ia-wordpress/design-system.md`
- Conversão / gates: skill `gutenlab-conversao-gutenberg` + inventário `Studio/.gutenlab/`
- Visual prod aprovado → não “melhorar” visual sem C.5 + OK humano

---

## 8. Git

Conventional Commits. Commit só com pedido explícito. Receita shell: skill `ambiente-local` C-08.
