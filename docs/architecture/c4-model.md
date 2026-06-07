# ArchTech Suite — Modelo C4

## Nível 1 — Contexto

```
[Cliente (Browser/Mobile)] → [ArchTech Suite]
                                    ↓
                      [Provedores IA (OpenAI, etc)]
                      [Serviços Externos (RabbitMQ, Redis)]
```

## Nível 2 — Containers

```
[Frontend: Next.js 15] ↔ [API Gateway: Drupal JSON:API]
                              ↓
                    ┌─────────────────┐
                    │  Backend Drupal  │
                    │  11 Headless     │
                    └────────┬────────┘
                             ↓
              ┌──────────────┼──────────────┐
              ↓              ↓              ↓
        [PostgreSQL]    [Redis]       [RabbitMQ]
        18 + pgvector    7+ cache     3.13 events
```

## Nível 3 — Componentes (Drupal)

```
Backend Drupal 11
├── archtech_core_api       ← Controller base, paginação, filtros
├── archtech_security       ← OAuth2/OIDC, RBAC, audit trail
├── archtech_events         ← Event bus (RabbitMQ + Outbox)
├── archtech_feature_flags  ← Feature flags (Redis)
├── archtech_ai_gateway     ← Proxy IA + Prompt Registry
│
├── ia_client_portal        ← Bounded Context Client Portal
├── ia_crm                  ← Bounded Context CRM
├── ia_proposals            ← Bounded Context Propostas
├── ia_financeiro           ← Bounded Context Financeiro
├── ia_library              ← Bounded Context Biblioteca
├── ia_permits              ← Bounded Context Aprovação
├── ia_suppliers            ← Bounded Context Fornecedores
├── ia_facilities           ← Bounded Context Facilities
├── ia_bim_twin             ← Bounded Context BIM
└── ia_marketplace          ← Bounded Context Marketplace
```

## Nível 4 — Código

Ver arquitetura detalhada por módulo em `archtech-prd.md` seção 6 e content types em `archtech-prd-enhanced.xml`.
