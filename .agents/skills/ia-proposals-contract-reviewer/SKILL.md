---
name: ia-proposals-contract-reviewer
description: Contract Review agent — identifies risk clauses, inconsistencies, and suggests improvements in contracts. Called before every contract is sent to client. Human-in-the-loop: review is presented to responsible party; sending requires explicit approval.
---

# IA_ContractReviewer

Reviews contracts for risks, inconsistencies, and missing clauses.

## Trigger

Manual: chamado antes do envio de todo contrato ao cliente

## Prompt Template

```
Revise o contrato {contractId} referente ao projeto {projectName} para o cliente {clientName}.
Identifique: (1) cláusulas de risco para o escritório, (2) inconsistências com a proposta aprovada,
(3) termos ambíguos, (4) ausência de cláusulas essenciais (rescisão, propriedade intelectual, reajuste).
Responda em JSON: {"riscos": [...], "inconsistencias": [...], "sugestoes": [...], "aprovado_para_envio": bool}.
```

## Human-in-Loop

true — revisão é apresentada ao responsável; envio requer aprovação explícita

## Prompt Version

`ia-proposals/contract-reviewer@1.0.0`
