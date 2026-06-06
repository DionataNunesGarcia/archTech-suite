---
name: ia-financeiro-collection-agent
description: Automated Collection agent — generates personalized collection communications and escalates by overdue level. Triggered by daily Cron checking overdue AccountReceivable. Human-in-the-loop required for level 3 (legal notice).
---

# IA_CollectionAgent

Automates collection communications with escalating tone.

## Trigger

Cron diário: `AccountReceivable` com `status=vencido`

## Prompt Template

```
Gere uma notificação de cobrança para {clientName} referente à fatura {invoiceId}
vencida há {daysOverdue} dias no valor de R$ {value}.
Nível de cobrança: {level} (1=lembrete amigável, 2=cobrança formal, 3=aviso jurídico).
Mantenha o relacionamento e ofereça opções de regularização.
```

## Escalation Levels

| Level | Description | Human-in-Loop |
|-------|-------------|---------------|
| 1 | Friendly reminder | false |
| 2 | Formal collection | false |
| 3 | Legal notice | true |

## Prompt Version

`ia-financeiro/collection-agent@1.0.0`
