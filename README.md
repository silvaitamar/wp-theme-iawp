# IA+WP Theme

Tema de blocos (FSE) do hub editorial [IA+WP](https://iaewp.com.br) — inteligência artificial, WordPress moderno e soberania digital.

**Não é** landing de curso nem waitlist. CTA canônico: **Receber novos artigos** (newsletter).

## Requisitos

- WordPress 6.7+
- PHP 8.0+
- Plugins recomendados (wp.org): [Jetpack](https://wordpress.org/plugins/jetpack/) (form da newsletter), [The SEO Framework](https://wordpress.org/plugins/autodescription/)

## Instalação (dev)

1. Clonar ou copiar esta pasta para `wp-content/themes/iawp/`
2. Ativar o tema
3. Fonte visual aprovada: protótipo HTML em `iawp-hub-proto` (até o port Gutenberg fechar)

## Estrutura

```text
iawp/
├── style.css          # chrome global + tokens de header
├── theme.json         # design tokens (cores, tipo, spacing, layout)
├── functions.php      # enqueue condicional, ícones SVG, llms.txt
├── templates/         # FSE (front-page, index, single, archive, 404, page…)
├── parts/             # header, footer
├── patterns/          # block patterns (hub) — fase de port
├── assets/
│   ├── blocks/        # CSS on-demand (hub, blog, single, author, cta…)
│   ├── fonts/         # Geist + Inter (locais)
│   ├── icons/         # Lucide SVG
│   ├── motion.js      # reveal + spotlight
│   └── scroll.js
└── llms.txt
```

## Princípios

- Preferir **blocos core** e padrões FSE atuais
- Custom CSS só onde o proto exige fidelidade
- Plugins: apenas gratuitos do diretório oficial, quando o core não cobrir
- Sem segredos, sem nomes de clientes no repositório

## Licença

GPL-2.0-or-later

## Autor

[Itamar Silva](https://itamarsilva.dev.br) · [ola@iaewp.com.br](mailto:ola@iaewp.com.br)
