PHASE=2

---

Analise todos os detalhes da **Fase ${PHASE}** no `@archtech-prd.md` (seção "Fase ${PHASE}"), além do código, implementações e padrões já realizados no projeto.

Em seguida, execute todas as tarefas da fase.

Para cada tarefa, marque com `[X]` conforme finalizar. Ignore tarefas já marcadas com `[X]`. Se encontrar impedimentos, registre-os ao final com o status atual.

---

## Fase ${PHASE} — Infraestrutura, CI/CD e Segurança Base

**Duração:** 2 semanas

**Skills:** DevOps/SRE (K8s, Terraform, Helm) · CI/CD (GitHub Actions) · Security (WAF, Vault) · Observabilidade (Prometheus, Grafana, OpenTelemetry)

**Ferramentas:** Terraform + Helm · GitHub Actions · Docker + Kubernetes · HashiCorp Vault · Prometheus + Grafana · Loki + Jaeger · DDEV

| # | Tarefa | Status |
|:--|:-------|:------|
| 2.1 | Provisionar infraestrutura via Terraform: VPC, EKS/GKE, RDS PostgreSQL, ElastiCache Redis | [X] |
| 2.2 | Kubernetes: namespaces por ambiente, RBAC, NetworkPolicies | [X] |
| 2.3 | Pipeline CI Backend: lint → SAST → tests → Docker build → deploy Helm | [X] |
| 2.4 | Pipeline CI Frontend: lint → type-check → jest → Lighthouse CI → build → deploy | [X] |
| 2.5 | Quality gates obrigatórios configurados como bloqueantes no CI | [X] |
| 2.6 | HashiCorp Vault: políticas por serviço, rotação automática de secrets do banco | [X] |
| 2.7 | WAF (AWS WAF / Cloudflare) com ruleset OWASP CRS + DDoS protection | [X] |
| 2.8 | Stack de observabilidade: Prometheus + Grafana + Loki + Jaeger | [X] |
| 2.9 | Ambientes locais DDEV com paridade de serviços via Docker Compose | [X] |
| 2.10 | RabbitMQ: exchanges, queues, DLQ, alertas de queue depth | [X] |

**Entregáveis:** infraestrutura provisionada e documentada · pipelines CI/CD funcionais · quality gates ativos · runbooks de operação · dashboard de observabilidade base no Grafana

**Definition of Done:**
- [X] Deploy automatizado — Pipelines CI/CD configurados (backend-ci.yml + frontend-ci.yml + test.yml)
- [ ] Secrets nunca visíveis em logs ou código (verificado por auditoria manual pendente)
- [ ] Alertas de disponibilidade — Regras de alerta configuradas no Prometheus (archtech-alerts.yml)
- [X] Ambiente local DDEV replicável em < 10 min — Runbook 10-new-dev-onboarding.md validado

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
