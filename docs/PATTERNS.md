# Inventário de patterns — tema `iawp`

> Onda 5A (2026-10-12). Fonte: arquivos em `patterns/*.php`.
> Classes em uso = API pública — ver [DESENVOLVEDORES.md](DESENVOLVEDORES.md) §5.

## Resumo

| | Contagem |
|--|----------|
| Total | 44 |
| L0 (página) | 6 (`page-*`) |
| L1 (seção / query / card) | 38 |
| Com `Block Types` no header | 0 |
| Com `Inserter: no` explícito | 0 (vários L0/L1 têm `Inserter: yes` explícito) |

## Gaps (hygiene — não fechar sem OK)

1. **L0 no Inserter:** `page-*` estão com `Inserter: yes`. Padrão advanced-64/GutenLab: composições de página internas costumam `Inserter: no`. Avaliar em leva dedicada — header só afeta o que o editor oferece, não o `post_content` em prod.
2. **`Block Types`:** nenhum pattern declara tipos. Opcional; útil para sync CBT.
3. **Peças internas** (`bento-item`, `query-*`, `section-header*`): candidatos a `Inserter: no` se só forem compostos por pais.

## Tabela

| Nível | Slug | Title | Categories | Block Types | Inserter | Arquivo |
|-------|------|-------|------------|-------------|----------|---------|
| L1 | iawp/about-author | About author | about, featured | — | default | about-author.php |
| L1 | iawp/about-hero | About hero | about, featured, banner | — | default | about-hero.php |
| L1 | iawp/about-hub | About hub | about, featured | — | default | about-hub.php |
| L1 | iawp/about-teaser | About teaser | about, featured | — | default | about-teaser.php |
| L1 | iawp/archive-hero | Archive hero | featured, banner | — | default | archive-hero.php |
| L1 | iawp/archive-latest | Archive latest | posts, featured | — | default | archive-latest.php |
| L1 | iawp/article-cta | Article CTA | call-to-action, featured | — | default | article-cta.php |
| L1 | iawp/author-social | Author social | about, featured | — | default | author-social.php |
| L1 | iawp/bento-grid | Bento grid | featured, about | — | default | bento-grid.php |
| L1 | iawp/bento-item | Bento item | featured, about | — | default | bento-item.php |
| L1 | iawp/blog-hero | Blog hero | featured, banner | — | default | blog-hero.php |
| L1 | iawp/blog-latest | Blog latest | posts, featured | — | default | blog-latest.php |
| L1 | iawp/error-404 | Error 404 | featured, banner | — | default | error-404.php |
| L1 | iawp/home-about | Home about | about, featured | — | default | home-about.php |
| L1 | iawp/home-eixos | Home eixos | featured, about | — | default | home-eixos.php |
| L1 | iawp/home-hero | Home hero | featured, banner | — | default | home-hero.php |
| L1 | iawp/home-latest | Home latest | posts, featured | — | default | home-latest.php |
| L1 | iawp/newsletter | Newsletter | featured, call-to-action | — | default | newsletter.php |
| L0 | iawp/page-404 | Page — 404 | featured, pages | — | default | page-404.php |
| L0 | iawp/page-archive | Page — Archive | featured, pages | — | default | page-archive.php |
| L0 | iawp/page-blog | Page — Blog | featured, pages | — | default | page-blog.php |
| L0 | iawp/page-home | Page — Home | featured, pages | — | default | page-home.php |
| L0 | iawp/page-single | Page — Single | featured, pages | — | default | page-single.php |
| L0 | iawp/page-sobre | Page — Sobre | featured, pages, about | — | default | page-sobre.php |
| L1 | iawp/post-card | Post card | posts, featured | — | default | post-card.php |
| L1 | iawp/post-featured | Post featured | posts, featured | — | default | post-featured.php |
| L1 | iawp/posts-hub | Posts hub | posts, featured | — | default | posts-hub.php |
| L1 | iawp/query-cards-archive | Query cards (archive) | posts, featured | — | default | query-cards-archive.php |
| L1 | iawp/query-cards-blog | Query cards (blog) | posts, featured | — | default | query-cards-blog.php |
| L1 | iawp/query-cards | Query cards | posts, featured | — | default | query-cards.php |
| L1 | iawp/query-featured-archive | Query featured (archive) | posts, featured | — | default | query-featured-archive.php |
| L1 | iawp/query-featured | Query featured | posts, featured | — | default | query-featured.php |
| L1 | iawp/section-header-about-hub | Section header (About hub) | about, featured | — | default | section-header-about-hub.php |
| L1 | iawp/section-header-lede | Section header (lede) | featured | — | default | section-header-lede.php |
| L1 | iawp/section-header | Section header | featured | — | default | section-header.php |
| L1 | iawp/share-links | Share links | featured | — | default | share-links.php |
| L1 | iawp/sidebar-cta | Sidebar CTA | call-to-action, featured | — | default | sidebar-cta.php |
| L1 | iawp/sidebar-related | Sidebar related | posts, featured | — | default | sidebar-related.php |
| L1 | iawp/single-body | Single body | featured, posts | — | default | single-body.php |
| L1 | iawp/single-header | Single header | featured, banner | — | default | single-header.php |
| L1 | iawp/single-sidebar | Single sidebar | featured, call-to-action | — | default | single-sidebar.php |
| L1 | iawp/sobre-pillar-nao-e | Sobre pillar — O que não é | about, featured | — | default | sobre-pillar-nao-e.php |
| L1 | iawp/sobre-pillar-para-quem | Sobre pillar — Para quem | about, featured | — | default | sobre-pillar-para-quem.php |
| L1 | iawp/sobre-pillar | Sobre pillar — Tese | about, featured | — | default | sobre-pillar.php |

## Referências

- Starter [advanced-64](https://github.com/carolinan/fullsiteediting/tree/course/Starter%20themes/advanced-64) — exemplos de patterns + `inc/register-block-patterns.php`
- Plano: Onda 5A · backlog headers = não auto-aplicar sem OK humano
