# Drupal service policy — leitura de secrets específicos
path "secret/data/archtech/drupal" {
  capabilities = ["read"]
}

path "secret/data/archtech/db" {
  capabilities = ["read"]
}

path "secret/data/archtech/redis" {
  capabilities = ["read"]
}

path "secret/data/archtech/rabbitmq" {
  capabilities = ["read"]
}

path "secret/data/archtech/ai/*" {
  capabilities = ["read", "list"]
}

path "secret/data/archtech/oauth/*" {
  capabilities = ["read", "list"]
}
