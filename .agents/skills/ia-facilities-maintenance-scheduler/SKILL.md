---
name: ia-facilities-maintenance-scheduler
description: Predictive Maintenance Scheduling agent — generates preventive schedules based on construction type and history. Triggered by ProjectDelivered event + annual cron review. Returns JSON with maintenance categories, frequency, and estimated annual cost.
---

# IA_MaintenanceScheduler

Creates preventive maintenance schedules for delivered properties.

## Trigger

Evento `ProjectDelivered` + cron anual de revisão de cronograma

## Prompt Template

```
Sugira um cronograma de manutenção preventiva para o imóvel {propertyAddress}
com base em: tipo de construção ({constructionType}), idade do imóvel ({age} anos),
sistemas instalados ({installedSystems}), histórico de manutenções anteriores ({maintenanceHistory}).
Retorne JSON com manutenções por categoria, frequência recomendada e estimativa de custo anual.
```

## Prompt Version

`ia-facilities/maintenance-scheduler@1.0.0`
