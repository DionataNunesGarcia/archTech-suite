# Guia de Criação de Recipes

Cada bounded context do ArchTech é empacotado como uma Drupal Recipe independente.

## Estrutura

```
recipes/archtech_{context}/
├── recipe.yml          # Obrigatório
├── composer.json       # Obrigatório
├── config/             # Config YAML para import
│   └── actions/        # Config action plugins (opcional)
└── content/            # Default content YAML (opcional)
```

## recipe.yml Template

```yaml
name: 'ArchTech {Context Name}'
description: '{Descrição do bounded context}'
type: 'Site'
recipes:
  - archtech_base
install:
  - { module_custom }
config:
  strict: false
  import:
    { module }:
      - { config_entity }
  actions:
    { config_object }:
      simpleConfigUpdate:
        { key }: { value }
```

## Regras

- Nunca importar recipe de outro squad diretamente
- Comunicação cross-context via `archtech_events` (RabbitMQ)
- Todo field, content type e taxonomy em config YAML
- Usar `#config_target` para `ConfigFormBase`
- `composer.json` com dependências de módulos

## Recipes existentes

### Base (do drupal-recipes-base)

| Recipe                 | Descrição                   |
| ---------------------- | --------------------------- |
| `base_core`            | Configuração core do Drupal |
| `base_admin`           | Admin UI (Gin, toolbar)     |
| `base_media`           | Media types e library       |
| `base_seo`             | SEO (metatag, sitemap)      |
| `base_i18n`            | Internacionalização         |
| `base_pt_br`           | Português BR                |
| `base_es`              | Espanhol                    |
| `base_theme`           | Tema Tailwind               |
| `base_theme_bootstrap` | Tema Bootstrap              |
| `base_lp`              | Landing pages (paragraphs)  |
| `base_contents`        | Content types               |
| `base_courses`         | Cursos                      |
| `base_ai`              | AI Core (OpenAI)            |
| `base_ai_contents`     | AI Content automation       |
| `base_ai_search`       | AI Search (pgvector)        |
| `base_menus`           | Menu links                  |

### ArchTech (bounded contexts)

| Recipe                   | Contexto           | Depende de     |
| ------------------------ | ------------------ | -------------- |
| `archtech_base`          | Plataforma base    | —              |
| `archtech_client_portal` | Portal do Cliente  | base           |
| `archtech_crm`           | CRM                | base           |
| `archtech_proposals`     | Propostas          | base           |
| `archtech_financeiro`    | Financeiro         | base           |
| `archtech_library`       | Biblioteca Técnica | base           |
| `archtech_permits`       | Aprovação          | base + library |
| `archtech_suppliers`     | Fornecedores       | base           |
| `archtech_facilities`    | Facilities         | base           |
| `archtech_bim_twin`      | BIM Digital Twin   | base           |
