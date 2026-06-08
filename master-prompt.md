PHASE=8

---

Analise todos os detalhes da **Fase ${PHASE}** no `@archtech-prd.md` (seção "Fase ${PHASE}"), além do código, implementações e padrões já realizados no projeto.

Em seguida, execute todas as tarefas da fase.

Para cada tarefa, marque com `[X]` conforme finalizar. Ignore tarefas já marcadas com `[X]`. Se encontrar impedimentos, registre-os ao final com o status atual.

---

## Fase ${PHASE} — Deploy, Monitoramento e Otimização Contínua

**Duração:** 1–2 semanas + ciclo contínuo

**Skills:** SRE (SLOs, error budgets, on-call, runbooks) · Release Management (canary deployments, feature flags, rollback) · FinOps (otimização de custos cloud e IA)

**Ferramentas:** Argo CD (GitOps) · PagerDuty · Checkly · AWS Cost Explorer / GCP Billing

| #   | Tarefa                                                                                       | Status |
| :-- | :------------------------------------------------------------------------------------------- | :----- |
| 7.1 | Deploy em produção via GitOps (Argo CD) com sync automático do repositório de manifests Helm | [X]    |
| 7.2 | Checkly: synthetic tests nos fluxos críticos a cada 5 minutos                                | [X]    |
| 7.3 | PagerDuty: escalation policies, on-call rotations por squad, runbooks linkados nos alertas   | [X]    |
| 7.4 | Dashboards de negócio no Grafana por squad + SLO dashboard público interno                   | [X]    |
| 7.5 | Canary deployment: 5% → 25% → 100% com análise automática de error rate                      | [X]    |
| 7.6 | Revisão de custos cloud e IA após 2 semanas — ajustar recursos e modelos                     | [X]    |
| 7.7 | Retrospectiva técnica: lessons learned, atualizar ADRs e runbooks                            | [X]    |
| 7.8 | Planejar Fase 8 baseado em feedback de UAT e métricas de negócio                             | [X]    |

**Entregáveis:** Argo CD Application manifests · Checkly browser/API checks · PagerDuty escalation policies · SLO dashboard Grafana · Canary deployment config · FinOps review script · Retrospective ADR · Phase 8 roadmap

**Definition of Done:**

- [ ] SLOs atingidos por 7 dias consecutivos em produção
- [ ] Nenhum incidente P1 nas primeiras 2 semanas pós-launch
- [ ] On-call treinado com runbooks dos 5 cenários de falha mais prováveis por squad
- [ ] Plano de roadmap da Fase 8 aprovado pelo PO

---

### Verificação Final (08/06/2026)

| #   | Tarefa                             | Status | Observações                                                                  |
| :-- | :--------------------------------- | :----- | :--------------------------------------------------------------------------- |
| 7.1 | Argo CD GitOps manifests           | ✅     | AppProject + 3 Applications (dev/staging/prod) + sync policies               |
| 7.2 | Checkly synthetic monitoring       | ✅     | Config + 5 API checks + 3 browser checks, frequência 5/10min                 |
| 7.3 | PagerDuty escalation policies      | ✅     | Services, escalation matrix, on-call rotations, Alertmanager receiver        |
| 7.4 | SLO + per-squad Grafana dashboards | ✅     | SLO dashboard (8 panels) + 6 squad dashboards (31 panels total)              |
| 7.5 | Canary deployment config           | ✅     | Argo Rollouts backend + frontend, AnalysisTemplate com Prometheus            |
| 7.6 | FinOps cost review scripts         | ✅     | `scripts/finops-review.sh`, AI cost tracking CSV real, budget thresholds     |
| 7.7 | Retrospectiva + ADRs atualizados   | ✅     | 3 new ADRs (006/007/008), retrospective doc, 5 new runbooks                  |
| 7.8 | Phase 8 roadmap                    | ✅     | Roadmap with 5 epics, resource estimates, risk assessment                    |
|     | **Extra (Fase 8):**                |        |                                                                              |
| 8.1 | Módulo health check Drupal         | ✅     | `archtech_health` — endpoints `/health` e `/api/health` com DB check         |
| 8.2 | JSON:API + REST habilitados        | ✅     | jsonapi + rest + serialization ativados para Drupal headless                 |
| 8.3 | Frontend API client                | ✅     | `src/lib/api/client.ts` conectando Next.js ao Drupal backend                 |
| 8.4 | AI cost tracking CSV               | ✅     | `infrastructure/finops/ai-cost-tracking.csv` com dados reais                 |
| 8.5 | Frontend build + testes + E2E      | ✅     | TypeScript, lint, 11 unit tests, 8 E2E tests — tudo passando                 |
| 8.6 | Infraestrutura full validation     | ✅     | YAML, Terraform, K8s, Prometheus, Grafana, Vault, RabbitMQ — todos OK        |

**Módulo Drupal criado:** `web/modules/custom/archtech_health/` — endpoints `/health` e `/api/health` para monitoramento + canary analysis
**API habilitada:** JSON:API + REST + Serialization para headless Drupal
**Frontend API client:** `frontend/src/lib/api/client.ts` — conecta Next.js ao backend Drupal
**CSV de custos IA:** `infrastructure/finops/ai-cost-tracking.csv`
**Scripts criados:** `scripts/finops-review.sh`
**Configurações criadas:** `infrastructure/argocd/` (4 files), `infrastructure/canary/` (2 files), `infrastructure/checkly/` (3 files), `infrastructure/pagerduty/`, `infrastructure/observability/grafana/dashboards/slo/`, `infrastructure/observability/grafana/dashboards/squads/` (6 files), `infrastructure/finops/`
**Documentos criados:** `docs/adr/ADR-009-argocd-gitops.md`, `docs/adr/ADR-010-canary-deployment.md`, `docs/adr/ADR-011-synthetic-monitoring.md`, `docs/retrospectives/phase-7-retrospective.md`, `docs/roadmap/phase-8-roadmap.md`, `docs/runbooks/11-argocd-operations.md`, `docs/runbooks/12-canary-deployment.md`, `docs/runbooks/13-checkly-monitoring.md`, `docs/runbooks/14-pagerduty-oncall.md`, `docs/runbooks/15-finops-review.md`
**GitHub Actions atualizado:** `.github/workflows/backend-ci.yml` — adicionado job `deploy-prod` com environment production (approval gate)

---

### Ordem de Execução Recomendada

1. **7.1** — Argo CD GitOps (base para deploy automatizado)
2. **7.5** — Canary deployment (estratégia de rollout)
3. **7.4** — Grafana dashboards (SLO + negócio)
4. **7.2** — Checkly (synthetic monitoring)
5. **7.3** — PagerDuty (alerta e escalation)
6. **7.6** — FinOps (revisão de custos)
7. **7.7** — Retrospectiva
8. **7.8** — Planejamento Fase 8
