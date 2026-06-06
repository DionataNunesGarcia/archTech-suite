---
name: ia-crm-lead-scorer
description: Commercial Lead Scoring agent — scores leads 1-10 based on fit, budget, urgency, and profile. Triggered by LeadCreated or BriefingCreated events on RabbitMQ. Returns JSON with score, justification, and suggested actions.
---

# IA_LeadScorer

Scores leads from 1 to 10 based on commercial criteria.

## Trigger

Evento `LeadCreated` ou `BriefingCreated` no RabbitMQ (exchange: `archtech.crm`)

## Prompt Template

```
Avalie o lead {leadId} com base nos critérios: tipo de projeto ({tipo_projeto}),
orçamento ({orcamento_range}), prazo ({prazo_desejado}), fonte ({fonte}) e perfil do contato.
Atribua uma pontuação de 1 a 10 e justifique os 3 principais fatores.
Responda apenas em JSON: {"score": N, "justificativa": "...", "acoes_sugeridas": ["...", "..."]}.
```

## Output Format

JSON string-only output for structured response.

## Prompt Version

`ia-crm/lead-scorer@1.0.0`
