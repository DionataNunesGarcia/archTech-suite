# PagerDuty On-Call Runbook

## Overview

PagerDuty manages incident alerting and on-call rotations for ArchTech Suite.

## On-Call Schedule

| Squad        | Primary Channel               | Rotation            |
| ------------ | ----------------------------- | ------------------- |
| Platform/SRE | `#archtech-oncall-platform`   | 4 engineers, weekly |
| AI Team      | `#archtech-oncall-ai`         | 3 engineers, weekly |
| Atendimento  | `#archtech-squad-atendimento` | 3 engineers, weekly |
| Projetos     | `#archtech-squad-projetos`    | 3 engineers, weekly |
| Obras        | `#archtech-squad-obras`       | 3 engineers, weekly |
| Suporte      | `#archtech-squad-suporte`     | 3 engineers, weekly |

## Escalation Policies

### Critical (P1) — 5min response

1. On-call Platform/SRE engineer
2. Squad Lead + Tech Lead (15min)
3. CTO / Engineering Director (30min)

### High (P2) — 10min response

1. On-call squad engineer
2. Squad Lead (20min)
3. Tech Lead (45min)

### Warning (P3) — 30min response

1. On-call squad engineer (next business day if outside hours)
2. Squad Lead (next business day)

## Incident Response

### When Alerted

1. Acknowledge incident in PagerDuty
2. Check runbook linked in alert for the affected component
3. Join incident Slack channel (auto-created)
4. Diagnose using Grafana dashboards

### Critical Incident (P1)

1. **Triage (5min)**: Assess impact, notify squad lead
2. **Mitigate (15min)**: Rollback, scale up, or activate fallback
3. **Resolve (30min)**: Confirm system is stable
4. **Post-mortem (24h)**: Schedule incident review

## Shift Handover

Before ending shift:

1. Resolve or reassign open incidents
2. Document ongoing issues in `#archtech-oncall-handover`
3. Review pending alerts and acknowledge any that triggered during shift
