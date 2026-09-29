# AGENTS.md — tema IA+WP (`iawp`)

Contrato curto para agentes que editam este tema. Não inventar convenções fora daqui e do `README.md`.

## Fonte da verdade visual

1. Protótipo aprovado: `Downloads/iawp-hub-proto/` (HTML/CSS/JS).
2. Tokens e decisões: vault `04-ia-wordpress/design-system.md` + `arquitetura.md`.
3. Identidade: punk + fashion (dark, minimal/brutal, cor como joia) — decisão `15-decisoes/iawp-identidade-punk-fashion`.

Fidelidade ao proto **antes** de “melhorias” inventadas.

## Stack

- Tema de **blocos** (FSE): `theme.json`, `templates/`, `parts/`, patterns.
- PHP: `functions.php` — enqueue **condicional** por template; ícones SVG inline; `llms.txt`.
- CSS: `style.css` = global; `assets/blocks/*.css` = seções.
- JS: `motion.js` (reveal + spotlight); respeitar `prefers-reduced-motion`.
- Preferir blocos **nativos**; Jetpack form / TSF só quando necessário (wp.org).
- Ícones de UI: **`core/icon`** + coleção `iawp/*` (`assets/icons/`). Não `core/html` para SVG de ícone.

## Nomenclatura

| Item | Valor |
|------|--------|
| Pasta / text domain | `iawp` |
| Theme Name | `IA+WP` |
| Prefixos PHP/handles | `iawp_` / `iawp-` |
| Wordmark | `<em>IA</em>+WP` (não `IA<strong>+</strong>WP`) |
| CTA | Receber novos artigos — **nunca** Lista VIP / curso |

## O que não fazer

- Não editar core WP
- Não reintroduzir CSS de landing de curso (`problem` / `benefits` / `contents`)
- Não commitar `.env`, dumps, ZIPs, paths de cliente
- Não ativar/deploy em produção sem OK humano explícito (`digital-vanguard` = rollback)
- Não inventar item “Clusters” na nav até haver mais de um cluster com posts

## Commits (marcos)

Ver Conventional Commits. Marcos típicos deste tema:

1. `chore: scaffold iawp block theme` — estrutura + git
2. `feat(chrome): align header and footer…` — parts
3. `feat: add newsletter pattern and sync proto tokens` — pattern + tokens
4. `feat: port hub home from approved HTML proto`
5. `feat: port single, archive, about, 404`
6. `chore: release x.y.z` — só com OK de testes

Após **adicionar/renomear** arquivos em `patterns/`, limpar cache de patterns do tema no Studio (`$theme->delete_pattern_cache()` + reload) se o inserter/template não enxergar o slug novo.

**Home (F5.10):** composição no `post_content` da página estática (`page_on_front`) via pattern L0 `iawp/page-home`; template de página `page-no-title`. **Não** usar `templates/front-page.html` neste hub — mesma abordagem da Sobre.

## Ambiente

Site Studio: `Studio/ia-wordpress`. WP-CLI: `studio wp … --path C:\Users\silva\Studio\ia-wordpress` (um comando por vez). Shell da máquina: skill `ambiente-local` (WSL default; commit = C-08).
