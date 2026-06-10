# Argo CD Operations Runbook

## Overview

Argo CD manages all ArchTech Suite deployments via GitOps. The source of truth is the Helm chart at `infrastructure/kubernetes/helm/`.

## Environments

| Environment | Argo CD App        | Branch    | Sync Policy                  |
| ----------- | ------------------ | --------- | ---------------------------- |
| Dev         | `archtech-dev`     | `develop` | Auto-sync on push            |
| Staging     | `archtech-staging` | `main`    | Auto-sync on merge           |
| Production  | `archtech-prod`    | `main`    | Auto-sync (with PR approval) |

## Common Operations

### View Application Status

```bash
argocd app get arctech-dev
argocd app get arctech-staging
argocd app get arctech-prod
```

### Manual Sync

```bash
argocd app sync arctech-dev
argocd app sync arctech-prod --prune
```

### Rollback

```bash
# List deployment history
argocd app history arctech-prod

# Rollback to specific version
argocd app rollback arctech-prod 3
```

### Sync Troubleshooting

1. Check sync status: `argocd app get <app>`
2. View conditions: `argocd app get <app> -o json | jq .status.conditions`
3. Force refresh: `argocd app refresh <app>`
4. Manual sync with prune: `argocd app sync <app> --prune`

## Alerting

- **SyncFailure**: PagerDuty P2 alert → Check Helm values syntax
- **OutOfSync**: Auto-healing enabled; if persistent, check for manual changes to cluster state
- **Unknown**: Argo CD server issue → check argocd-server pod logs
