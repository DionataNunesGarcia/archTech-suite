# FinOps Cost Review Runbook

## Overview

Weekly cost optimization review for cloud infrastructure and AI provider usage.

## Schedule

- **Weekly**: Every Monday at 10:00 BRT
- **Monthly**: First Monday of month with full billing review
- **Quarterly**: Deep dive with budget forecast

## Cloud Infrastructure Costs

### Check Current Spend

```bash
# AWS
./scripts/finops-review.sh

# Manual: AWS Console → Cost Explorer
# Check: RDS, EKS, ElastiCache, NAT Gateway
```

### Optimization Checklist

- [ ] RDS: Check IOPS utilization (right-size if < 20%)
- [ ] EKS: Check node utilization (consolidate if < 40%)
- [ ] NAT Gateway: Can we use VPC endpoints instead?
- [ ] S3: Lifecycle policy moving old logs to Glacier?
- [ ] Redis: Memory utilization within limits?

## AI Provider Costs

### Track Usage

AI cost data is tracked in `infrastructure/finops/ai-cost-tracking.csv`.

### Optimization Strategies

1. **Model tiering**: Use GPT-4o-mini for simple tasks (lead scoring, classification), reserve GPT-4o for complex reasoning
2. **Context window optimization**: Reduce prompt size, use caching for repeated contexts
3. **Batch processing**: Aggregate low-priority AI tasks into batch calls
4. **Rate limiting**: Cap daily tokens per squad

### Budget Thresholds

| Squad       | Daily Limit | Weekly Limit | Monthly Forecast |
| ----------- | ----------- | ------------ | ---------------- |
| Atendimento | $50         | $300         | $1,200           |
| Projetos    | $100        | $600         | $2,400           |
| Obras       | $75         | $450         | $1,800           |
| Suporte     | $40         | $240         | $960             |
| Marketing   | $80         | $480         | $1,920           |
| Insights    | $30         | $180         | $720             |

## Alerts

- **Daily cost > 150% of daily limit**: Slack alert to squad lead
- **Weekly cost > weekly limit**: PagerDuty P3 to AI team
- **Monthly forecast > budget**: PagerDuty P2 to Tech Lead
