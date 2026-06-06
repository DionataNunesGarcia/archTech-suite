# ArchTech Suite — Visão Arquitetural

## Stack

| Camada | Tecnologia | Versão |
|--------|------------|--------|
| Backend | Drupal | 11 (headless, JSON:API + GraphQL) |
| Linguagem | PHP | 8.4+ |
| Frontend | Next.js | 15 (App Router) |
| UI | React + TailwindCSS | 19 / 4.x |
| Banco | PostgreSQL | 18 (+ pgvector, PostGIS) |
| Cache | Redis | 7+ |
| Message Broker | RabbitMQ | 3.13+ |
| Workflows | n8n | self-hosted (Kubernetes) |

## Camadas

```
Cliente (Browser / Mobile PWA / Desktop App)
    ↓ HTTPS / WebSocket
API Gateway (WAF · OAuth2 · Rate Limiting · DDoS)
    ↓
Frontend (Next.js 15 + React 19)
    ↓ JSON:API / GraphQL / REST
Backend (Drupal 11 + PHP 8.4)
  ├── Plataforma (shared): core_api · security · events · ai_gateway · feature_flags
  └── Squads (isolados): 6 bounded contexts, comunicam via domain events
    ↓ AMQP
AI Orchestration (n8n · AI Service Layer · Prompt Registry)
    ↓
Dados (PostgreSQL + pgvector · Redis · RabbitMQ · Vault/KMS)
```

## Bounded Contexts

| Módulo | Bounded Context | Squad |
|--------|----------------|-------|
| `ia_client_portal` | `client_portal` | Portal do Cliente |
| `ia_crm` | `architecture_crm` | CRM |
| `ia_proposals` | `commercial_proposals` | Propostas |
| `ia_financeiro` | `financial_management` | Financeiro |
| `ia_library` | `technical_library` | Biblioteca Técnica |
| `ia_permits` | `permit_approval` | Aprovação |
| `ia_suppliers` | `supplier_management` | Fornecedores |
| `ia_facilities` | `facilities_post_construction` | Facilities |
| `ia_bim_twin` | `bim_digital_twin` | BIM |

## ADRs

7 ADRs registrados em `docs/adr/`. Decisões principais:
- Drupal 11 headless (ADR-001)
- Next.js 15 App Router (ADR-002)
- RabbitMQ + Redis (ADR-003)
- n8n self-hosted (ADR-004)
- PostgreSQL 18 gerenciado (ADR-005)
- Bounded Contexts isolados via Recipes (ADR-006)
- Contract-First API Design (ADR-007)
