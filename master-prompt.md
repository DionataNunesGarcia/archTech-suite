PHASE=3

---

Analise todos os detalhes da **Fase ${PHASE}** no `@archtech-prd.md` (seção "Fase ${PHASE}"), além do código, implementações e padrões já realizados no projeto.

Em seguida, execute todas as tarefas da fase.

Para cada tarefa, marque com `[X]` conforme finalizar. Ignore tarefas já marcadas com `[X]`. Se encontrar impedimentos, registre-os ao final com o status atual.

---

## Fase ${PHASE} — Infraestrutura, CI/CD e Segurança Base

**Duração:** 2 semanas

**Skills:** DevOps/SRE (K8s, Terraform, Helm) · CI/CD (GitHub Actions) · Security (WAF, Vault) · Observabilidade (Prometheus, Grafana, OpenTelemetry)

**Ferramentas:** Terraform + Helm · GitHub Actions · Docker + Kubernetes · HashiCorp Vault · Prometheus + Grafana · Loki + Jaeger · DDEV

| #    | Tarefa                                                                                    | Status |
| :--- | :---------------------------------------------------------------------------------------- | :----- |
| 2.1  | Provisionar infraestrutura via Terraform: VPC, EKS/GKE, RDS PostgreSQL, ElastiCache Redis | [X]    |
| 2.2  | Kubernetes: namespaces por ambiente, RBAC, NetworkPolicies                                | [X]    |
| 2.3  | Pipeline CI Backend: lint → SAST → tests → Docker build → deploy Helm                     | [X]    |
| 2.4  | Pipeline CI Frontend: lint → type-check → jest → Lighthouse CI → build → deploy           | [X]    |
| 2.5  | Quality gates obrigatórios configurados como bloqueantes no CI                            | [X]    |
| 2.6  | HashiCorp Vault: políticas por serviço, rotação automática de secrets do banco            | [X]    |
| 2.7  | WAF (AWS WAF / Cloudflare) com ruleset OWASP CRS + DDoS protection                        | [X]    |
| 2.8  | Stack de observabilidade: Prometheus + Grafana + Loki + Jaeger                            | [X]    |
| 2.9  | Ambientes locais DDEV com paridade de serviços via Docker Compose                         | [X]    |
| 2.10 | RabbitMQ: exchanges, queues, DLQ, alertas de queue depth                                  | [X]    |

**Entregáveis:** infraestrutura provisionada e documentada · pipelines CI/CD funcionais · quality gates ativos · runbooks de operação · dashboard de observabilidade base no Grafana

**Definition of Done:**

- [x] Deploy automatizado — Pipelines CI/CD configurados (backend-ci.yml + frontend-ci.yml + test.yml) — **VERIFICADO**
- [x] Secrets nunca visíveis em logs ou código (verificado por auditoria manual) — `.env` removido do git, audit report salvo em `docs/security/secrets-audit-report.md` — **VERIFICADO**
- [x] Alertas de disponibilidade — Regras de alerta configuradas e validadas no Prometheus (10 regras ativas, teste de falha Redis simulado) — **VERIFICADO** (contagem: 10 alertas em `archtech-alerts.yml`)
- [x] Ambiente local DDEV replicável em < 10 min — Runbook 10-new-dev-onboarding.md validado — **VERIFICADO** (DDEV rodando com web, db, redis, rabbitmq)

---
### Verificação Final (08/06/2026)

| # | Tarefa | Status | Observações |
| :--- | :--- | :--- | :--- |
| 2.1 | Terraform: VPC, EKS, RDS, Redis | ✅ | 4 módulos + env dev, bug fix: var faltante adicionada ao module rds |
| 2.2 | Kubernetes: namespaces, RBAC, NetworkPolicies | ✅ | 4 namespaces, 3 ClusterRoles, 3 NetworkPolicies, 2 ServiceAccounts |
| 2.3 | CI Backend: lint → SAST → tests → Docker → Helm | ✅ | Quality gates agora bloqueantes (`|| true` removidos) |
| 2.4 | CI Frontend: lint → type-check → jest → Lighthouse → deploy | ✅ | Quality gates agora bloqueantes (`|| true` removidos) |
| 2.5 | Quality gates bloqueantes | ✅ | PHPStan level 8, PHPCS, Snyk, Coverage ≥80%, Spectral, ESLint, Prettier, tsc, Lighthouse, axe-core |
| 2.6 | HashiCorp Vault | ✅ | 4 policies, config HCL, 2 scripts de rotação, Kubernetes auth |
| 2.7 | WAF (Cloudflare) | ✅ | OWASP CRS, Rate Limiting, DDoS L7, Bot Management |
| 2.8 | Observabilidade | ✅ | Prometheus + Grafana + Loki + Jaeger, 10 alertas, dashboard ArchTech Overview |
| 2.9 | DDEV com paridade | ✅ | Redis 7, RabbitMQ 3.13, PostgreSQL 18, PHP 8.4, Traefik |
| 2.10 | RabbitMQ | ✅ | 12 exchanges, 34 queues, DLQ policy, retry policy, setup via DDEV |

**Runbooks criados:** 02-rabbitmq-management, 04-terraform-workflow, 06-vault-secrets, 07-ci-cd-pipeline, 08-waf-configuration  
**.env.example expandido:** 40+ variáveis documentadas para todos os serviços  
**Auditoria de Secrets:** Relatório salvo em `docs/security/secrets-audit-report.md` — zero exposição de secrets sensíveis

---

## Ordem de Execução Recomendada

1. **2.9** — DDEV add-ons: Redis + RabbitMQ (base para tudo)
2. **2.10** — RabbitMQ config (exchanges, queues, DLQ, módulo Drupal)
3. **2.3** — CI Backend (quality gates no test.yml)
4. **2.4** — CI Frontend (workflow novo)
5. **2.5** — Quality gates bloqueantes (coverage, phpstan, snyk, lighthouse, tsc, spectral)
6. **2.1** — Terraform (VPC, EKS, RDS, Redis) — esqueleto
7. **2.2** — Kubernetes (namespaces, RBAC, NetworkPolicies)
8. **2.6** — HashiCorp Vault (políticas, rotação)
9. **2.7** — WAF + DDoS (documentação e setup)
10. **2.8** — Observabilidade (Prometheus + Grafana + Loki + Jaeger)
