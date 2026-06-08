#!/bin/bash
# Setup vault: auth methods, secrets engines, policies, roles
# Usage: ./setup-vault.sh <environment>
# Requires: vault CLI, VAULT_TOKEN

set -euo pipefail

ENV="${1:-dev}"
VAULT_ADDR="${VAULT_ADDR:-http://127.0.0.1:8200}"
export VAULT_ADDR

if [ -z "${VAULT_TOKEN:-}" ]; then
  echo "❌ VAULT_TOKEN not set"
  exit 1
fi

echo "=== Vault Setup for ArchTech Suite ($ENV) ==="

# 1. Enable KV v2 secrets engine
vault secrets enable -path=secret kv-v2 2>/dev/null || echo "KV v2 already enabled"

# 2. Write policies
echo "--- Policies ---"
for policy in drupal-service ci-policy audit-policy rabbitmq-rotation; do
  vault policy write "$policy" "infrastructure/vault/policies/${policy}.hcl"
  echo "  ✅ Policy $policy written"
done

# 3. Enable Kubernetes auth
echo "--- Kubernetes Auth ---"
vault auth enable kubernetes 2>/dev/null || echo "K8s auth already enabled"

vault write auth/kubernetes/config \
  kubernetes_host="https://kubernetes.default.svc"

# 4. Map service accounts
echo "--- Role mappings ---"
vault write auth/kubernetes/role/drupal \
  bound_service_account_names="default,drupal-sa" \
  bound_service_account_namespaces="archtech-dev,archtech-staging,archtech-prod" \
  policies="drupal-service" \
  ttl="1h"

vault write auth/kubernetes/role/github-actions \
  bound_service_account_names="github-actions" \
  bound_service_account_namespaces="archtech-dev,archtech-staging" \
  policies="ci-policy" \
  ttl="30m"

# 5. Enable Database secrets engine (RDS PostgreSQL)
echo "--- Database Secrets Engine ---"
vault secrets enable database 2>/dev/null || echo "Database engine already enabled"

vault write database/config/postgresql \
  plugin_name="postgresql-database-plugin" \
  allowed_roles="archtech-*" \
  connection_url="postgresql://{{username}}:{{password}}@archtech-${ENV}.rds.amazonaws.com:5432/archtech"

vault write database/roles/archtech-db \
  db_name="postgresql" \
  creation_statements="CREATE USER \"{{name}}\" WITH PASSWORD '{{password}}' VALID UNTIL '{{expiration}}';" \
  creation_statements="GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO \"{{name}}\";" \
  default_ttl="24h" \
  max_ttl="72h"

# 6. Enable RabbitMQ secrets engine
echo "--- RabbitMQ Secrets Engine ---"
vault secrets enable rabbitmq 2>/dev/null || echo "RabbitMQ engine already enabled"

vault write rabbitmq/config/connection \
  connection_uri="http://rabbitmq:15672" \
  username="archtech" \
  password="archtech"

vault write rabbitmq/roles/archtech \
  vhosts='{"/":{"configure":".*","write":".*","read":".*"}}'

echo "=== Vault setup complete ==="
