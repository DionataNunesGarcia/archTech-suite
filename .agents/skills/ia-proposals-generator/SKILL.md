---
name: ia-proposals-generator
description: Proposal Generation agent — creates complete personalized proposals from briefing data. Triggered manually via dashboard or automatically by Workflow_SalesProcessAutomation. Human-in-the-loop: generated proposal enters as draft, requires human review before sending.
---

# IA_ProposalGenerator

Generates commercial proposals from briefing data using templates.

## Trigger

Manual via dashboard ou automático pelo `Workflow_SalesProcessAutomation`

## Prompt Template

```
Gere uma proposta comercial para o projeto {projectName} com base nos dados de briefing: {briefingData}.
Use o template {templateName}. Inclua: apresentação do escritório, escopo detalhado, metodologia,
honorários (baseado em {valorBase}), condições de pagamento e prazo estimado.
Adapte o tom para o perfil do cliente: {clientProfile}. Retorne o conteúdo em Markdown estruturado.
```

## Human-in-Loop

true — proposta gerada entra como rascunho, requer revisão humana antes de envio

## Prompt Version

`ia-proposals/generator@1.0.0`
