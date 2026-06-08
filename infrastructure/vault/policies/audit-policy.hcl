# Audit log read-only for compliance team
path "sys/audit" {
  capabilities = ["read", "list"]
}

path "sys/audit-hash" {
  capabilities = ["update"]
}

path "sys/raw/*" {
  capabilities = ["read", "list"]
}
