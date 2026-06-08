# ADR-008 · Synthetic Monitoring with Checkly

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2026-06-08 |

**Decisão:** Usar Checkly para synthetic monitoring dos endpoints críticos.

**Contexto:** Testes E2E pós-deploy precisam validar continuamente que os serviços estão respondendo corretamente.

**Justificativa:**
- Configuração como código (IaC) via CLI
- Browser checks com Playwright para fluxos E2E
- API checks para endpoints REST
- Múltiplas localizações globais (us-east-1, sa-east-1, eu-west-1)
- Integração com PagerDuty para alertas

**Alternativas descartadas:**
- **Datadog Synthetics** — Vendor lock-in, custo mais alto
- **Pingdom** — Sem browser checks, sem IaC
- **Custom Prometheus blackbox exporter** — Sem browser checks, sem alerting nativo

**Configuração:** `infrastructure/checkly/` com config + API checks + browser checks.
