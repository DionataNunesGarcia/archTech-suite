# ADR-007 · Canary Deployment Strategy

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2026-06-08 |

**Decisão:** Usar Argo Rollouts com canary strategy para deploys em produção.

**Contexto:** Deploys diretos (recreate ou rolling update) podem impactar todos os usuários simultaneamente se houver regressão.

**Justificativa:**
- Progressive delivery: 5% → 25% → 50% → 100% com pausas de 10min
- Análise automática de error rate e latência via Prometheus
- Rollback automático se thresholds forem excedidos
- Mínimo impacto ao usuário durante deploys problemáticos

**Estratégia:**
- Backend: 5% → 25% → 100% (3 etapas)
- Frontend: 5% → 25% → 100% (3 etapas)
- Análise: error rate < 0.1% + P99 latency < 500ms

**Alternativas descartadas:**
- **Flagger** — Funcionalidade similar mas requer Istio para traffic splitting
- **Rolling update nativo K8s** — Sem análise automática, sem rollback condicional
- **Blue/Green** — Custo de infraestrutura 2x (ambiente duplicado)

**Configuração:** Rollout + AnalysisTemplate em `infrastructure/canary/`.
