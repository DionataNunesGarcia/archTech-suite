# Policy for RabbitMQ credential rotation
path "database/creds/archtech-rabbitmq" {
  capabilities = ["read"]
}

path "database/roles/archtech-rabbitmq" {
  capabilities = ["read"]
}

path "rabbitmq/creds/archtech" {
  capabilities = ["read"]
}

path "sys/leases/renew" {
  capabilities = ["update"]
}
