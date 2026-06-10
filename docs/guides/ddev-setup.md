# DDEV Setup

## Requisitos

- Docker
- DDEV >= 1.24

## Comandos Disponíveis

### Instalação

```bash
ddev install                         # Instala Drupal + base recipes
ddev install crm                     # + bounded context CRM
ddev install client-portal proposals # + múltiplos contexts
ddev install all                     # + todos os bounded contexts
```

### Recipes

```bash
ddev recipe-apply archtech_crm       # Aplica recipe específica
```

### Qualidade

```bash
ddev code-review [caminho]          # phpcs + phpstan + cspell
ddev code-fix [caminho]             # phpcbf
```

### Tema

```bash
ddev theme-install                   # npm install do tema
ddev theme-build                     # Build produção
ddev theme-dev                       # Build dev
ddev theme-watch                     # Watch mode
```

### Utilitários

```bash
ddev hook <function>                 # Executa hook Drupal
ddev uli                             # Login URL
ddev ai                              # Comandos de IA
```

## Ambiente

| Config    | Valor                      |
| --------- | -------------------------- |
| PHP       | 8.4                        |
| Database  | PostgreSQL 18              |
| Webserver | nginx-fpm                  |
| Docroot   | `web/`                     |
| URL       | https://archtech.ddev.site |
