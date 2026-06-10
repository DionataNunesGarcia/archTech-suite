# ArchTech Suite — PRD v2.0 Enhanced

> **Documento de Requisitos de Produto** — Revisão arquitetural sênior  
> Versão: `2.0` · Status: `Draft/Review` · Atualizado: `Junho 2025`

---

## Sumário

1. [Visão Geral do Projeto](#1-visão-geral-do-projeto)
2. [Princípios de Design](#2-princípios-de-design)
3. [Architectural Decision Records (ADRs)](#3-architectural-decision-records-adrs)
4. [Requisitos Não-Funcionais e SLOs](#4-requisitos-não-funcionais-e-slos)
5. [Stack Tecnológico](#5-stack-tecnológico)
6. [Arquitetura de Módulos](#6-arquitetura-de-módulos)
7. [Domain Events e Comunicação entre Squads](#7-domain-events-e-comunicação-entre-squads)
8. [Squads e Bounded Contexts](#8-squads-e-bounded-contexts)
9. [Infraestrutura e DevOps](#9-infraestrutura-e-devops)
10. [Fases de Desenvolvimento](#10-fases-de-desenvolvimento)
11. [Governança de IA](#11-governança-de-ia)
12. [Estratégia de Dados e LGPD](#12-estratégia-de-dados-e-lgpd)
13. [Segurança](#13-segurança)
14. [Observabilidade](#14-observabilidade)

---

## 1. Visão Geral do Projeto

### O que é

O **ArchTech Suite** é um ecossistema de squads de inteligência artificial voltado para escritórios de arquitetura, urbanismo e construção civil. O sistema reúne 6 squads especializados com 20 agentes de IA, todos orquestrados por um backend modular em Drupal 11 e um frontend em Next.js/React.

### Repositório e Documentação

| Recurso                 | Localização                                                 |
| ----------------------- | ----------------------------------------------------------- |
| Repositório principal   | `https://github.com/DionataNunesGarcia/drupal-recipes-base` |
| API Specs (OpenAPI 3.1) | `./docs/api-specifications/`                                |
| ADRs                    | `./docs/adr/`                                               |
| Prompt Registry         | `./docs/ai-prompts/`                                        |
| Data Contracts          | `./docs/data-contracts/`                                    |
| Runbooks                | `./docs/runbooks/`                                          |
| Security Guidelines     | `./docs/security-guidelines/`                               |

### Responsáveis

| Papel             | Responsável |
| ----------------- | ----------- |
| Tech Lead         | A definir   |
| Product Owner     | A definir   |
| Security Champion | A definir   |
| AI Team Lead      | A definir   |

---

## 2. Princípios de Design

Estes princípios são **regras arquiteturais**, não sugestões. Qualquer decisão que os contradiga deve ser documentada com justificativa em um novo ADR.

---

### P01 — Contract-First API

> Toda API é especificada via **OpenAPI 3.1** antes de qualquer linha de código de implementação ser escrita. O contrato é o artefato primário.

**Por quê:** Garante que backend e frontend possam evoluir em paralelo com expectativas alinhadas. Evita a síndrome de "o endpoint está pronto, vamos documentar depois" — que leva a inconsistências e retrabalho.

**Ferramentas:** Stoplight Studio (design) · Spectral (linting automático) · Prism (mock server para desenvolvimento paralelo)

---

### P02 — Bounded Contexts (DDD)

> Cada squad é um **Bounded Context** independente com seu próprio modelo de dados, linguagem ubíqua e interfaces de integração bem definidas.

**Por quê:** Módulos de squad que compartilham banco, classes ou lógica diretamente criam acoplamento que inviabiliza manutenção independente. Um bug no Squad de Obras não pode derrubar o Squad de Atendimento.

**Regra fundamental:** Módulos de squad **nunca** importam diretamente outros módulos de squad. Toda comunicação cross-squad passa por domain events ou APIs públicas documentadas.

---

### P03 — Event-Driven por Padrão

> Comunicação assíncrona entre squads via **domain events** publicados no RabbitMQ. Sem chamadas síncronas diretas entre módulos de squads distintos.

**Por quê:** Chamadas síncronas criam cadeias de falha. Se o Squad de Projetos fizer uma chamada direta ao Squad de Obras e este estiver lento, o Squad de Projetos também fica lento. Com eventos, cada squad processa no seu ritmo.

**Padrões obrigatórios:**

- **Outbox Pattern** — garante publicação atômica do evento junto ao write no banco
- **Idempotência** — todo consumer deve ser idempotente (at-least-once delivery)
- **Dead Letter Queue (DLQ)** — mensagens não processadas vão para fila de erro com alerta

---

### P04 — Fail-Safe e Circuit Breaker

> Toda integração com sistemas externos (provedores de IA, APIs de terceiros, calendários) implementa **circuit breaker**, retry com backoff exponencial e fallback gracioso.

**Por quê:** APIs de IA têm latência variável e rate limits. Sem circuit breaker, uma lentidão da OpenAI pode travar toda a plataforma.

**Comportamento esperado:** Se o GPT-4o não responde em 30s, o circuit breaker abre. O sistema entra em modo degradado (ex: lead criado sem score, score calculado manualmente depois) e alerta o time de operação.

---

### P05 — Observabilidade de Ponta a Ponta

> Cada operação carrega um `trace_id` único, propagado do frontend até os provedores de IA. Nenhuma operação existe sem log estruturado (JSON) e span de tracing.

**Por quê:** Em um sistema com 20 agentes de IA, múltiplos filas e 6 squads, encontrar a origem de um bug sem tracing distribuído é inviável.

**Formato mínimo de log:**

```json
{
	"trace_id": "abc123",
	"squad": "ia_atendimento",
	"agent": "qualificadora",
	"action": "qualify_lead",
	"lead_id": 42,
	"duration_ms": 183,
	"ai_model": "gpt-4o",
	"tokens_used": 312,
	"level": "info",
	"timestamp": "2025-06-01T10:00:00Z"
}
```

---

### P06 — Segurança por Design (Shift Left)

> Análise de segurança acontece na **fase de design de requisitos**, não no deploy. SAST/DAST rodam no pipeline CI. Secrets nunca aparecem em código ou logs.

**Por quê:** Corrigir uma vulnerabilidade de autenticação após o go-live custa 10x mais do que preveni-la no design.

**Ferramentas:** Snyk (SAST) · OWASP ZAP (DAST) · HashiCorp Vault (secrets) · STRIDE (threat modeling)

---

### P07 — Feature Flags e Progressive Delivery

> Deploy é desacoplado de release. Toda feature nova fica atrás de uma **feature flag**. Canary deployments para mudanças de alto risco.

**Por quê:** Permite testar em produção com um subconjunto de usuários, reverter instantaneamente sem rollback de deploy e lançar features por squad/tenant.

**Regra:** Flag removida após 2 sprints de estabilidade em produção.

---

### P08 — AI Governance e Prompt Versioning

> Prompts são **artefatos de engenharia versionados** (semver), não strings no banco de dados. Toda mudança passa pelo mesmo pipeline de review do código.

**Por quê:** Um prompt alterado sem versionamento é como fazer deploy sem controle de versão. Mudanças de prompt afetam o comportamento de todos os usuários imediatamente e são impossíveis de auditar retroativamente.

**Regra:** `prompt_id@major.minor.patch`. Breaking change (mudança de comportamento esperado) = major bump. Toda versão tem test cases automatizados.

---

## 3. Architectural Decision Records (ADRs)

Os ADRs registram **por que** cada decisão arquitetural foi tomada e quais alternativas foram descartadas. Atualizações são feitas via pull request em `./docs/adr/`.

---

### ADR-001 · Drupal 11 como Backend Headless

| Campo  | Valor        |
| ------ | ------------ |
| Status | **accepted** |
| Data   | 2025-06      |

**Decisão:** Usar Drupal 11 como CMS headless com JSON:API e GraphQL.

**Contexto:** O sistema precisa de content modelling flexível (20+ content types), suporte nativo a multisite, i18n, e um ecossistema de módulos maduro para autenticação, media, workflows e roles.

**Justificativa:** Drupal tem 20+ anos de battle-testing em enterprise, content modelling declarativo via UI (sem código para criar campos), Recipes para instalação reproduzível de configurações, e suporte nativo a JSON:API e GraphQL sem plugins adicionais.

**Alternativas descartadas:**

- **Strapi** — content modelling menos maduro para enterprise, sem suporte nativo a multisite
- **WordPress + ACF** — modelo de dados menos estruturado, REST API limitada, arquitetura menos adequada para headless

---

### ADR-002 · Next.js 15 com App Router

| Campo  | Valor        |
| ------ | ------------ |
| Status | **accepted** |
| Data   | 2025-06      |

**Decisão:** Usar Next.js 15 com App Router como framework frontend.

**Justificativa:** SEO nativo via SSR/ISR, API routes para o padrão BFF (Backend for Frontend), ISR (Incremental Static Regeneration) para páginas de marketing com revalidação automática, e Server Components para reduzir bundle no cliente.

**Alternativas descartadas:**

- **SPA pura (Vite + React)** — SEO inadequado para landing pages e blog sem SSR
- **Remix** — ecossistema menor, menos integrações com ferramentas de deploy

---

### ADR-003 · RabbitMQ como Message Broker Principal

| Campo  | Valor        |
| ------ | ------------ |
| Status | **accepted** |
| Data   | 2025-06      |

**Decisão:** RabbitMQ para mensageria principal + Redis para cache e pub/sub efêmero. Kafka reservado para escala futura.

**Justificativa:** RabbitMQ oferece entrega garantida com ACK/NACK, DLQ nativa, gestão visual via management UI e suporte a múltiplos padrões de exchange (direct, topic, fanout). Redis é ideal para pub/sub em tempo real (notificações WebSocket) e cache de sessão sem overhead de persistência.

**Critério para migrar para Kafka:** Volume acima de 1 milhão de eventos/dia ou necessidade de replay de eventos históricos (event sourcing).

---

### ADR-004 · n8n Self-Hosted como Orquestrador de Workflows de IA

| Campo  | Valor        |
| ------ | ------------ |
| Status | **proposed** |
| Data   | 2025-06      |

**Decisão:** Usar n8n self-hosted (deploy Kubernetes) para orquestração de workflows de IA e integrações externas.

**Justificativa:** Workflows visuais aceleram desenvolvimento de integrações. Self-hosted elimina vendor lock-in e custos variáveis por execução. Suporte nativo a webhooks, HTTP nodes, código JavaScript customizado e dezenas de integrações pré-built.

**Alternativas descartadas:**

- **Zapier** — caro em volume alto de execuções, sem self-hosting
- **Temporal** — complexidade operacional elevada para o estágio atual do projeto
- **Make (Integromat)** — sem self-hosting, vendor lock-in

**Risco:** n8n self-hosted requer operação e monitoramento da própria infraestrutura. Mitigação: deploy em Kubernetes com backup automático de workflows.

---

### ADR-005 · PostgreSQL 18 Gerenciado

| Campo  | Valor        |
| ------ | ------------ |
| Status | **accepted** |
| Data   | 2025-06      |

**Decisão:** PostgreSQL 18 como banco principal via managed service (RDS ou Cloud SQL).

**Justificativa:** JSONB para dados semi-estruturados, full-text search nativo, extensão pgvector para embeddings semânticos (Squad Suporte), PostGIS para dados geoespaciais de projetos, e point-in-time recovery via managed service.

**Extensões críticas:**

- `pgvector` — busca por similaridade de embeddings
- `PostGIS` — dados geoespaciais de projetos urbanos
- `pg_trgm` — trigram search para full-text fuzzy

---

## 4. Requisitos Não-Funcionais e SLOs

Cada NFR tem um SLO mensurável, prioridade e ferramenta de medição definidos. SLOs são monitorados continuamente no Grafana.

### Performance

| ID      | Descrição                      | SLO                              | Prioridade   | Ferramenta                 |
| ------- | ------------------------------ | -------------------------------- | ------------ | -------------------------- |
| NFR-P01 | Tempo de resposta de APIs      | P90 ≤ 200ms · P99 ≤ 500ms        | **Critical** | Prometheus + k6            |
| NFR-P02 | LCP (Largest Contentful Paint) | P75 ≤ 2.5s                       | High         | Web Vitals + Lighthouse CI |
| NFR-P03 | Resposta de agentes de IA      | P90 ≤ 5s (streaming) · P99 ≤ 15s | High         | AI Observability dashboard |

### Escalabilidade

| ID      | Descrição            | SLO                             | Prioridade   | Ferramenta          |
| ------- | -------------------- | ------------------------------- | ------------ | ------------------- |
| NFR-S01 | Usuários simultâneos | 10.000 conc. · degradação ≤ 20% | **Critical** | k6 + Kubernetes HPA |

### Disponibilidade

| ID      | Descrição               | SLO                          | Prioridade   | Ferramenta                   |
| ------- | ----------------------- | ---------------------------- | ------------ | ---------------------------- |
| NFR-R01 | Uptime do sistema       | 99.9% (≤ 8.76h downtime/ano) | **Critical** | Checkly + PagerDuty          |
| NFR-R02 | MTTR de falhas críticas | ≤ 4 horas                    | High         | Incident tracking + runbooks |

### Segurança

| ID       | Descrição                        | SLO                                   | Prioridade   | Ferramenta                    |
| -------- | -------------------------------- | ------------------------------------- | ------------ | ----------------------------- |
| NFR-SE01 | Conformidade OWASP Top 10 + LGPD | Zero vulns críticas · auditoria anual | **Critical** | Snyk + OWASP ZAP + Burp Suite |

### Manutenibilidade

| ID      | Descrição            | SLO                         | Prioridade | Ferramenta               |
| ------- | -------------------- | --------------------------- | ---------- | ------------------------ |
| NFR-M01 | Cobertura de testes  | ≥ 80% unitário + integração | High       | PHPUnit + Jest + Codecov |
| NFR-M02 | Tempo de build do CI | ≤ 10 minutos por pipeline   | Medium     | GitHub Actions metrics   |

### Usabilidade

| ID      | Descrição      | SLO                               | Prioridade | Ferramenta                   |
| ------- | -------------- | --------------------------------- | ---------- | ---------------------------- |
| NFR-U01 | Acessibilidade | WCAG 2.1 AA · zero violações A/AA | Medium     | axe-core CI + NVDA/VoiceOver |

---

## 5. Stack Tecnológico

### Backend

| Camada          | Tecnologia | Versão | Observação                          |
| --------------- | ---------- | ------ | ----------------------------------- |
| Plataforma      | Drupal     | 11     | Headless, JSON:API + GraphQL        |
| Linguagem       | PHP        | 8.4+   | Strict types, readonly, fibers      |
| Banco principal | PostgreSQL | 18     | Managed (RDS/Cloud SQL) + pgvector  |
| Cache / Pub-Sub | Redis      | 7+     | Object cache, sessão, rate limiting |
| Message broker  | RabbitMQ   | 3.13+  | Domain events, DLQ                  |
| Event streaming | Kafka      | —      | Reservado para >1M eventos/dia      |

**Recipes Drupal (ordem de instalação):**

```
1. base_admin             — administração base
2. base_i18n              — internacionalização
3. base_seo               — SEO canônico e meta tags
4. base_theme             — tema base headless
5. base_lp                — landing pages
6. base_ai                — integração AI Service Layer
7. base_ai_contents       — content types AI-assisted
8. custom_api_gateway     — API Gateway config + rate limiting
9. observability_integration — hooks de observabilidade (OpenTelemetry)
```

> **Importante:** A ordem de instalação das recipes é funcional. `base_ai` deve ser instalada antes de `base_ai_contents`. Não altere sem atualizar este documento.

---

### Frontend

| Camada         | Tecnologia        | Versão | Observação                                |
| -------------- | ----------------- | ------ | ----------------------------------------- |
| Framework      | Next.js           | 15     | App Router, SSR/ISR, Server Components    |
| UI Library     | React             | 19     | Concurrent features, Server Components    |
| Linguagem      | TypeScript        | 5.x    | Strict mode obrigatório                   |
| Styling        | TailwindCSS       | 4.x    | + CSS Modules para escopo local           |
| Design System  | Storybook         | 8      | Atomic Design, visual regression          |
| State (server) | TanStack Query    | 5      | Cache, sincronização, mutations           |
| State (client) | Zustand           | —      | Evitar Redux salvo necessidade comprovada |
| Auth           | NextAuth.js       | —      | Provider OAuth2 → Drupal OIDC             |
| Realtime       | Socket.IO ou Ably | —      | Fallback para polling com backoff         |
| PWA            | Workbox           | —      | Service Worker, push notifications        |

**Estratégias de rendering por tipo de página:**

| Tipo de Página       | Estratégia             | Motivo                                  |
| -------------------- | ---------------------- | --------------------------------------- |
| Dashboards de squad  | SSR                    | Dados dinâmicos por usuário             |
| Landing pages / blog | ISR (revalidate: 3600) | SEO + performance                       |
| Editor de conteúdo   | CSR                    | Interatividade alta, SEO não necessário |
| Relatórios           | SSR                    | Dados atualizados a cada acesso         |

---

### Inteligência Artificial

| Recurso             | Tecnologia             | Uso                                            |
| ------------------- | ---------------------- | ---------------------------------------------- |
| Texto / chat        | OpenAI GPT-4o          | Agentes conversacionais, qualificação, análise |
| Texto (custo menor) | GPT-4o-mini            | Tarefas simples, fallback de custo             |
| Imagens             | DALL·E 3               | Geração de assets de marketing                 |
| Imagens alternativo | MidJourney API         | Renders arquitetônicos fotorrealistas          |
| Vídeo / render      | Runway Gen-3           | Renders animados de projetos                   |
| Embeddings          | text-embedding-3-large | Busca semântica de documentos                  |
| Orquestração        | n8n (self-hosted)      | Workflows visuais de IA                        |
| AI tracing          | LangSmith ou Helicone  | Logging e custo de chamadas LLM                |
| PII masking         | Microsoft Presidio     | Mascara dados pessoais antes de APIs externas  |
| Moderação           | OpenAI Moderation API  | Filtra conteúdo gerado antes de persistir      |

---

## 6. Arquitetura de Módulos

### Visão Geral das Camadas

```
┌─────────────────────────────────────────────────────┐
│                    CLIENTE                          │
│         Browser · Mobile PWA · Desktop App          │
└──────────────────────┬──────────────────────────────┘
                       │ HTTPS / WebSocket
┌──────────────────────▼──────────────────────────────┐
│              API GATEWAY + SEGURANÇA                │
│    WAF · OAuth2/OIDC · Rate Limiting · DDoS         │
└──────────────────────┬──────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────┐
│        FRONTEND  (Next.js 15 + React 19)            │
│  Design System · TanStack Query · WebSocket · PWA   │
└──────────────────────┬──────────────────────────────┘
                       │ JSON:API / GraphQL / REST
┌──────────────────────▼──────────────────────────────┐
│          BACKEND  (Drupal 11 + PHP 8.4)             │
│                                                     │
│  ┌─────────────── PLATAFORMA (shared) ───────────┐  │
│  │ core_api · security · events · ai_gateway     │  │
│  │ feature_flags                                 │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  ┌── SQUADS (isolados, comunicam via events) ────┐  │
│  │ atendimento · marketing · projetos · obras    │  │
│  │ suporte · insights                            │  │
│  └───────────────────────────────────────────────┘  │
└──────────────────────┬──────────────────────────────┘
              AMQP ↕   │ SQL/Redis
┌──────────────────────▼──────────────────────────────┐
│            AI ORCHESTRATION LAYER                   │
│  n8n · AI Service Layer · Prompt Registry           │
│  OpenAI · DALL·E · MidJourney · Runway              │
└──────────────────────┬──────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────┐
│                   DADOS                             │
│  PostgreSQL 18 + pgvector · Redis · RabbitMQ        │
│  Data Warehouse (BigQuery/Snowflake) · Vault/KMS    │
└─────────────────────────────────────────────────────┘
```

---

### Módulos de Plataforma (Shared)

São os únicos módulos que podem ser importados por módulos de squad. **Não têm dependências de squad.**

#### `archtech_core_api`

- **Propósito:** Base arquitetural de todas as APIs. Controller base, paginação cursor-based, filtros declarativos, error response contracts padronizados.
- **Expõe:** `ArchtechApiController`, traits de serialização, middleware de validação de schema.
- **Depende de:** Drupal core, JSON:API, GraphQL module.
- **Responsável:** Platform team.

#### `archtech_security`

- **Propósito:** Autenticação, autorização, auditoria e gestão de tokens.
- **Expõe:** Serviços OAuth2/OIDC, decorators de permissão por squad, audit trail imutável.
- **Depende de:** `archtech_core_api`.
- **Responsável:** Security Champion.

#### `archtech_events`

- **Propósito:** Domain Event bus sobre RabbitMQ. Implementa Outbox Pattern para atomicidade, EventStore para histórico, retry com exponential backoff, DLQ handling.
- **Expõe:** `EventDispatcher`, `EventSubscriberInterface`, `EventStore`.
- **Depende de:** `archtech_core_api`, RabbitMQ client.
- **Responsável:** Platform team.

#### `archtech_feature_flags`

- **Propósito:** Feature flags por squad, usuário, tenant e percentual de rollout. Store no Redis para latência mínima.
- **Expõe:** `FeatureFlagService`, Twig extension, endpoint REST de flags.
- **Depende de:** `archtech_core_api`, Redis.
- **Responsável:** Platform team.

#### `archtech_ai_gateway`

- **Propósito:** Proxy unificado para todos os provedores de IA. Circuit breaker por provider, retry com backoff, cost tracking por chamada, Prompt Registry versionado, PII masking via Presidio.
- **Expõe:** `AiGatewayService`, `PromptRegistry`, `ModelRouter`.
- **Depende de:** `archtech_core_api`, `archtech_events`.
- **Responsável:** AI team.

---

### Módulos de Squad (Isolados)

Cada módulo é um Bounded Context com ownership claro. **Nunca importam uns aos outros.**

| Módulo                   | Bounded Context        | Squad       | APIs Públicas                                                        |
| ------------------------ | ---------------------- | ----------- | -------------------------------------------------------------------- |
| `ia_atendimento`         | `leads`                | Atendimento | `/api/v1/leads`, `/api/v1/meetings`                                  |
| `ia_marketing`           | `marketing`            | Marketing   | `/api/v1/blog_posts`, `/api/v1/media/images`                         |
| `ia_projetos`            | `projects`             | Projetos    | `/api/v1/projects`, `/api/v1/media/renders`                          |
| `ia_obras`               | `construction`         | Obras       | `/api/v1/construction/*`                                             |
| `ia_suporte`             | `internal_ops`         | Suporte     | `/api/v1/documents`, `/api/v1/tutorials`                             |
| `ia_insights`            | `intelligence`         | Insights    | `/api/v1/insights/*`                                                 |
| `ia_diary`               | `construction_diary`   | Obras       | `/api/v1/diary/*`                                                    |
| `ia_meetings`            | `meeting_intelligence` | Projetos    | `/api/v1/meetings`, `/api/v1/projects/{id}/meetings`                 |
| `ia_financeiro_avancado` | `advanced_financial`   | Financeiro  | `/api/v1/finance/*`                                                  |
| `ia_teams`               | `team_management`      | Operações   | `/api/v1/teams/*`                                                    |
| `ia_compliance`          | `compliance_audit`     | Platform    | `/api/v1/audit-log`, `/api/v1/versions/*`, `/api/v1/lgpd/*`          |
| `ia_marketing_digital`   | `digital_marketing`    | Marketing   | `/api/v1/portfolio`, `/api/v1/blog`, `/api/v1/campaigns`             |
| `ia_budget_construction` | `construction_budget`  | Obras       | `/api/v1/construction/budget`, `/api/v1/construction/measurement`    |
| `ia_deliverables`        | `project_deliverables` | Projetos    | `/api/v1/projects/{id}/phases`, `/api/v1/projects/{id}/deliverables` |
| `ia_tasks`               | `task_management`      | Platform    | `/api/v1/tasks/*`                                                    |

---

### Regras de Dependência

```
✅ PERMITIDO:
  ia_atendimento → archtech_core_api
  ia_atendimento → archtech_security
  ia_atendimento → archtech_events
  ia_atendimento → archtech_ai_gateway
  ia_atendimento → archtech_feature_flags
  (mesma regra para todos os módulos de squad)

❌ PROIBIDO:
  ia_atendimento → ia_marketing
    solução: publicar LeadCreated no RabbitMQ; Marketing consome

  ia_projetos → ia_obras
    solução: publicar ProjectValidated no RabbitMQ; Obras consome

  qualquer ia_* → outro ia_*
    solução: sempre via domain events ou API pública documentada
```

---

## 7. Domain Events e Comunicação entre Squads

### Catálogo de Eventos

Todos os schemas estão em `./docs/data-contracts/` no formato JSON Schema. **Alterações em schemas exigem PR aprovado.**

#### Eventos Emitidos por Squad

| Evento                | Squad Emissor | Consumidores             | Schema                                  |
| --------------------- | ------------- | ------------------------ | --------------------------------------- |
| `LeadCreated`         | Atendimento   | Marketing, Suporte       | `leads/LeadCreated.json`                |
| `LeadQualified`       | Atendimento   | —                        | `leads/LeadQualified.json`              |
| `MeetingScheduled`    | Atendimento   | Suporte                  | `leads/MeetingScheduled.json`           |
| `ContentPublished`    | Marketing     | Insights                 | `marketing/ContentPublished.json`       |
| `CampaignOptimized`   | Marketing     | —                        | `marketing/CampaignOptimized.json`      |
| `ProjectCreated`      | Projetos      | Obras, Suporte, Insights | `projects/ProjectCreated.json`          |
| `ProjectValidated`    | Projetos      | Obras                    | `projects/ProjectValidated.json`        |
| `RenderGenerated`     | Projetos      | —                        | `projects/RenderGenerated.json`         |
| `ConstructionStarted` | Obras         | Suporte                  | `construction/ConstructionStarted.json` |
| `BudgetDeviation`     | Obras         | Suporte                  | `construction/BudgetDeviation.json`     |
| `ChecklistAlert`      | Obras         | Suporte                  | `construction/ChecklistAlert.json`      |
| `DocumentIndexed`     | Suporte       | —                        | `internal/DocumentIndexed.json`         |
| `InsightGenerated`    | Insights      | —                        | `insights/InsightGenerated.json`        |
| `TrendAlert`          | Insights      | Suporte                  | `insights/TrendAlert.json`              |

### Configuração RabbitMQ

```
Exchanges:
  archtech.leads        (type: topic)    — eventos de leads e reuniões
  archtech.marketing    (type: topic)    — eventos de conteúdo e campanhas
  archtech.projects     (type: topic)    — eventos de projetos e renders
  archtech.construction (type: topic)    — eventos de obra e orçamento
  archtech.internal     (type: topic)    — eventos internos
  archtech.ai.jobs      (type: direct)   — jobs assíncronos de IA
  archtech.dlq          (type: fanout)   — dead letter queue (alertas)
  archtech.retry        (type: direct)   — retry queue (backoff exponencial)
  archtech.diary        (type: topic)    — eventos do diário de obra (F11)
  archtech.meetings     (type: topic)    — eventos de atas de reunião (F12)
  archtech.financial_adv (type: topic)   — eventos financeiros avançados (F13)
  archtech.tasks        (type: topic)    — eventos de tarefas (F20)

Políticas obrigatórias:
  - DLQ configurada em todas as filas
  - TTL de mensagem: 7 dias (configurável por fila)
  - Max retries: 3 com backoff exponencial (1s, 4s, 16s)
  - Alerta de PagerDuty se DLQ > 10 mensagens
```

### Estrutura Base de um Domain Event

```json
{
	"event_id": "uuid-v4",
	"event_type": "LeadCreated",
	"event_version": "1.0",
	"source_module": "ia_atendimento",
	"trace_id": "abc123",
	"occurred_at": "2025-06-01T10:00:00Z",
	"payload": {
		// dados específicos do evento
	}
}
```

---

## 8. Squads e Bounded Contexts

---

### Squad: Atendimento e Leads

**Bounded context:** `leads` · **Módulo:** `ia_atendimento`

#### Agentes

| Agente           | Skill                                            | Endpoint                           | Trigger                           |
| ---------------- | ------------------------------------------------ | ---------------------------------- | --------------------------------- |
| IA Recepcionista | Lead Chat — coleta estruturada via conversação   | `POST /api/v1/leads`               | Visitante inicia chat             |
| IA Qualificadora | Lead Scoring (0–100) por orçamento, prazo e fit  | `PATCH /api/v1/leads/{id}/qualify` | Evento `LeadCreated`              |
| IA Agenda        | Meeting Scheduling via Google Calendar / Outlook | `POST /api/v1/meetings`            | Lead score ≥ 60 ou ação manual    |
| IA Follow-up     | Nurturing automatizado por email e WhatsApp      | Queue: `followup_queue`            | Cron: leads sem meeting há 3 dias |

**Fluxo: Lead novo até reunião agendada**

```
Visitante abre chat
    → IA Recepcionista coleta dados
    → POST /api/v1/leads (cria Lead no Drupal)
    → Publica LeadCreated no RabbitMQ

LeadCreated é consumido:
    → IA Qualificadora analisa via GPT-4o
    → PATCH /api/v1/leads/{id}/qualify (score = 75)
    → Publica LeadQualified

Se score ≥ 60:
    → IA Agenda verifica disponibilidade (Google Calendar API via n8n)
    → POST /api/v1/meetings
    → Publica MeetingScheduled
    → Envia confirmação por email (SendGrid) e WhatsApp (Twilio)
```

**Tratamento de falhas:**

- GPT-4o indisponível → lead criado sem score, alerta para qualificação manual
- Google Calendar indisponível → meeting salvo como pendente, reprocessado via queue
- Timeout de qualificação → circuit breaker abre após 3 falhas consecutivas, alerta PagerDuty

#### Content Types

**Lead**

| Campo                  | Tipo    | Observação                                           |
| ---------------------- | ------- | ---------------------------------------------------- |
| `nome`                 | string  | required                                             |
| `email`                | email   | required · unique                                    |
| `telefone`             | phone   |                                                      |
| `tipo_projeto`         | select  | residencial · comercial · urbanismo · reforma        |
| `orcamento_estimado`   | decimal |                                                      |
| `prazo_desejado`       | date    |                                                      |
| `score_qualificacao`   | integer | range: 0–100                                         |
| `status`               | select  | novo · qualificado · agendado · convertido · perdido |
| `historico_interacoes` | json    | array de interações com timestamp                    |
| `canal_origem`         | select  | chat · email · whatsapp · indicacao · organico       |

**Meeting**

| Campo          | Tipo      | Observação                                    |
| -------------- | --------- | --------------------------------------------- |
| `lead_id`      | reference | → Lead                                        |
| `arquiteto_id` | reference | → User                                        |
| `data_hora`    | datetime  |                                               |
| `tipo`         | select    | video · presencial · telefone                 |
| `link_video`   | url       |                                               |
| `status`       | select    | agendado · confirmado · realizado · cancelado |

#### Métricas

| Métrica                     | Target      |
| --------------------------- | ----------- |
| Taxa de conversão de leads  | ≥ 15%       |
| Tempo médio de qualificação | ≤ 5 min     |
| Follow-up response rate     | ≥ 20%       |
| Reuniões agendadas/semana   | baseline S1 |

---

### Squad: Marketing e Divulgação

**Bounded context:** `marketing` · **Módulo:** `ia_marketing`

#### Agentes

| Agente         | Skill                                   | Endpoint                          | Trigger             |
| -------------- | --------------------------------------- | --------------------------------- | ------------------- |
| IA Conteudista | Geração de posts/artigos SEO-otimizados | `POST /api/v1/blog_posts`         | Manual ou schedule  |
| IA Designer    | Visual assets via DALL·E 3 / MidJourney | `POST /api/v1/media/images`       | Manual              |
| IA Ads         | Otimização de campanhas Google/Meta Ads | n8n workflow                      | Diário              |
| IA Analytics   | Analytics preditivo via GA4 + dados CRM | `GET /api/v1/analytics/marketing` | Semanal + on-demand |

> **Human-in-the-loop:** Todo conteúdo gerado pela IA Conteudista entra com `status: revisao`. Publicação requer aprovação humana. Mudanças de budget em campanhas >20% exigem aprovação do gestor.

#### Content Types

**BlogPost:** `titulo · slug (unique) · conteudo (rich_text) · status · gerado_por_ia (boolean) · prompt_version · seo_title · seo_description · imagem_destaque`

**MediaAsset:** `arquivo · tipo (imagem/video/render/mockup) · alt_text · prompt_utilizado · modelo_ia · diretrizes_marca_versao`

> O campo `prompt_version` no BlogPost é obrigatório para conteúdo gerado por IA. Permite rastrear qual versão do prompt gerou qual conteúdo — essencial para auditorias de qualidade.

#### Métricas

| Métrica                         | Target    |
| ------------------------------- | --------- |
| Crescimento de tráfego orgânico | ≥ 10%/mês |
| Engajamento de conteúdo         | ≥ 3%      |
| ROAS de campanhas               | ≥ 4x      |

---

### Squad: Projetos e Execução

**Bounded context:** `projects` · **Módulo:** `ia_projetos`

#### Agentes

| Agente         | Skill                                               | Endpoint                              | Observação                    |
| -------------- | --------------------------------------------------- | ------------------------------------- | ----------------------------- |
| IA Drafting    | Plantas iniciais a partir de briefing estruturado   | `POST /api/v1/projects/draft`         |                               |
| IA BIM         | Parse IFC, detecção de conflitos multidisciplinares | `POST /api/v1/bim/integrate`          | Integra Autodesk APS, Trimble |
| IA Render      | Renders fotorrealistas Runway Gen-3 / MidJourney    | `POST /api/v1/media/renders`          | **Assíncrono** via RabbitMQ   |
| IA Verificador | Checklist normativo NBR/ABNT + acessibilidade       | `POST /api/v1/projects/{id}/validate` |                               |

> **Renders são assíncronos:** a requisição é aceita imediatamente (HTTP 202), processada em background via RabbitMQ, e o usuário recebe notificação via WebSocket quando o render estiver pronto. Tempo esperado: ≤ 2h para 4K.

#### Content Types

**Project:** `nome · cliente_id · tipo · status · briefing (json) · fases (json) · arquivos_bim (file: .ifc/.rvt/.dwg) · normas_verificadas (json) · area_total (m²)`

**Render:** `projeto_id · tipo_render · estilo · resolucao · arquivo · prompt_utilizado · modelo_ia`

**ValidationReport:** `projeto_id · data_verificacao · resultado · normas_checadas (json) · problemas_encontrados (json) · recomendacoes`

#### Métricas

| Métrica                        | Target                       |
| ------------------------------ | ---------------------------- |
| Tempo de criação de rascunho   | ≤ 24h após briefing completo |
| Taxa de conformidade normativa | ≥ 95%                        |
| Turnaround de render 4K        | ≤ 2 horas                    |

---

### Squad: Obras e Reformas

**Bounded context:** `construction` · **Módulo:** `ia_obras`

#### Agentes

| Agente         | Skill                                          | Endpoint                               | Trigger                   |
| -------------- | ---------------------------------------------- | -------------------------------------- | ------------------------- |
| IA Planejadora | Cronograma CPM/PERT, otimização de recursos    | `POST /api/v1/construction/schedules`  | Evento `ProjectValidated` |
| IA Compras     | Comparação de fornecedores, lista de materiais | `POST /api/v1/construction/materials`  | Manual ou schedule        |
| IA Supervisora | Análise de imagens de campo (GPT-4o Vision)    | Queue: `construction_monitoring_queue` | Upload via Mobile PWA     |
| IA Custos      | Orçamento em tempo real, previsão de desvios   | `POST /api/v1/construction/budgets`    | Diário (cron) + on-demand |

**Fluxo: Supervisão de campo**

```
Técnico de campo faz upload de foto via Mobile PWA
    → API Gateway → Drupal (armazena arquivo temporariamente)
    → Publica ImageUploaded → RabbitMQ
    → archtech_ai_gateway → GPT-4o Vision (análise de anomalias)
    → Cria/atualiza SiteChecklist com resultado

Se alerta crítico detectado:
    → Publica ChecklistAlert → RabbitMQ
    → n8n workflow → notificação Slack para Squad Obras + Suporte
    → Push notification para responsável da obra via WebSocket
```

#### Content Types

**Schedule:** `projeto_id · data_inicio · data_prevista_fim · fases (json: tarefas + dependências gantt-ready) · status · percentual_conclusao`

**MaterialList:** `projeto_id · itens (json: item + qtd + unidade + fornecedor + preço) · total_estimado · status`

**SiteChecklist:** `obra_id · data · itens (json) · fotos (multiple files) · alertas (json) · status_geral`

**Budget:** `projeto_id · orcamento_inicial · gasto_real · previsao_final · desvio_percentual · historico (json)`

#### Métricas

| Métrica                         | Target      |
| ------------------------------- | ----------- |
| Aderência ao cronograma         | ≥ 85%       |
| Desvio orçamentário             | ≤ 10%       |
| Não conformidades por checklist | ≤ 2 (média) |

---

### Squad: Suporte Interno

**Bounded context:** `internal_ops` · **Módulo:** `ia_suporte`

#### Agentes

| Agente          | Skill                                              | Endpoint                       | Observação                     |
| --------------- | -------------------------------------------------- | ------------------------------ | ------------------------------ |
| IA Documental   | OCR + indexação semântica + busca por similaridade | `POST /api/v1/documents`       | Usa pgvector/Pinecone          |
| IA Relatórios   | BI automatizado com visualizações interativas      | `GET /api/v1/reports/internal` | Semanal + mensal               |
| IA Comunicadora | Notificações contextuais via Slack/Teams           | n8n workflow                   | Triggered por eventos críticos |
| IA Treinamento  | Trilhas de onboarding adaptativas por perfil       | `POST /api/v1/tutorials`       |                                |

> **Busca Semântica:** O campo `embedding_vector` nos documentos armazena o vetor gerado pelo modelo `text-embedding-3-large`. A busca por `GET /api/v1/documents/search?q=consulta` faz similarity search via pgvector (cosine distance), retornando os documentos mais relevantes mesmo sem correspondência exata de palavras.

#### Content Types

**Document:** `titulo · tipo · arquivo · conteudo_indexado (text: OCR/parsed) · embedding_vector (json: vetor 1536d) · tags · acesso (publico/interno/restrito/confidencial)`

**Tutorial:** `titulo · modulos (json: conteúdo + vídeos + quizzes) · publico_alvo · squad_alvo · progresso (json: user_id → %) · tempo_estimado (min)`

#### Métricas

| Métrica                              | Target   |
| ------------------------------------ | -------- |
| Taxa de sucesso de busca semântica   | ≥ 90%    |
| Completion rate de onboarding        | ≥ 95%    |
| Tempo de resposta a alertas críticos | ≤ 15 min |

---

### Squad: Insights e Tendências

**Bounded context:** `intelligence` · **Módulo:** `ia_insights`

#### Agentes

| Agente         | Skill                                           | Endpoint                            | Schedule                       |
| -------------- | ----------------------------------------------- | ----------------------------------- | ------------------------------ |
| IA Pesquisa    | Web scraping + NLP em fontes técnicas           | `POST /api/v1/insights/trends`      | Diário às 6h                   |
| IA Benchmark   | Análise competitiva de fornecedores e inovações | `POST /api/v1/insights/benchmark`   | Semanal (domingo)              |
| IA Sustentável | Sugestões de materiais por impacto ambiental    | `POST /api/v1/insights/sustainable` | Triggered por `ProjectCreated` |

#### Content Types

**Insight:** `titulo · categoria (tendencia/benchmark/sustentabilidade/regulatorio/tecnologia) · fonte (url) · data_coleta · resumo · impacto_estimado (alto/medio/baixo) · squads_impactados · acoes_sugeridas (json) · status`

#### Métricas

| Métrica                        | Target |
| ------------------------------ | ------ |
| Insights acionáveis por semana | ≥ 5    |
| Insight-to-action rate         | ≥ 30%  |

---

## 9. Infraestrutura e DevOps

### Ambientes

| Ambiente      | Propósito                  | Deploy                         | Banco                      |
| ------------- | -------------------------- | ------------------------------ | -------------------------- |
| `local`       | Desenvolvimento individual | DDEV / Docker Compose          | PostgreSQL local           |
| `development` | Integração de branches     | Auto via CI em push            | Instância dev isolada      |
| `staging`     | Validação pré-produção     | Auto via CI em merge para main | Dados anonimizados de prod |
| `production`  | Sistema em uso real        | Canary deployment via Argo CD  | Managed (RDS/Cloud SQL)    |

### Pipelines CI/CD

**Backend (GitHub Actions):**

```
trigger: push em qualquer branch

1. lint          → PHPStan nível 8 + PHPCS (Drupal coding standards)
2. sast          → Snyk (vulnerabilidades em dependências)
3. test          → PHPUnit (unit + kernel + functional tests)
4. coverage      → Codecov (bloqueia se < 80%)
5. api-lint      → Spectral (valida OpenAPI specs)
6. build         → Docker image → push para ECR/GCR
7. deploy        → Helm upgrade no ambiente correspondente
```

**Frontend (GitHub Actions):**

```
trigger: push em qualquer branch

1. lint          → ESLint + Prettier check
2. type-check    → tsc --noEmit (zero erros TypeScript)
3. test          → Jest + React Testing Library (coverage ≥ 80%)
4. e2e           → Playwright (fluxos críticos em staging)
5. visual        → Chromatic (visual regression no Storybook)
6. lighthouse    → Lighthouse CI (Performance ≥ 85, A11y = 100)
7. a11y          → axe-core (zero violações A/AA)
8. build         → next build + bundle analysis
9. deploy        → Vercel ou ECS
```

### Quality Gates (Bloqueantes no CI)

Nenhum PR é mergeado se algum destes falhar:

- Cobertura de testes < 80%
- Vulnerabilidades críticas ou altas no Snyk
- Lighthouse Performance < 85
- Erros de TypeScript (tsc)
- OpenAPI spec inválida (Spectral)
- Testes E2E falhando

### Kubernetes

```
namespaces:
  archtech-dev        → ambiente development
  archtech-staging    → ambiente staging
  archtech-prod       → produção

por namespace:
  - RBAC com permissões mínimas por serviço
  - NetworkPolicies: módulos de squad só se comunicam com módulos de plataforma
  - HPA (Horizontal Pod Autoscaler): min 2, max 10 pods por serviço
  - PodDisruptionBudget: mínimo 1 pod disponível durante deploys
  - Resource limits: CPU e memória definidos por workload
```

### Canary Deployment

```
Estratégia de rollout para mudanças de alto risco:
  5% do tráfego → aguarda 10 min → análise automática de error rate
  Se error rate < 0.1%: promove para 25%
  Se error rate ≥ 0.1%: rollback automático + alerta PagerDuty
  25% → 10 min → análise → 100%
```

---

## 10. Fases de Desenvolvimento

---

### Fase 1 — Descoberta e Arquitetura

**Duração:** 2–3 semanas

**Skills necessárias:**

- Solution Architecture, Domain-Driven Design (DDD)
- API Design (OpenAPI 3.1), Event Storming
- Security (STRIDE, Threat Modeling)
- Facilitation de workshops com stakeholders

**Ferramentas:** Miro/Mural · Stoplight Studio · Spectral · Structurizr · Notion/Confluence

| #   | Tarefa                                                                                                 |
| --- | ------------------------------------------------------------------------------------------------------ |
| 1.1 | Event Storming com stakeholders — mapear domain events, bounded contexts e agregados por squad         |
| 1.2 | Definir e documentar ADRs para decisões arquiteturais críticas (stack, comunicação, AI providers)      |
| 1.3 | Modelagem de dados por bounded context — Content Types, relações, Data Contracts iniciais              |
| 1.4 | Contract-First API Design — esboço de endpoints OpenAPI 3.1 por squad                                  |
| 1.5 | Threat Modeling STRIDE — identificação de superfícies de ataque e requisitos de segurança              |
| 1.6 | Design System: tokens (cores, tipografia, espaçamento), guia de componentes atômicos, referência Figma |
| 1.7 | Backlog detalhado: Epics → Features → User Stories com critérios de aceite BDD                         |
| 1.8 | Definição de SLOs por endpoint crítico e estratégia de observabilidade                                 |

**Entregáveis:** Documento de Arquitetura (C4 L1–L3) · ADRs versionados · Schemas OpenAPI 3.1 iniciais · Data Contracts JSON Schema · Design System Guidelines · Backlog priorizado · Threat Model document

**Definition of Done:**

- [ ] ADRs revisados e aprovados pelo Tech Lead e PO
- [ ] APIs spec aprovadas via PR com Spectral lint passando (zero erros)
- [ ] Backlog com estimativas para Sprint 1
- [ ] Threat Model document revisado e aprovado pelo Security Champion

---

### Fase 2 — Infraestrutura, CI/CD e Segurança Base

**Duração:** 2 semanas

**Skills necessárias:**

- DevOps / SRE: Kubernetes, Terraform, Helm
- CI/CD: GitHub Actions, pipelines de qualidade
- Security: WAF, secrets management, network policies
- Observabilidade: Prometheus, Grafana, OpenTelemetry

**Ferramentas:** Terraform + Helm · GitHub Actions · Docker + Kubernetes · HashiCorp Vault · Prometheus + Grafana · Loki + Jaeger · DDEV

| #    | Tarefa                                                                                    |
| ---- | ----------------------------------------------------------------------------------------- |
| 2.1  | Provisionar infraestrutura via Terraform: VPC, EKS/GKE, RDS PostgreSQL, ElastiCache Redis |
| 2.2  | Kubernetes: namespaces por ambiente, RBAC, NetworkPolicies                                |
| 2.3  | Pipeline CI Backend: lint → SAST → tests → Docker build → deploy Helm                     |
| 2.4  | Pipeline CI Frontend: lint → type-check → jest → Lighthouse CI → build → deploy           |
| 2.5  | Quality gates obrigatórios configurados como bloqueantes no CI                            |
| 2.6  | HashiCorp Vault: políticas por serviço, rotação automática de secrets do banco            |
| 2.7  | WAF (AWS WAF / Cloudflare) com ruleset OWASP CRS + DDoS protection                        |
| 2.8  | Stack de observabilidade: Prometheus + Grafana + Loki + Jaeger                            |
| 2.9  | Ambientes locais DDEV com paridade de serviços via Docker Compose                         |
| 2.10 | RabbitMQ: exchanges, queues, DLQ, alertas de queue depth                                  |

**Entregáveis:** Infraestrutura provisionada e documentada · Pipelines CI/CD funcionais · Quality gates ativos · Runbooks de operação · Dashboard de observabilidade base no Grafana

**Definition of Done:**

- [ ] Deploy automatizado de uma mudança simples passa em todos os environments
- [ ] Secrets nunca visíveis em logs ou código (verificado por auditoria manual)
- [ ] Alertas de disponibilidade disparando corretamente em teste de falha simulada
- [ ] Ambiente local DDEV replicável em < 10 min (README validado por novo dev)

---

### Fase 3 — Backend Modular — Plataforma Core

**Duração:** 4–5 semanas

**Skills necessárias:**

- Drupal 11 avançado: módulos customizados, Content Types, Recipes, JSON:API, GraphQL
- PHP 8.4+: tipos estritos, atributos, fibers, readonly properties
- Event-Driven Architecture: RabbitMQ, Outbox Pattern, idempotência
- API Design: versionamento semântico, error contracts, OpenAPI compliance
- Testes: PHPUnit, Drupal KernelTestBase, functional tests

**Ferramentas:** Drush · PHPStan nível 8 · PHPCS · PHPUnit + Drupal Test Traits · Spectral · Postman/Hoppscotch

| #    | Tarefa                                                                                                                    |
| ---- | ------------------------------------------------------------------------------------------------------------------------- |
| 3.1  | Clonar e configurar drupal-recipes-base; aplicar Recipes na ordem definida                                                |
| 3.2  | `archtech_core_api`: controller base, paginação cursor-based, filtros declarativos, error response contracts              |
| 3.3  | `archtech_security`: OAuth2/OIDC provider, MFA, RBAC por squad, audit log imutável                                        |
| 3.4  | `archtech_events`: EventDispatcher sobre RabbitMQ, Outbox Pattern, EventStore, retry/DLQ handling                         |
| 3.5  | `archtech_feature_flags`: flag store no Redis, API de flags, Twig extension                                               |
| 3.6  | `archtech_ai_gateway`: circuit breaker, retry backoff, prompt registry, cost tracking por chamada                         |
| 3.7  | `ia_atendimento`: Content Types Lead + Meeting, endpoints, consumers, testes                                              |
| 3.8  | `ia_marketing`: Content Types BlogPost + MediaAsset, endpoints, testes                                                    |
| 3.9  | `ia_projetos`: Content Types Project + Render + ValidationReport, endpoints, testes                                       |
| 3.10 | `ia_obras`: Content Types Schedule + MaterialList + SiteChecklist + Budget, endpoints, testes                             |
| 3.11 | `ia_suporte`: Document com pgvector embedding, Tutorial, semantic search endpoint, testes                                 |
| 3.12 | `ia_insights`: Insight content type, schedulers de coleta, testes                                                         |
| 3.13 | `ia_diary`: Diário de Obra Digital — DiaryEntry, DiaryPhoto, WeeklyReport, endpoints, IA_DiaryAssistant, IA_SiteInspector |
| 3.14 | `ia_meetings`: Atas Inteligentes — MeetingRecord, ActionItem, transcrição Whisper, IA_MeetingScribe                       |
| 3.15 | `ia_financeiro_avancado`: Reembolso, Folha por Projeto, Fluxo de Caixa por Obra, Curva ABC                                |
| 3.16 | `ia_teams`: Gestão de Equipes — TeamMember, ProjectAllocation, IA_TeamOptimizer                                           |
| 3.17 | `ia_compliance`: Auditoria Imutável, Versionamento SHA-256, LGPD Consent                                                  |
| 3.18 | `ia_marketing_digital`: Portfólio, Blog, Landing Pages, Campanhas, IA_ContentCalendar                                     |
| 3.19 | `ia_budget_construction`: Orçamento com Composição Unitária, SINAPI, Medições, Curva ABC                                  |
| 3.20 | `ia_deliverables`: Fases de Projeto, Checklist de Entregáveis, Versionamento, Aprovação                                   |
| 3.21 | `ia_tasks`: Tarefas Unificadas, Kanban, IA_TaskPrioritizer                                                                |
| 3.22 | OpenTelemetry trace_id em todas as requisições HTTP e mensagens de fila                                                   |
| 3.23 | Documentar APIs finais em OpenAPI 3.1 + gerar Postman collection automaticamente                                          |

**Entregáveis:** 5 módulos de plataforma + 15 módulos de squad funcionais e testados · Cobertura ≥ 80% por módulo · OpenAPI specs validadas (Spectral) · Postman collection · Event Catalog completo

**Definition of Done:**

- [ ] Todos os endpoints retornam respostas conformes com o schema OpenAPI (validação automatizada via Dredd ou Schemathesis)
- [ ] Eventos publicados no RabbitMQ e consumidos corretamente (teste de integração com broker real)
- [ ] Cobertura ≥ 80% no CI (bloqueante para merge)
- [ ] Zero vulnerabilidades críticas ou altas no Snyk (bloqueante para merge)
- [ ] PHPStan nível 8 sem erros

---

### Fase 4 — Frontend Modular — Design System e Dashboards

**Duração:** 4–5 semanas

**Skills necessárias:**

- Next.js 15 App Router (SSR/ISR, Server Components, route groups)
- React 19 + TypeScript 5.x strict mode
- TailwindCSS 4.x + CSS Modules
- Design System: Storybook 8, Atomic Design, design tokens
- State Management: TanStack Query 5, Zustand
- Testes: Jest + React Testing Library, Playwright, Chromatic

**Ferramentas:** Storybook 8 · Chromatic · Playwright · Lighthouse CI · axe-core · Bundle Analyzer (next-bundle-analyzer) · Figma

| #    | Tarefa                                                                                                                     |
| ---- | -------------------------------------------------------------------------------------------------------------------------- |
| 4.1  | Setup Next.js 15 App Router + TypeScript strict + TailwindCSS + ESLint/Prettier                                            |
| 4.2  | Design System: design tokens → Atoms (Button, Input, Badge, Icon) → Molecules (Card, Form, Alert) → documentação Storybook |
| 4.3  | NextAuth.js com provider OAuth2 (Drupal OIDC); protected routes por squad/role                                             |
| 4.4  | Camada de data fetching: TanStack Query + abstração de API client gerada do OpenAPI spec                                   |
| 4.5  | Dashboard Squad Atendimento: pipeline Kanban de leads, agenda, histórico de interações                                     |
| 4.6  | Dashboard Squad Marketing: editor de conteúdo com preview, galeria de assets, analytics overview                           |
| 4.7  | Dashboard Squad Projetos: viewer de projetos, upload BIM, galeria de renders, relatório de validação                       |
| 4.8  | Dashboard Squad Obras: cronograma Gantt, lista de materiais, checklist mobile-first, dashboard de custos                   |
| 4.9  | Dashboard Squad Suporte: busca semântica de documentos, viewer de relatórios, módulo de treinamento                        |
| 4.10 | Dashboard Squad Insights: feed de insights, cards de tendências, comparativo de benchmark                                  |
| 4.11 | WebSocket client (Socket.IO) para notificações em tempo real                                                               |
| 4.12 | PWA: Service Worker (Workbox), manifest.json, push notifications via Web Push API                                          |
| 4.13 | Testes: unitários (Jest/RTL), E2E Playwright (fluxos por squad), visual regression (Chromatic)                             |
| 4.14 | Lighthouse CI com performance budgets; axe-core no CI para acessibilidade                                                  |

**Entregáveis:** Design System documentado no Storybook (publicado) · 6 dashboards funcionais · PWA com offline básico · Cobertura ≥ 80% · Lighthouse Performance ≥ 85

**Definition of Done:**

- [ ] axe-core: zero violações A e AA em todas as páginas
- [ ] Lighthouse CI ≥ 85 performance, 100 accessibility (bloqueante)
- [ ] Chromatic visual regression aprovado no PR
- [ ] E2E Playwright passando nos fluxos críticos de cada squad
- [ ] Bundle size dentro do orçamento definido (< 200KB first load JS por rota)

---

### Fase 5 — Integração e Orquestração de IA

**Duração:** 3–4 semanas

**Skills necessárias:**

- Prompt Engineering: system prompts, few-shot, chain-of-thought, structured outputs
- LLM Integration: OpenAI API, function calling, streaming, Assistants API
- n8n: workflows visuais, custom nodes, error handling
- AI Observability: LangSmith/Helicone, token cost management
- AI Governance: PII masking, content moderation, prompt versioning

**Ferramentas:** n8n self-hosted (Kubernetes) · LangSmith ou Helicone · Microsoft Presidio · OpenAI Playground + Evals · YAML-based Prompt Registry (Git-versioned)

| #    | Tarefa                                                                                                                            |
| ---- | --------------------------------------------------------------------------------------------------------------------------------- |
| 5.1  | Deploy n8n no Kubernetes com persistent storage e backup automático de workflows                                                  |
| 5.2  | Configurar LangSmith/Helicone: logging de todas as chamadas LLM, dashboard de latência e custo por squad                          |
| 5.3  | Prompt Registry: estrutura YAML, CI para validação de schema, script de deploy de nova versão                                     |
| 5.4  | Desenvolver e testar prompts v1.0.0 para os 20 agentes (system + user template + few-shot + test cases)                           |
| 5.5  | Integrar `archtech_ai_gateway` com todos os providers: OpenAI, MidJourney (proxy), Runway                                         |
| 5.6  | Circuit breakers por provider com fallback para provider alternativo ou degradação graciosa                                       |
| 5.7  | PII masking (Presidio) no gateway antes de enviar dados para APIs externas                                                        |
| 5.8  | Content moderation de outputs gerados (OpenAI Moderation API)                                                                     |
| 5.9  | n8n workflows: lead_nurturing, meeting_scheduling, campaign_optimization, render_notification, checklist_alert, report_generation |
| 5.10 | Alertas de custo de IA por squad (threshold diário + semanal) no Grafana                                                          |
| 5.11 | Testes de qualidade: test cases por prompt, avaliação LLM-as-judge (amostragem 20% em staging)                                    |

**Entregáveis:** 20 agentes funcionais com prompts v1.0.0 versionados · n8n com workflows críticos ativos · Dashboard de AI Observability · Prompt Registry com test suite · Documentação de governança de IA

**Definition of Done:**

- [ ] 100% dos test cases do Prompt Registry passando por agente
- [ ] PII masking validado com dataset de teste (zero PII em logs de chamadas externas)
- [ ] Custo por squad monitorado e dentro do budget estimado
- [ ] Circuit breakers testados com falha simulada de cada provider
- [ ] n8n workflows com error handling e DLQ configurados

---

### Fase 6 — Testes Abrangentes, Segurança e QA

**Duração:** 2–3 semanas

**Skills necessárias:**

- QA: testes E2E, testes de carga, testes de caos
- Security: penetration testing, OWASP ZAP, análise de vulnerabilidades
- Performance Testing: k6, JMeter, análise de bottlenecks
- Acessibilidade: WCAG 2.1, testes com tecnologias assistivas

**Ferramentas:** k6 · OWASP ZAP · Burp Suite · Playwright · Grafana k6 Cloud · Litmus Chaos · NVDA + VoiceOver

| #   | Tarefa                                                                                                 |
| --- | ------------------------------------------------------------------------------------------------------ |
| 6.1 | Testes E2E: 1 cenário feliz + 2 cenários de erro por fluxo crítico de cada squad                       |
| 6.2 | Testes de carga k6: ramp-up até 10.000 usuários simultâneos, validar SLOs de latência                  |
| 6.3 | Testes de carga de IA: 100 requisições paralelas por agente, validar circuit breakers                  |
| 6.4 | Pentest OWASP Top 10: SQL injection, XSS, CSRF, IDOR, broken authentication, security misconfiguration |
| 6.5 | Auditoria de acessibilidade: NVDA (Windows) + VoiceOver (macOS) nos 6 dashboards                       |
| 6.6 | Testes de recuperação: falha de banco, RabbitMQ, AI provider — validar comportamento e MTTR            |
| 6.7 | Testes de backup e restore: executar restore completo em ambiente isolado                              |
| 6.8 | UAT com stakeholders: sessions por squad, coleta de feedback estruturado                               |
| 6.9 | Corrigir todos os bugs críticos e altos; re-testar e documentar resolução                              |

**Entregáveis:** Relatório de testes de carga (k6) · Relatório de pentest com severidade e status · Relatório de acessibilidade · Relatório de DR (RTO/RPO medidos) · Ata de aprovação de UAT

**Definition of Done:**

- [ ] Zero vulnerabilidades críticas ou altas não resolvidas antes do go-live
- [ ] SLOs de performance atingidos sob carga de 10.000 usuários simultâneos
- [ ] UAT aprovado em ≥ 80% dos cenários pelos stakeholders
- [ ] RTO ≤ 4h demonstrado em exercício de DR documentado
- [ ] Zero violações WCAG AA nos 6 dashboards

---

### Fase 7 — Deploy, Monitoramento e Otimização Contínua

**Duração:** 1–2 semanas + ciclo contínuo

**Skills necessárias:**

- SRE: SLOs, error budgets, on-call, runbooks
- Release Management: canary deployments, feature flags, rollback
- FinOps: otimização de custos cloud e IA

**Ferramentas:** Argo CD (GitOps) · PagerDuty · Checkly · AWS Cost Explorer / GCP Billing

| #   | Tarefa                                                                                       |
| --- | -------------------------------------------------------------------------------------------- |
| 7.1 | Deploy em produção via GitOps (Argo CD) com sync automático do repositório de manifests Helm |
| 7.2 | Checkly: synthetic tests nos fluxos críticos a cada 5 minutos                                |
| 7.3 | PagerDuty: escalation policies, on-call rotations por squad, runbooks linkados nos alertas   |
| 7.4 | Dashboards de negócio no Grafana por squad + SLO dashboard público interno                   |
| 7.5 | Canary deployment: 5% → 25% → 100% com análise automática de error rate                      |
| 7.6 | Revisão de custos cloud e IA após 2 semanas — ajustar recursos e modelos                     |
| 7.7 | Retrospectiva técnica: lessons learned, atualizar ADRs e runbooks                            |
| 7.8 | Planejar Fase 8 baseado em feedback de UAT e métricas de negócio                             |

**Definition of Done:**

- [ ] SLOs atingidos por 7 dias consecutivos em produção
- [ ] Nenhum incidente P1 nas primeiras 2 semanas pós-launch
- [ ] On-call treinado com runbooks dos 5 cenários de falha mais prováveis por squad
- [ ] Plano de roadmap da Fase 8 aprovado pelo PO

---

## 11. Governança de IA

### Prompt Registry

Estrutura YAML de um prompt versionado:

```yaml
id: ia-atendimento/qualificadora
version: 1.0.0
squad: atendimento
agent: IA Qualificadora
model: gpt-4o
max_tokens: 1000
temperature: 0.3
changelog:
  - version: 1.0.0
    date: 2025-06-01
    author: ai-team
    description: Versão inicial

system_prompt: |
  Você é um assistente de qualificação de leads para escritórios de arquitetura.
  Analise os dados do lead e atribua um score de 0 a 100 baseado nos critérios:
  - Orçamento compatível com o escopo (peso 40%)
  - Prazo realista (peso 30%)
  - Fit com os serviços do escritório (peso 30%)
  Responda APENAS com JSON no formato especificado.

user_template: |
  Dados do lead:
  - Tipo de projeto: {{tipo_projeto}}
  - Orçamento estimado: R$ {{orcamento_estimado}}
  - Prazo desejado: {{prazo_desejado}}
  - Informações adicionais: {{notas}}

  Retorne: {"score": 0-100, "justificativa": "...", "proximos_passos": "..."}

test_cases:
  - input:
      tipo_projeto: 'residencial'
      orcamento_estimado: 500000
      prazo_desejado: '12 meses'
      notas: 'Casa de alto padrão, 400m²'
    expected:
      score_min: 70
      score_max: 95
  - input:
      tipo_projeto: 'residencial'
      orcamento_estimado: 15000
      prazo_desejado: '1 mês'
      notas: 'Apartamento pequeno'
    expected:
      score_min: 0
      score_max: 30
```

### PII Handling

Antes de qualquer chamada a APIs externas de IA, o `archtech_ai_gateway` passa o payload pelo Microsoft Presidio:

```
Dados identificados e mascarados:
  - CPF / CNPJ          → [CPF_REDACTED]
  - Email               → [EMAIL_REDACTED]
  - Telefone            → [PHONE_REDACTED]
  - Nome completo       → [NAME_REDACTED]
  - Endereço            → [ADDRESS_REDACTED]

Auditoria:
  - Log de cada mascaramento com campo e tipo de PII
  - Amostragem mensal de 5% das chamadas para validação
  - Alerta se PII detectada em logs de resposta de APIs externas
```

### Custo e Monitoramento

```
Dashboard por squad no Grafana:
  - Tokens consumidos por modelo (input + output)
  - Custo estimado em USD (tokens × preço do modelo)
  - Latência P50/P90/P99 por agente
  - Taxa de erros por provider

Alertas configurados:
  - Custo diário > $X por squad  → alerta Slack para AI team lead
  - Custo semanal > $Y total     → alerta PagerDuty
  - Latência P99 > 30s           → alerta imediato
  - Error rate > 5%              → circuit breaker + alerta
```

---

## 12. Estratégia de Dados e LGPD

### Arquitetura de Dados

```
OLTP (operacional):
  PostgreSQL 18 (Managed)
    ├── Módulos de squad (dados de negócio)
    ├── pgvector (embeddings de documentos)
    └── PostGIS (dados geoespaciais de projetos)

OLAP (analytics):
  Pipeline: Drupal → Airbyte/dbt → BigQuery/Snowflake
  Visualização: Grafana + Metabase

Cache:
  Redis 7+ (sessão, object cache, rate limiting, feature flags)

Busca semântica:
  pgvector (primary) ou Pinecone (se volume > 1M documentos)
```

### Compliance LGPD / GDPR

| Requisito               | Implementação                                                                            |
| ----------------------- | ---------------------------------------------------------------------------------------- |
| Classificação de dados  | Todos os Content Types têm campo `sensibilidade` (público/interno/restrito/confidencial) |
| Consentimento           | Registro de consentimento com timestamp em Lead.historico_interacoes                     |
| Direito ao esquecimento | `DELETE /api/v1/users/{id}/data` — anonimiza PII em todos os content types relacionados  |
| Retenção                | Dados de Lead: 2 anos após última interação. Job mensal de anonimização.                 |
| Portabilidade           | `GET /api/v1/users/{id}/export` — exporta dados em formato JSON/CSV                      |
| Notificação de breach   | Processo documentado no runbook de segurança. ANPD notificada em ≤ 72h.                  |

### Backup e Recovery

```
Estratégia:
  - Backup automático diário às 2h (horário local)
  - Retenção: 30 dias de backups diários + 12 meses de backups mensais
  - Point-in-time recovery habilitado (até 7 dias para trás)
  - Backup de configurações Drupal (config_split) a cada deploy

Targets:
  - RTO (Recovery Time Objective): ≤ 4 horas
  - RPO (Recovery Point Objective): ≤ 1 hora

Teste obrigatório:
  - Restore completo em ambiente isolado mensalmente
  - Resultado documentado no Confluence + alerta se RTO ultrapassado
```

---

## 13. Segurança

### Autenticação e Autorização

```
Fluxo de autenticação:
  1. Frontend → NextAuth.js → /oauth2/authorize (Drupal)
  2. Drupal valida credenciais + MFA (TOTP)
  3. Drupal emite JWT (access token 15min + refresh token 7 dias)
  4. Frontend usa access token em Authorization: Bearer
  5. API Gateway valida JWT antes de encaminhar ao backend
  6. Backend verifica permissões RBAC por squad

Papéis disponíveis:
  super_admin       → acesso total
  squad_lead_*      → gestão do squad específico
  squad_member_*    → operações do squad específico
  viewer_*          → leitura apenas no squad específico
  ai_operator       → gestão do Prompt Registry e custos de IA
```

### Headers de Segurança

```http
Strict-Transport-Security: max-age=63072000; includeSubDomains; preload
Content-Security-Policy: default-src 'self'; [policy completa em ./docs/security-guidelines/csp.md]
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

### Checklist de Segurança por PR

Antes de qualquer merge em `main`:

- [ ] Nenhum secret, API key ou credencial no código (Gitleaks scan automático)
- [ ] Dependências novas aprovadas pela análise Snyk
- [ ] Endpoints novos têm autenticação e autorização documentadas
- [ ] Dados pessoais tratados com mascaramento e classificação adequados
- [ ] Logs não expõem PII ou dados sensíveis

---

## 14. Observabilidade

### Stack

| Camada           | Ferramenta             | Propósito                                                |
| ---------------- | ---------------------- | -------------------------------------------------------- |
| Métricas         | Prometheus + Grafana   | RED metrics (Rate, Errors, Duration) por serviço e squad |
| Logs             | Loki (ou ELK)          | Logs estruturados JSON com trace_id e correlação         |
| Tracing          | Jaeger + OpenTelemetry | Distributed tracing ponta a ponta (frontend → IA)        |
| Uptime           | Checkly                | Synthetic monitoring de fluxos críticos a cada 5 min     |
| Alertas          | PagerDuty              | On-call com escalation policies por squad                |
| AI               | LangSmith / Helicone   | Tracing específico de chamadas LLM com inputs/outputs    |
| Erros (frontend) | Sentry                 | Error tracking com source maps e session replay          |

### Dashboards no Grafana

```
1. SLO Dashboard (visível a todos)
   - Uptime por serviço (últimos 30 dias)
   - P90/P99 de latência por endpoint crítico
   - Error budget consumption

2. Por Squad (visível ao squad + leads)
   - Métricas de negócio (conversão, engajamento, etc.)
   - Latência dos agentes de IA
   - Volume de eventos no RabbitMQ

3. AI Costs (visível ao AI team + C-level)
   - Custo diário/semanal/mensal por squad e modelo
   - Tokens consumidos por agente
   - Latência P50/P90/P99 por provider

4. Infrastructure (visível ao Platform team + SRE)
   - CPU/Memory por pod
   - Queue depth RabbitMQ por fila
   - PostgreSQL: conexões, IOPS, slow queries
```

### Runbooks Obrigatórios

Cada squad deve ter runbooks para os seguintes cenários antes do go-live:

1. **Serviço indisponível** — diagnóstico inicial, escalação, rollback
2. **Queue DLQ com mensagens** — investigação, reprocessamento ou descarte
3. **AI provider indisponível** — ativação de fallback, comunicação ao usuário
4. **Banco de dados lento** — identificação de slow queries, ação imediata
5. **Deploy com falha** — rollback via Argo CD, notificação de stakeholders

---

_Fim do documento PRD v2.0 — ArchTech Suite Enhanced_

---

> **Como contribuir com este documento:** Abra um PR na branch `docs/prd-updates`. ADRs novos devem ser adicionados em `./docs/adr/ADR-XXX-titulo.md` seguindo o template em `./docs/adr/TEMPLATE.md`. Mudanças em Data Contracts exigem revisão do squad emissor e de todos os squads consumidores antes do merge.
