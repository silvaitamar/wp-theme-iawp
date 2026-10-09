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
├── functions.php             # Bootstrap atual (monólito → Onda 2: inc/)
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

**Ausente por desenho (por agora):** `inc/` (Onda 2), `styles/` variations, `languages/`, `phpcs.xml.dist` (Onda 5).

**PHP / WPCS (sempre):** prefixo `iawp_`; escape na saída; `ABSPATH` em includes; enqueue só via APIs WP. Consolida: `Studio/.gutenlab/php-standards.md`. CI Theme Check / wp.org = só se houver publish — a disciplina não espera CI.

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
| Front | `wp_enqueue_scripts` + conditional tags (home, blog, single, sobre, legal, 404, archive) |
| Editor | `add_editor_style` de `style.css`, `editor-canvas.css` e `assets/blocks/*.css` |
| BHB | `enqueue_block_assets` para header overlay no canvas |
| Versionamento assets | `filemtime` por arquivo; **também** bump `Version` em `style.css` após CSS/JS relevantes (prática Onda 3) |

Home **não** carrega `blog.css` sozinho no front (evita colisão `.post-featured` / `.post-card`).

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

## 6. PHP (estado atual)

Tudo em `functions.php` (~1060 linhas): setup, enqueue, ícones/`core/icon`, inliner legado span→SVG, head/SEO/`llms.txt`, query loop archive, sociais, etc.

Alvo Onda 2 (sem mudança de comportamento):

- `inc/setup.php` · `inc/assets.php` · `inc/icons.php` · `inc/head-seo.php` · `inc/social.php`
- `functions.php` = só `require_once`

---

## 7. Fidelidade e skills

- Proto: `Downloads/iawp-hub-proto/`
- Design system: vault `04-ia-wordpress/design-system.md`
- Conversão / gates: skill `gutenlab-conversao-gutenberg` + inventário `Studio/.gutenlab/`
- Visual prod aprovado → não “melhorar” visual sem C.5 + OK humano

---

## 8. Git

Conventional Commits. Commit só com pedido explícito. Receita shell: skill `ambiente-local` C-08.
