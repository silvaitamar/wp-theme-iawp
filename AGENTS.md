# AGENTS.md — tema IA+WP (`iawp`)

Contrato **curto**. Detalhe obrigatório: [`docs/AGENTES.md`](docs/AGENTES.md). Índice: [`docs/README.md`](docs/README.md).

## Fonte da verdade

1. Visual **produção** aprovado (congelar por default). Proto: `Downloads/iawp-hub-proto/`.
2. Tokens / identidade: vault `04-ia-wordpress/design-system.md` · decisão punk+fashion.
3. Inventário conversão/maturidade: `Studio/.gutenlab/` (ex. `maturity-iawp-2026-10.md`).

## Stack

- FSE: `theme.json`, `templates/`, `parts/`, `patterns/`
- PHP: `functions.php` bootstrap → `inc/{setup,icons,assets,blocks,head-seo,social}.php`; **WPCS sempre** (`.gutenlab/php-standards.md`)
- CSS: `style.css` global · `assets/blocks/*` por superfície · `editor-canvas.css` no canvas
- JS: `motion.js`, `back-to-top.js`; `prefers-reduced-motion`
- Ícones: `core/icon` + `iawp/*` — não `core/html` para ícone
- CTA: **Receber novos artigos** — nunca Lista VIP / curso

## Home

Pattern L0 `iawp/page-home` no `post_content` da página estática + template `page-no-title`.  
**Não** usar `templates/front-page.html` para o hub.

## Não fazer

- Editar core / plugins de terceiros
- Reintroduzir CSS de landing de curso
- Commitar segredos / dumps / paths de cliente
- Deploy/push sem pedido explícito
- Mudança visual sem C.5 + OK humano
- Zerar margin de `core/paragraph` no `theme.json` (mata blockGap da prosa)
- PHP sem escape/prefixo/`ABSPATH` (adiar WPCS “para CI” é regressão)

## Ambiente

Studio: `Studio/ia-wordpress`. Shell: skill `ambiente-local` (WSL; commit C-08).  
Prod: wp-remote `ia-wp` — conteúdo = fonte da verdade; tema = deploy seletivo ([docs/ECOSSISTEMA.md](docs/ECOSSISTEMA.md)).
