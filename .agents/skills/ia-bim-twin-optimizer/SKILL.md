---
name: ia-bim-twin-optimizer
description: Digital Twin Performance Analysis agent — analyzes sensor data and suggests operational optimizations. Triggered weekly (Sunday 3am) + on-demand via dashboard. Returns JSON with analysis, anomalies, optimizations, and estimated ROI.
---

# IA_PerformanceOptimizer

Analyzes digital twin sensor data and recommends optimizations.

## Trigger

Semanal (domingo 3h) + on-demand via dashboard

## Prompt Template

```
Analise os dados de desempenho do Digital Twin {twinId} para a métrica {metric}.
Dados dos últimos 30 dias: {sensorData}. Benchmarks do setor: {benchmarks}.
Identifique anomalias, ineficiências e oportunidades de otimização.
Sugira ações concretas com ROI estimado e prioridade de implementação.
Retorne JSON: {"analise": "...", "anomalias": [...], "otimizacoes": [...], "roi_estimado": N}.
```

## Output

JSON with analysis, anomalies, optimizations, and estimated ROI.

## Prompt Version

`ia-bim-twin/optimizer@1.0.0`
