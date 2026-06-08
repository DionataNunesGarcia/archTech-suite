# CI/CD policy — escrita de secrets apenas em ambientes não-prod
path "secret/data/archtech/dev/*" {
  capabilities = ["create", "read", "update", "delete", "list"]
}

path "secret/data/archtech/staging/*" {
  capabilities = ["create", "read", "update", "delete", "list"]
}

path "secret/data/archtech/prod/*" {
  capabilities = ["read"]
}

path "secret/metadata/archtech/*" {
  capabilities = ["list"]
}
