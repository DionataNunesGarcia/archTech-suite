# ArchTech Suite — Auditoria de Entregas por Fase

Checklist consolidado de todas as entregas do projeto, por fase. Use este arquivo para rastrear o progresso e auditar o que está pronto.

---

## Fase 1 — Descoberta e Arquitetura ✅

| # | Tarefa | Status | Entregável | Localização |
|---|--------|--------|-----------|-------------|
| 1.1 | Event Storming | ✅ | Mapa de contexts, aggregates, eventos e fluxos | `docs/architecture/event-storming.md` |
| 1.2 | ADRs | ✅ | 7 ADRs (stack, frontend, broker, workflows, banco, contexts, APIs) | `docs/adr/ADR-*.md` |
| 1.3 | Modelagem de dados | ✅ | 45 content types em 10 contexts | `archtech-prd-enhanced.xml` |
| 1.4 | Contract-First API | ✅ | OpenAPI 3.1 com 55 endpoints, 10 contexts | `docs/api-specifications/archtech-openapi.yaml` |
| 1.5 | Threat Model STRIDE | ✅ | 7 camadas, 20 ameaças com impacto e mitigação | `docs/security/threat-model.md` |
| 1.6 | Design System | ✅ | Tokens (cores, tipografia, espaçamento, efeitos) + Lovable UI | `web/themes/custom/front_theme/src/scss/tokens/` |
| 1.7 | Backlog | ✅ | 11 user stories, 3 sprints, critérios BDD | `docs/backlog-sprint-1-3.md` |
| 1.8 | SLOs | ✅ | 10 SLOs definidos | `archtech-prd.md` |
| — | C4 Model | ✅ | Níveis L1 (Contexto), L2 (Containers), L3 (Componentes) | `docs/architecture/c4-model.md` |
| — | Event Catalog | ✅ | 31 domain events catalogados | `docs/data-contracts/event-catalog.md` |
| — | Coding Standards | ✅ | Regras PHP/Drupal/Git/HTML/CSS | `docs/guides/coding-standards.md` |
| — | DDEV Setup | ✅ | Ambiente local (PHP 8.4, PostgreSQL 18, nginx-fpm) | `docs/guides/ddev-setup.md` |
| — | Recipe Guide | ✅ | Como criar recipes ArchTech | `docs/guides/recipe-creation.md` |
| — | Site Instalado | ✅ | Drupal 11.3 com front_theme + 25 recipes aplicadas | `https://archtech.ddev.site:8443` |

### Definition of Done — Fase 1

- [x] ADRs revisados (7) — aprovados
- [x] API specs OpenAPI 3.1 com 55 endpoints — YAML válido
- [x] Backlog com estimativas para Sprint 1
- [x] Threat Model STRIDE documentado

---

## Fase 2 — Infraestrutura, CI/CD e Segurança Base

**Duração:** 2 semanas · **Skills:** DevOps/SRE, CI/CD, Security, Observabilidade · **Ferramentas:** Terraform, Helm, GitHub Actions, Docker, Kubernetes, Vault, Prometheus, Grafana, Loki, Jaeger, DDEV

### Tarefas

| # | Tarefa | Prioridade | Status | Entregável |
|---|--------|-----------|--------|------------|
| 2.1 | Terraform: VPC, EKS/GKE, RDS PostgreSQL, ElastiCache Redis | Média | ✅ | `infrastructure/terraform/` com 4 módulos (VPC, EKS, RDS, Redis) |
| 2.2 | Kubernetes: namespaces, RBAC, NetworkPolicies | Média | ✅ | `infrastructure/kubernetes/` (namespaces, RBAC, NetworkPolicies, Helm chart) |
| 2.3 | Pipeline CI Backend: lint → SAST → tests → Docker → Helm | Alta | ✅ | `.github/workflows/backend-ci.yml` com quality gates |
| 2.4 | Pipeline CI Frontend: lint → type-check → jest → Lighthouse → deploy | Alta | ✅ | `.github/workflows/frontend-ci.yml` completo |
| 2.5 | Quality gates bloqueantes (coverage, phpstan, snyk, lighthouse, tsc, spectral) | Alta | ✅ | Coverage ≥80%, Snyk high, tsc, Spectral, Lighthouse em ambos workflows |
| 2.6 | HashiCorp Vault: políticas por serviço, rotação automática de secrets | Média | ✅ | `infrastructure/vault/` com policies + scripts de rotação |
| 2.7 | WAF (AWS WAF / Cloudflare) OWASP CRS + DDoS protection | Baixa | ✅ | `infrastructure/waf/` com AWS WAF + Cloudflare WAF Terraform |
| 2.8 | Observabilidade: Prometheus + Grafana + Loki + Jaeger | Média | ✅ | `infrastructure/observability/` com dashboards + alertas |
| 2.9 | DDEV com paridade: Redis 7+ e RabbitMQ 3.13+ como add-ons | **Alta** | ✅ | `.ddev/docker-compose.redis.yaml` + módulo redis ativo |
| 2.10 | RabbitMQ: exchanges, queues, DLQ, alertas de queue depth | **Alta** | ✅ | `infrastructure/rabbitmq/definitions.json` — 8 exchanges, 18 queues, DLQ policy |

### Ordem de Execução

```
2.9 (DDEV add-ons) → 2.10 (RabbitMQ) → 2.3 (CI Backend) → 2.4 (CI Frontend) → 2.5 (Quality Gates)
→ 2.1 (Terraform) → 2.2 (K8s) → 2.6 (Vault) → 2.7 (WAF) → 2.8 (Observabilidade)
```

### Definition of Done

- [X] Pipelines CI/CD criados: backend-ci.yml (7 stages), frontend-ci.yml (9 stages), test.yml (smoke)
- [X] Secrets nunca visíveis em logs ou código — auditado (relatório em docs/security/secrets-audit-report.md)
- [X] Regras de alerta configuradas — Prometheus rules (archtech-alerts.yml) com 9 alertas
- [X] DDEV replicável — runbook 10-new-dev-onboarding.md validado, setup < 5 min

**Status geral:** ✅ 10/10 tarefas concluídas

---

## Fase 3 — Backend Modular — Plataforma Core

| # | Tarefa | Status | Observação |
|---|--------|--------|------------|
| 3.1 | Clonar e configurar drupal-recipes-base | ✅ Feito | Recipes base aplicadas |
| 3.2 | archtech_core_api | ❌ Pendente | Controller base, paginação, filtros |
| 3.3 | archtech_security | ❌ Pendente | OAuth2/OIDC, RBAC, audit log |
| 3.4 | archtech_events | ❌ Pendente | EventDispatcher, Outbox Pattern, RabbitMQ |
| 3.5 | archtech_feature_flags | ❌ Pendente | Redis feature flags |
| 3.6 | archtech_ai_gateway | ❌ Pendente | Circuit breaker, prompt registry |
| 3.7 | `ia_atendimento` — Lead, Meeting, consumers | 📋 Especificado | Ver PRD F01-F02 |
| 3.8 | `ia_marketing` — BlogPost, MediaAsset | 📋 Especificado | Ver PRD F03 |
| 3.9 | `ia_projetos` — Project, Render, ValidationReport | 📋 Especificado | Ver PRD F04 |
| 3.10 | `ia_obras` — Schedule, MaterialList, SiteChecklist, Budget | 📋 Especificado | Ver PRD F05 |
| 3.11 | `ia_suporte` — Document, Tutorial, pgvector | 📋 Especificado | Ver PRD F06 |
| 3.12 | `ia_insights` — Insight, schedulers | 📋 Especificado | Ver PRD F07 |
| 3.13 | `ia_diary` — DiaryEntry, DiaryPhoto, WeeklyReport | 📋 Especificado | Ver F11 XML |
| 3.14 | `ia_meetings` — MeetingRecord, ActionItem, transcrição | 📋 Especificado | Ver F12 XML |
| 3.15 | `ia_financeiro_avancado` — Reembolso, Folha, CashFlow, ABC | 📋 Especificado | Ver F13 XML |
| 3.16 | `ia_teams` — TeamMember, ProjectAllocation | 📋 Especificado | Ver F14 XML |
| 3.17 | `ia_compliance` — AuditLog, DocumentVersion, LGPDConsent | 📋 Especificado | Ver F16 XML |
| 3.18 | `ia_marketing_digital` — Portfolio, Blog, LandingPage, Campaign | 📋 Especificado | Ver F17 XML |
| 3.19 | `ia_budget_construction` — Budget, BudgetService, Measurement | 📋 Especificado | Ver F18 XML |
| 3.20 | `ia_deliverables` — ProjectPhase, Deliverable | 📋 Especificado | Ver F19 XML |
| 3.21 | `ia_tasks` — Task | 📋 Especificado | Ver F20 XML |

---

## Fase 4 — Frontend — Design System e Dashboards

| # | Tarefa | Status | Observação |
|---|--------|--------|------------|
| 4.1-4.14 | Setup Next.js, Storybook, dashboards, PWA | ❌ Pendente | Frontend não iniciado |

---

## Fase 5 — Integração e Orquestração de IA

| # | Tarefa | Status | Observação |
|---|--------|--------|------------|
| 5.1-5.11 | n8n, LangSmith, prompts, circuit breakers | ❌ Pendente | Skills dos agentes prontas, integração pendente |

---

## Fase 6 — Testes, Segurança e QA

| # | Tarefa | Status | Observação |
|---|--------|--------|------------|
| 6.1-6.9 | E2E, carga, pentest, acessibilidade, DR | ❌ Pendente | Não iniciado |

---

## Legendas

| Símbolo | Significado |
|---------|-------------|
| ✅ | Completo |
| ⚠️ | Parcial / Incompleto |
| ❌ | Não iniciado |
