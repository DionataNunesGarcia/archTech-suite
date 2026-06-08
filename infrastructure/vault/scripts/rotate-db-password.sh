#!/bin/bash
# Rotate RDS PostgreSQL password via HashiCorp Vault
# Usage: ./rotate-db-password.sh <environment>

set -euo pipefail

ENV="${1:-dev}"
VAULT_ADDR="${VAULT_ADDR:-http://127.0.0.1:8200}"
VAULT_TOKEN="${VAULT_TOKEN:-}"

if [ -z "$VAULT_TOKEN" ]; then
  echo "❌ VAULT_TOKEN not set"
  exit 1
fi

export VAULT_ADDR VAULT_TOKEN

echo "🔄 Rotating database password for environment: $ENV"

# Generate new password
NEW_PASS=$(openssl rand -base64 32 | tr -d '/+=' | cut -c1-30)

# Store in Vault
vault kv put "secret/archtech/${ENV}/db" \
  database_url="pgsql://archtech:${NEW_PASS}@archtech-${ENV}.rds.amazonaws.com:5432/archtech" \
  password="${NEW_PASS}" \
  rotated_at="$(date -u +%Y-%m-%dT%H:%M:%SZ)"

echo "✅ Password rotated and stored in Vault at secret/archtech/${ENV}/db"

# Trigger database password update (via AWS CLI or RDS API)
if command -v aws &> /dev/null; then
  aws rds modify-db-instance \
    --db-instance-identifier "archtech-${ENV}" \
    --master-user-password "${NEW_PASS}" \
    --apply-immediately

  echo "✅ RDS password updated via AWS CLI"
else
  echo "⚠️  AWS CLI not found. Update RDS password manually."
fi
