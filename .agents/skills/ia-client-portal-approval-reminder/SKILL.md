---
name: ia-client-portal-approval-reminder
description: Approval Nudging agent — sends smart reminders with context to reduce approval time. Triggered by Cron when ClientApprovalRequest is pending > 48h. Auto-sends up to 3 reminders; escalates to PM after 3rd.
---

# IA_ApprovalReminder

Sends intelligent reminders to clients with pending approval requests.

## Trigger

Cron: `ClientApprovalRequest` com `status=pendente` há mais de 48h

## Prompt Template

```
Envie um lembrete ao cliente {clientName} para aprovar a etapa {stageName} do projeto {projectName}.
O prazo vence em {daysUntilDeadline} dias. Inclua um resumo do que está sendo aprovado e o link direto.
```

## Human-in-Loop

- false para níveis 1-3 (envio automático)
- Escala para PM após 3º lembrete sem resposta

## Prompt Version

`ia-client-portal/approval-reminder@1.0.0`
