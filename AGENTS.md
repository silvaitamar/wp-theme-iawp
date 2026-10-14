# AGENTS.md — tema IA+WP (`iawp`)

Contrato **curto**. Detalhe obrigatório: [`docs/AGENTES.md`](docs/AGENTES.md). Índice: [`docs/README.md`](docs/README.md).

## Fonte da verdade

1. Visual **produção** aprovado (congelar por default). Proto de fidelidade: `iawp-hub-proto` (local do autor).
2. Tokens / identidade: design system editorial do hub IA+WP (punk + fashion).
3. Inventário conversão/maturidade: notas GutenLab locais (fora deste repo).

## Stack

- FSE: `theme.json`, `templates/`, `parts/`, `patterns/`
- PHP: `functions.php` bootstrap → `inc/{style-surfaces,setup,icons,assets,blocks,head-seo,social}.php`; **WPCS sempre**
- CSS: front condicional (`inc/assets.php`); editor = `iawp_editor_style_files()`; bump `Version` após CSS/JS; folhas `style.css` / `assets/blocks/*` / `editor-canvas.css`
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

Dev: WordPress Studio (ou equivalente) com o tema em `wp-content/themes/iawp`.  
Prod: conteúdo = fonte da verdade; tema = deploy seletivo ([docs/ECOSSISTEMA.md](docs/ECOSSISTEMA.md)).
