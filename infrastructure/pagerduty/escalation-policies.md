# PagerDuty Integration Configuration
# ArchTech Suite — Escalation Policies & On-Call Rotations

## Service Integration

### Critical Services (P1/P2)
| Service | Squad | Escalation Policy | Runbook |
|---------|-------|-------------------|---------|
| ArchTech Backend API | Platform | critical-escalation | `/docs/runbooks/05-incident-response.md` |
| ArchTech Frontend | Platform | critical-escalation | `/docs/runbooks/05-incident-response.md` |
| ArchTech Database | SRE | critical-escalation | `/docs/runbooks/03-database-backup.md` |
| ArchTech RabbitMQ | Platform | critical-escalation | `/docs/runbooks/02-rabbitmq-management.md` |
| ArchTech AI Gateway | AI Team | ai-escalation | `/docs/runbooks/ai-provider-failure.md` |

### High Services (P3)
| Service | Squad | Escalation Policy |
|---------|-------|-------------------|
| ArchTech CRM | Atendimento | squad-escalation |
| ArchTech Proposals | Projetos | squad-escalation |
| ArchTech Financeiro | Financeiro | squad-escalation |
| ArchTech Library | Suporte | squad-escalation |
| ArchTech Permits | Obras | squad-escalation |

## Escalation Policies

### Critical Escalation
1. **L1 (5min)** — On-call Platform/SRE
2. **L2 (15min)** — Squad Lead + Tech Lead
3. **L3 (30min)** — CTO / Engineering Director

### AI Escalation
1. **L1 (5min)** — On-call AI Team
2. **L2 (15min)** — AI Team Lead
3. **L3 (30min)** — Tech Lead + CTO

### Squad Escalation
1. **L1 (10min)** — On-call Squad Member
2. **L2 (20min)** — Squad Lead
3. **L3 (45min)** — Tech Lead

## On-Call Schedule

### Rotation Pattern
- **Primary**: Weekly rotation (Mon 9am → Mon 9am)
- **Secondary**: Follow-the-sun (BR timezone: 8am-6pm BRT)
- **Override**: Squad Lead covers during PTO

### Squads
| Squad | On-Call Channel | Rotation Size |
|-------|----------------|---------------|
| Platform/SRE | `#archtech-oncall-platform` | 4 engineers |
| AI Team | `#archtech-oncall-ai` | 3 engineers |
| Atendimento | `#archtech-squad-atendimento` | 3 engineers |
| Projetos | `#archtech-squad-projetos` | 3 engineers |
| Obras | `#archtech-squad-obras` | 3 engineers |
| Suporte | `#archtech-squad-suporte` | 3 engineers |
