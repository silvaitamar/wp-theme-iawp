# Ecossistema — tema IA+WP (`iawp`)

Plugins, ambientes e operação. Sem page builders.

---

## 1. Ambientes

| Ambiente | Path / URL | Papel |
|----------|------------|--------|
| Studio (dev) | `Studio/ia-wordpress` · `localhost:8886` (confirmar) | Lab; tema em disco |
| Produção | `iaewp.com.br` · wp-remote slug `ia-wp` | **Fonte da verdade de conteúdo** |
| Rollback tema | Tema `digital-vanguard` instalado inactive | Emergência visual |

**Proibido:** sync full de BD/uploads Studio → prod; `db import` em massa; full ZIP de tema “no escuro”.

**Permitido:** deploy **seletivo** de arquivos do tema (`theme.json`, CSS, PHP, patterns) via SCP + purge cache; updates pontuais de páginas Home/Sobre só com OK explícito.

---

## 2. Plugins relevantes (prod)

| Plugin | Papel no hub |
|--------|----------------|
| Jetpack | Forms (newsletter), Social (share) |
| The SEO Framework | Título, description, social image |
| Block Header Behavior (BHB) | Header overlay / scroll |
| LiteSpeed Cache | Cache / purge após CSS |
| UpdraftPlus | Backup (nunca dump manual se ativo) |
| Wordfence / Hostinger / Site Kit | Ops — não acoplar layout do tema |

Tema deve degradar sem Jetpack/TSF no lab; em prod o CTA newsletter depende do form Jetpack.

---

## 3. CLI

| Onde | Como |
|------|------|
| Studio | `studio wp … --path C:\Users\silva\Studio\ia-wordpress` — **um** comando por vez (C-15) |
| Produção | `wp-remote.sh` / MCP `wp_cli` — mutações com `--write` / `write=true` |
| Shell host | skill `ambiente-local` (WSL default; commit C-08) |

SQLite Studio quebrado (“without an active database connection”) → C-16 repair com site parado.

---

## 4. Deploy seletivo de tema (receita)

1. Alterar e validar no Studio (ou diff local).
2. SCP só dos arquivos tocados para `wp-content/themes/iawp/…` no servidor.
3. `litespeed-purge all` (ou equivalente).
4. Smoke: home, um single, Sobre — front; editor se a mudança afetar canvas.
5. Não publicar conteúdo de artigo nesta leva a menos que o pedido seja editorial.

Exemplo já usado: calibração de prosa (`theme.json` + `single.css`) sem ZIP full.

---

## 5. Disco vs Site Editor

- `parts/header.html` / `footer.html` e `templates/*` no git podem **não** ser o que o Site Editor tem salvo no BD.
- Antes de sobrescrever parts/templates em prod a partir do disco, confirmar overrides.
- Home/Sobre: composição no `post_content` da página (patterns L0), não só template HTML.

---

## 6. Pendências fora do tema (vault)

Lista canônica: vault `04-ia-wordpress/pendencias.md` — Playground (adiado), push GitHub do tema, i18n, OG de artigos, etc.

Maturidade estrutural do código: `Studio/.gutenlab/maturity-iawp-2026-10.md` + plano Cursor `melhorias_tema_iawp`.
