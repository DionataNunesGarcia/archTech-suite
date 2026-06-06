---
name: ia-financeiro-cashflow-predictor
description: Cash Flow Forecasting agent — predicts cash flow for next months based on history, receivables, payables, and active projects. Triggered weekly (Monday 7am) + on-demand via dashboard. Returns JSON with monthly projections and alerts.
---

# IA_CashFlowPredictor

Forecasts cash flow using financial data and active contracts.

## Trigger

Semanal (segunda-feira 7h) + on-demand via dashboard

## Prompt Template

```
Preveja o fluxo de caixa para os próximos {months} meses com base nos dados financeiros atuais:
Recebíveis pendentes: {receivablesSummary}. Pagamentos previstos: {payablesSummary}.
Projetos em andamento: {activeProjects}. Sazonalidade histórica: {seasonalityData}.
Retorne em JSON: {"meses": [{"mes": "YYYY-MM", "receita_prevista": N, "despesa_prevista": N, "saldo": N}], "alertas": [...]}.
```

## Prompt Version

`ia-financeiro/cashflow-predictor@1.0.0`
