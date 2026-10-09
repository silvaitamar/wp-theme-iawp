# IA+WP Theme

Tema de blocos (FSE) do hub editorial [IA+WP](https://iaewp.com.br) — inteligência artificial, WordPress moderno e soberania digital.

**Não é** landing de curso nem waitlist. CTA canônico: **Receber novos artigos** (newsletter).

## Documentação

Suite completa em [`docs/`](docs/README.md):

| Público | Doc |
|---------|-----|
| Desenvolvedores | [docs/DESENVOLVEDORES.md](docs/DESENVOLVEDORES.md) |
| Editores | [docs/EDITORES.md](docs/EDITORES.md) |
| Agentes de IA | [docs/AGENTES.md](docs/AGENTES.md) · contrato curto [AGENTS.md](AGENTS.md) |
| Ops | [docs/ECOSSISTEMA.md](docs/ECOSSISTEMA.md) |
| Arquitetura | [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) |

## Requisitos

- WordPress 6.7+
- PHP 8.0+
- Plugins recomendados (wp.org): [Jetpack](https://wordpress.org/plugins/jetpack/) (form da newsletter), [The SEO Framework](https://wordpress.org/plugins/autodescription/)
- Header overlay: plugin Block Header Behavior (BHB), se usado no ambiente

## Instalação (dev)

1. Copiar esta pasta para `wp-content/themes/iawp/`
2. Ativar o tema
3. Home: página estática com template **Página sem título** + pattern L0 `iawp/page-home` no conteúdo (não depender de `front-page.html`)
4. Proto de referência: `iawp-hub-proto` (fidelidade visual); em produção o visual aprovado é a fonte operacional

## Estrutura

```text
iawp/
├── style.css             # metadados + chrome global
├── theme.json            # tokens + styles.blocks
├── functions.php         # enqueue, ícones, head (→ inc/ na Onda 2)
├── docs/                 # documentação por público
├── templates/            # index, archive, single, 404, page, page-no-title, page-legal
├── parts/                # header, footer
├── patterns/             # L0 page-* + L1 seções
├── assets/
│   ├── blocks/           # hub, blog, single, cta, legal, error404
│   ├── fonts/            # Geist + Inter (locais)
│   ├── icons/            # coleção core/icon
│   ├── motion.js         # reveal + spotlight
│   └── back-to-top.js
└── llms.txt              # manifesto GEO do hub
```

## Princípios

- Preferir **blocos core** e padrões FSE
- CSS residual só onde o core não chega (allowlist)
- Plugins: diretório oficial quando o core não cobre
- Sem segredos; classes de pattern em uso = API pública
- Deploy em produção: seletivo + OK humano ([docs/ECOSSISTEMA.md](docs/ECOSSISTEMA.md))

## Licença

GPL-2.0-or-later

## Autor

[Itamar Silva](https://itamarsilva.dev.br) · [ola@iaewp.com.br](mailto:ola@iaewp.com.br)
