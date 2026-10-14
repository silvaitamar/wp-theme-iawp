# Ecossistema — tema IA+WP (`iawp`)

Plugins, ambientes e operação. Sem page builders.

---

## 1. Ambientes

| Ambiente | Path / URL | Papel |
|----------|------------|--------|
| Dev | WordPress Studio (ou equivalente) | Lab; tema em disco |
| Produção | `iaewp.com.br` | **Fonte da verdade de conteúdo** |
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

Em produção o CTA newsletter depende do form Jetpack. O lab Playground instala Jetpack + BHB + Social Sharing Block para reproduzir layout; envio real / conexão WordPress.com não são requisito do demo.

---

## 3. CLI

| Onde | Como |
|------|------|
| Dev (Studio) | `wp` / CLI do Studio no path do site — um comando por vez se a CLI for instável |
| Produção | WP-CLI remoto com cuidado em mutações; backup antes de leva |

SQLite do Studio “without an active database connection” → reparar com o site parado.

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

## 6. Pendências fora do tema

Backlog operacional do hub (Playground, push GitHub, i18n, etc.) vive nas notas internas do autor — não neste repo.

Maturidade estrutural do código: inventário GutenLab local (fora deste repo).
