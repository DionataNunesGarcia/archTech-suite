# ArchTech Suite

Ecossistema de IA para escritórios de arquitetura. Backend Drupal 11 headless, frontend Next.js 15, 9 bounded contexts, 24 agentes de IA.

## Stack

| Camada          | Tecnologia                               |
| --------------- | ---------------------------------------- |
| Backend         | Drupal 11 (headless, JSON:API + GraphQL) |
| Frontend        | Next.js 15 (App Router, SSR/ISR)         |
| Banco           | PostgreSQL 18 (+ pgvector, PostGIS)      |
| Cache           | Redis 7+                                 |
| Message Broker  | RabbitMQ 3.13+                           |
| Orquestração IA | n8n self-hosted                          |

## Quick Start

```bash
ddev start
ddev composer install
ddev install all
```

Acessar: `https://archtech.ddev.site:8443`

## Comandos DDEV

| Comando                                | Descrição                           |
| -------------------------------------- | ----------------------------------- |
| `ddev install [contexto]`              | Instala Drupal com bounded contexts |
| `ddev recipe-apply <nome>`             | Aplica recipe individual            |
| `ddev theme-{build,dev,watch,install}` | Compila o tema frontend             |
| `ddev code-review`                     | phpcs + phpstan (lint)              |
| `ddev code-fix`                        | phpcbf (autofix)                    |

## Bounded Contexts

| Contexto             | Módulo             | Squad              |
| -------------------- | ------------------ | ------------------ |
| Client Portal        | `ia_client_portal` | Portal do Cliente  |
| Architecture CRM     | `ia_crm`           | CRM                |
| Commercial Proposals | `ia_proposals`     | Propostas          |
| Financial Management | `ia_financeiro`    | Financeiro         |
| Technical Library    | `ia_library`       | Biblioteca Técnica |
| Permit Approval      | `ia_permits`       | Aprovação          |
| Supplier Management  | `ia_suppliers`     | Fornecedores       |
| Facilities           | `ia_facilities`    | Facilities         |
| BIM Digital Twin     | `ia_bim_twin`      | BIM                |

## Documentação

| O quê               | Onde                                            |
| ------------------- | ----------------------------------------------- |
| Índice completo     | `docs/README.md`                                |
| PRD                 | `archtech-prd.md`                               |
| API Specs (OpenAPI) | `docs/api-specifications/archtech-openapi.yaml` |
| ADRs                | `docs/adr/`                                     |
| Threat Model        | `docs/security/threat-model.md`                 |
| Backlog             | `docs/backlog-sprint-1-3.md`                    |
| Event Storming      | `docs/architecture/event-storming.md`           |
| Event Catalog       | `docs/data-contracts/event-catalog.md`          |
| Coding Standards    | `docs/guides/coding-standards.md`               |
| Auditoria           | `docs/audit-checklist.md`                       |

## Skills de IA

24 skills registradas em `.agents/skills/` para agentes dos bounded contexts + desenvolvimento Drupal + workflow. Consulte `AGENTS.md` para a lista completa.

## Licença

Proprietária — ArchTech Suite
