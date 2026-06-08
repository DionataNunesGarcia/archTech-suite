# Runbook: Database Backup

## DDEV

```bash
# Snapshot
ddev snapshot --name pre-deploy-$(date +%Y%m%d)

# Listar snapshots
ddev snapshot --list

# Restaurar
ddev restore-snapshot pre-deploy-20250101

# Export via drush
ddev drush sql-dump --result-file=/tmp/db-$(date +%Y%m%d).sql
```

## Produção (RDS)

```bash
# Backup automático (RDS retention: 30 dias)
aws rds create-db-snapshot \
  --db-instance-identifier archnet-prod \
  --db-snapshot-identifier archtech-prod-$(date +%Y-%m-%d-%H%M)

# Restore para nova instância
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier archtech-restore-test \
  --db-snapshot-identifier archtech-prod-20250101

# Export para S3
aws rds start-export-task \
  --export-task-identifier archtech-prod-export-$(date +%Y%m%d) \
  --source-arn arn:aws:rds:us-east-1:ACCOUNT:db-snapshot:archtech-prod-20250101 \
  --s3-bucket-name archtech-backups \
  --iam-role-arn arn:aws:iam::ACCOUNT:role/rds-s3-export
```

## Recovery Point Objective (RPO)

| Ambiente | RPO | Método |
|----------|-----|--------|
| Dev | 24h | DDEV snapshot |
| Staging | 24h | RDS automated backup |
| Production | 1h | RDS automated backup + WAL streaming |

## Recovery Time Objective (RTO)

| Ambiente | RTO | Método |
|----------|-----|--------|
| Dev | 15min | DDEV restore |
| Staging | 2h | RDS restore |
| Production | 4h | RDS point-in-time recovery |
