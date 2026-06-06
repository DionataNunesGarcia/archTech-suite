---
name: ia-crm-followup-scheduler
description: Follow-up Scheduling agent — suggests best time and channel for next contact based on interaction history. Triggered by Cron when Opportunity is inactive for > 5 business days. Returns JSON with suggested datetime, channel, and message.
---

# IA_FollowUpScheduler

Suggests optimal follow-up timing and channel based on lead history.

## Trigger

Cron: `Opportunity` sem atividade há mais de 5 dias úteis

## Prompt Template

```
Sugira a melhor data e hora para follow-up com {leadName} sobre o projeto {projectName}.
Considere o histórico de interações: {interactionHistory}, o score atual ({score}) e o stage ({stage}).
Responda em JSON: {"data_sugerida": "ISO8601", "canal": "email|whatsapp|telefone|reuniao", "mensagem_sugerida": "..."}.
```

## Prompt Version

`ia-crm/followup-scheduler@1.0.0`
