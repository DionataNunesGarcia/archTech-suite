#!/bin/bash
# Rotate Redis password via HashiCorp Vault
# Usage: ./rotate-redis-password.sh <environment>

set -euo pipefail

ENV="${1:-dev}"
VAULT_ADDR="${VAULT_ADDR:-http://127.0.0.1:8200}"
VAULT_TOKEN="${VAULT_TOKEN:-}"

if [ -z "$VAULT_TOKEN" ]; then
  echo "❌ VAULT_TOKEN not set"
  exit 1
fi

export VAULT_ADDR VAULT_TOKEN

echo "🔄 Rotating Redis password for environment: $ENV"

NEW_PASS=$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-20)

vault kv put "secret/archtech/${ENV}/redis" \
  password="${NEW_PASS}" \
  rotated_at="$(date -u +%Y-%m-%dT%H:%M:%SZ)"

echo "✅ Redis password rotated and stored in Vault at secret/archtech/${ENV}/redis"
