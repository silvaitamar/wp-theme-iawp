# Guia para agentes de IA — tema IA+WP (`iawp`)

Instruções para assistentes (Cursor e similares) que alteram este tema.

**Idioma:** responder em **português do Brasil**, salvo pedido contrário.

Índice: [README.md](README.md). Contrato curto na raiz: [`../AGENTS.md`](../AGENTS.md).

---

## 1. O que é este projeto

| Campo | Valor |
|-------|--------|
| Site | https://iaewp.com.br — hub editorial IA + WordPress |
| Tema | Block theme FSE `iawp` |
| Studio | `Studio/ia-wordpress` |
| Conteúdo das páginas principais | No **banco** (Home/Sobre via patterns L0) |
| Tema (código) | Tokens, CSS/JS, patterns, PHP |

---

## 2. Classificar a tarefa

| Tipo de pedido | Onde agir | Doc |
|----------------|-----------|-----|
| Textos, posts, newsletter copy | Conteúdo WP / orientação | [EDITORES.md](EDITORES.md) |
| Nova seção / markup de bloco | `patterns/` | [DESENVOLVEDORES.md](DESENVOLVEDORES.md) |
| Visual, CSS, JS, PHP, theme.json | `theme.json`, `assets/`, `functions.php` | [DESENVOLVEDORES.md](DESENVOLVEDORES.md) · [ARCHITECTURE.md](ARCHITECTURE.md) |
| Header/footer/templates FSE | `parts/`, `templates/` + consciência BD | [ARCHITECTURE.md](ARCHITECTURE.md) |
| Deploy, plugins, cache, Studio | Ops | [ECOSSISTEMA.md](ECOSSISTEMA.md) |

Não misturar conteúdo e refactor de tema num único passo opaco.

---

## 3. Regras obrigatórias

1. **Preservar o aspecto visual aprovado em produção** por default. Mudança visual = gate C.5 (proto×front) + OK humano.
2. **Não editar o core** nem plugins de terceiros. Trabalhar no tema `iawp`.
3. **`functions.php`** hoje é monólito; código novo preferir módulos claros (Onda 2: `inc/`). Não espalhar lógica em patterns PHP além do header do pattern.
4. **PHP / WPCS (sempre):** WordPress Coding Standards — escape (`esc_*` / `wp_kses_*`), prefixo `iawp_`, `ABSPATH` em includes, sanitize/nonce se houver input. Ver `Studio/.gutenlab/php-standards.md`. Não adiar “para CI/wp.org”. Se existir `phpcs.xml.dist`, rodar nos paths tocados antes do commit PHP.
5. **`style.css`** = metadados + chrome global. Seções em `assets/blocks/`.
6. **Tokens** via presets `theme.json`. Não reintroduzir `margin: 0` global em `core/paragraph` (mata blockGap da prosa).
7. **Classes de pattern em uso** = API pública (`.hero`, `.posts-hub`, `.post-featured`, `.single-prose`, …).
8. **Ícones:** `core/icon` + `iawp/*`. Não `core/html` para SVG de ícone. Hero-bg `core/html` = exceção documentada.
9. **CTA:** Receber novos artigos — nunca Lista VIP / curso / waitlist.
10. **Wordmark:** `<em>IA</em>+WP`.
11. **Segredos:** nunca no tema/docs.
12. **Git / deploy / push:** só com pedido explícito. Prod = fonte da verdade de conteúdo; deploy de tema seletivo (arquivo a arquivo), não sync Studio→prod de posts.
13. **Home:** L0 `iawp/page-home` + template `page-no-title`. **Não** recriar hub em `templates/front-page.html`.

---

## 4. Skills e inventário

| Recurso | Uso |
|---------|-----|
| `gutenlab-conversao-gutenberg` | Conversão / fidelidade / erros recorrentes |
| `wp-block-themes` | theme.json / FSE |
| `ambiente-local` | WSL, C-08 commit, C-15/C-16 Studio |
| `wordpress` | CLI remoto seletivo |
| `Studio/.gutenlab/` | PROMPT, blocks, maturity, gaps |
| Vault `04-ia-wordpress/` | design-system, arquitetura, pendências |

---

## 5. Checks pós-mudança

- Front: home, um single, Sobre (desktop; mobile se CSS tocado).
- Editor: canvas da superfície tocada (sem “Tentar restaurar” novo).
- Se CSS/JS relevante: bump `Version` em `style.css` (prática).
- Se PHP tocado: checklist WPCS (`php-standards.md`); PHPCS se config existir.
- Patterns novos: limpar cache de patterns do tema no Studio.
- Anti-freeze: um `studio wp` por vez; sem Grep em `plugins/`.
