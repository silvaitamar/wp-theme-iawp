# Guia para editores — hub IA+WP

Operar conteúdo **sem** editar código do tema.

Site: [iaewp.com.br](https://iaewp.com.br) · Admin: `/wp-admin/`

---

## 1. Papéis do site

| Superfície | O que é | Onde editar |
|------------|---------|-------------|
| Home | Hub editorial (hero, últimos, eixos, sobre teaser, newsletter) | Páginas → Home (template **Página sem título**) |
| Sobre | Página do autor | Páginas → Sobre |
| Artigos | Posts do blog | Posts → editar / novo |
| Arquivo / categorias | Listagens | Automático pelo tema + categorias |
| Privacidade / Termos | Legais | Páginas legais (template Legal) |

CTA canônico do hub: **Receber novos artigos** (formulário Jetpack na home / sidebar / padrões de CTA). Não usar linguagem de Lista VIP ou curso.

---

## 2. Home e Sobre (importante)

A composição visual da Home e da Sobre vem de **padrões de bloco** do tema embutidos no conteúdo da página.

- Template da página: **Página sem título** (`page-no-title`) — o H1/chrome do hub estão nos patterns, não no título da página sozinho.
- Evite apagar grupos grandes “para limpar”; isso remove seções inteiras do hub.
- Para ajustes de texto/imagem: edite o bloco correspondente no editor da página.
- Para mudanças estruturais (nova seção, ordem), peça a um desenvolvedor / agente com acesso ao tema (`patterns/`).

---

## 3. Artigos (single)

1. Posts → Adicionar / Editar.
2. Título, conteúdo em blocos, imagem destacada (**21:9** recomendado para o tema).
3. Categoria (ex.: IA + Gutenberg, IA + Agentes, Estratégia…).
4. Resumo (excerpt) — usado em listagens.
5. SEO: painel The SEO Framework (título e descrição) se disponível.
6. Imagem social / WhatsApp: fluxo editorial separado (OG 1200×630) — não basta a destacada 21:9.

Espaçamento entre parágrafos e títulos no single é do tema (`theme.json`). Não force margens manuais em cada parágrafo salvo exceção pontual.

---

## 4. Newsletter

- Bloco de formulário Jetpack nos padrões de CTA / sidebar.
- Copy alinhado a “Receber novos artigos”.
- Automação MailerLite (se existir) é ops — ver [ECOSSISTEMA.md](ECOSSISTEMA.md); não exige mudança no tema.

---

## 5. Site Editor (cuidado)

Aparência → Editor:

- Header / footer = template parts do tema (podem estar customizados no banco).
- Alterações globais de tipografia/cores afetam o hub inteiro — preferir pedir revisão técnica.
- Se o editor pedir “Tentar restaurar” em um bloco, **não salvar** às cegas; avise quem mantém o tema.

---

## 6. Checklist rápido antes de publicar artigo

- [ ] Categoria correta  
- [ ] Featured 21:9  
- [ ] Excerpt preenchido  
- [ ] SEO title/description  
- [ ] OG WhatsApp (quando for o fluxo completo)  
- [ ] Preview no front (desktop)  
