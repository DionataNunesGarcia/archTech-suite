# ArchTech Suite — AI Agent Instructions

## Projeto

ArchTech Suite — ecossistema de IA para escritórios de arquitetura.
Backend Drupal 11 headless, frontend Next.js 15, 9 bounded contexts, 24 skills de IA.

## Documentação

| O quê | Onde |
|-------|------|
| PRD completo | `archtech-prd.md` |
| Features detalhadas | `archtech-prd-enhanced.xml` |
| Diagrama arquitetura | `archtech_architecture_overview.svg` |
| Índice da documentação | `docs/README.md` |
| **Coding Standards** | **`docs/guides/coding-standards.md`** |
| Guia de Recipes | `docs/guides/recipe-creation.md` |
| DDEV Setup | `docs/guides/ddev-setup.md` |
| Event Storming | `docs/architecture/event-storming.md` |
| ADRs | `docs/adr/` |
| API Specs (OpenAPI) | `docs/api-specifications/archtech-openapi.yaml` |
| Prompt Registry | `docs/ai-prompts/` |
| Data Contracts | `docs/data-contracts/event-catalog.md` |
| Threat Model | `docs/security/threat-model.md` |
| C4 Model | `docs/architecture/c4-model.md` |
| Backlog | `docs/backlog-sprint-1-3.md` |
| Runbooks | `docs/runbooks/` |

## Skills Disponíveis

### Agentes de IA do PRD (`.agents/skills/`)

| Skill | Descrição |
|-------|-----------|
| `drupal-recipes` | Criar/modificar Drupal Recipes |
| `ia-client-portal-notifier` | Notificações contextuais para clientes |
| `ia-client-portal-approval-reminder` | Lembretes de aprovação |
| `ia-crm-lead-scorer` | Scoring de leads (1-10) |
| `ia-crm-followup-scheduler` | Follow-up scheduling |
| `ia-proposals-generator` | Geração de propostas |
| `ia-proposals-contract-reviewer` | Revisão de contratos |
| `ia-financeiro-cashflow-predictor` | Forecast de fluxo de caixa |
| `ia-financeiro-collection-agent` | Cobrança automatizada |
| `ia-library-knowledge-retriever` | RAG (busca semântica) |
| `ia-library-document-classifier` | Classificação de documentos |
| `ia-permits-advisor` | Consultoria de alvarás |
| `ia-permits-document-generator` | Geração de documentos oficiais |
| `ia-suppliers-recommender` | Recomendação de fornecedores |
| `ia-suppliers-sla-monitor` | Monitoramento de SLA |
| `ia-facilities-maintenance-scheduler` | Manutenção preditiva |
| `ia-facilities-warranty-advisor` | Assessoria de garantias |
| `ia-bim-twin-integrator` | Processamento IFC |
| `ia-bim-twin-optimizer` | Análise de performance |

### Skills de Desenvolvimento Drupal (`.agents/skills/`)

| Skill | Descrição |
|-------|-----------|
| `drupal-backend` | Desenvolvimento de módulos, hooks, APIs, PHP |
| `drupal-frontend` | Desenvolvimento de temas, Twig, CSS/JS |

### Skills de Workflow (`.agents/skills/`)

| Skill | Descrição |
|-------|-----------|
| `find-skills` | Busca skills disponíveis |
| `frontend-design` | Design system e frontend |
| `skill-creator` | Criação e otimização de skills |

### Skills Externas (`skills-lock.json`)

Qualidade: `code-standards-checker`, `accessibility-checker`, `security-scanner`, `performance-analyzer`, `browser-validator`
Testes: `test-driven-development`, `test-plan-generator`, `test-scaffolding`, `coverage-analyzer`
Drupal: `drupal-config-mgmt`, `drupal-ddev`, `drupal-search-api`, `drupal-contrib-mgmt`
Workflow: `systematic-debugging`, `dispatching-parallel-agents`, `writing-plans`, `writing-skills`, `verification-before-completion`

## Ambiente

| Config | Valor |
|--------|-------|
| DDEV | `archtech` |
| PHP | 8.4 |
| Database | PostgreSQL 18 |
| Docroot | `web/` |
| URL | https://archtech.ddev.site |
