# HashiCorp Vault configuration for ArchTech Suite
# Aplicar via: vault operator init && vault operator unseal

# Enable KV v2 secrets engine
path "secret/*" {
  capabilities = ["create", "read", "update", "delete", "list"]
}

# Audit log para todos os acessos a secrets
audit "file" {
  type = "file"
  path = "/vault/logs/audit.log"
}

# --- Kubernetes Auth ---
auth "kubernetes" {
  config {
    kubernetes_host = "https://kubernetes.default.svc"
  }
}

# Mapear service accounts para políticas
auth/kubernetes/role/drupal {
  bound_service_account_names = ["default", "drupal-sa"]
  bound_service_account_namespaces = ["archtech-dev", "archtech-staging", "archtech-prod"]
  policies = ["drupal-service"]
  ttl = "1h"
}

auth/kubernetes/role/github-actions {
  bound_service_account_names = ["github-actions"]
  bound_service_account_namespaces = ["archtech-dev", "archtech-staging"]
  policies = ["ci-policy"]
  ttl = "30m"
}

# --- Database Secrets Engine (RDS PostgreSQL) ---
database/configure/postgresql {
  plugin_name = "postgresql-database-plugin"
  allowed_roles = "archtech-*"
  connection_url = "postgresql://{{username}}:{{password}}@archtech-{{environment}}.rds.amazonaws.com:5432/archtech"
}

database/role/archtech-db {
  db_name = "postgresql"
  creation_statements = [
    "CREATE USER \"{{name}}\" WITH PASSWORD '{{password}}' VALID UNTIL '{{expiration}}';",
    "GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO \"{{name}}\";"
  ]
  default_ttl = "24h"
  max_ttl = "72h"
}

# --- RabbitMQ Secrets Engine ---
rabbitmq/configure/connection {
  connection_uri = "http://rabbitmq:15672"
  username = "archtech"
  password = "archtech"
}

rabbitmq/role/archtech {
  vhosts = "{\"/\": {\"configure\": \".*\", \"write\": \".*\", \"read\": \".*\"}}"
}
