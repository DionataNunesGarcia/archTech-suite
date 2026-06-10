# Canary Deployment Runbook

## Overview

Production deployments use Argo Rollouts for progressive delivery: 5% → 25% → 100% traffic shift with automatic Prometheus analysis.

## Canary Steps

| Step | Weight | Duration | Analysis                       |
| ---- | ------ | -------- | ------------------------------ |
| 1    | 5%     | 10 min   | Error rate < 0.1%, P99 < 500ms |
| 2    | 25%    | 10 min   | Error rate < 0.1%, P99 < 500ms |
| 3    | 50%    | 10 min   | Error rate < 0.1%, P99 < 500ms |
| 4    | 100%   | —        | Full rollout                   |

## How to Initiate

1. Merge PR to `main` branch
2. Argo CD syncs the new image tag automatically
3. Rollout controller executes canary steps
4. Monitor progress:

```bash
kubectl argo rollouts get rollout arctech-backend -n arctech-prod
kubectl argo rollouts get rollout arctech-frontend -n arctech-prod
```

## Manual Promotion

```bash
kubectl argo rollouts promote arctech-backend -n arctech-prod
```

## Manual Rollback

```bash
kubectl argo rollouts undo arctech-backend -n arctech-prod
```

## Failure Scenarios

### Canary stalls at 5%

- Check analysis runs: `kubectl get analysisrun -n arctech-prod`
- Check Prometheus queries are returning data
- If flaky: manual promote after verifying service health

### Error rate threshold exceeded

- Rollout auto-aborts and scales down canary
- Revert to previous stable version:

```bash
kubectl argo rollouts abort arctech-backend -n arctech-prod
kubectl argo rollouts undo arctech-backend -n arctech-prod
```

## Monitoring

- Grafana: SLO Dashboard → "Canary" section
- Argo CD: Application detail page shows rollout progress
- Slack: `#archtech-alerts` for canary promotions and failures
