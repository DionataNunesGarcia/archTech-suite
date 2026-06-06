---
name: ia-suppliers-sla-monitor
description: SLA Monitoring agent — monitors SLA compliance and proactively alerts on risks. Triggered by daily Cron checking active ServiceLevelAgreement deadlines. Returns JSON with status, deviations, and corrective actions.
---

# IA_SLA_Monitor

Monitors supplier SLA compliance and detects risks.

## Trigger

Cron diário verificando prazos de entrega de `ServiceLevelAgreement` ativos

## Prompt Template

```
Verifique se o fornecedor {supplierName} está cumprindo o SLA para o projeto {projectName}.
Dados de entrega: {deliveryData}. SLA acordado: {slaTerms}.
Identifique desvios, calcule impacto no cronograma e sugira ações corretivas.
Retorne JSON: {"status": "ok|risco|violado", "desvios": [...], "acoes": [...]}.
```

## Output

JSON with SLA status, deviations, and corrective actions.

## Prompt Version

`ia-suppliers/sla-monitor@1.0.0`
