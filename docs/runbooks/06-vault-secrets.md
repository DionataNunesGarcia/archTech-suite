# Runbook: Gerenciamento de Secrets no Vault

## Arquitetura

- **Engine:** KV v2 + Database + RabbitMQ
- **Auth:** Kubernetes auth (service accounts)
- **Audit:** File audit log em `/vault/logs/audit.log`

## Políticas

| Política | Acesso | Arquivo |
|----------|--------|---------|
| `drupal-service` | Leitura de secrets da aplicação | `infrastructure/vault/policies/drupal-service.hcl` |
| `ci-policy` | Escrita dev/staging, leitura prod | `infrastructure/vault/policies/ci-policy.hcl` |
| `audit-policy` | Leitura de audit logs | `infrastructure/vault/policies/audit-policy.hcl` |
| `rabbitmq-rotation` | Rotação de credenciais RabbitMQ | `infrastructure/vault/policies/rabbitmq-rotation.hcl` |

## Operações

### Inicializar cluster

```bash
vault operator init -key-shares=5 -key-threshold=3
vault operator unseal
```

### Aplicar configuração

```bash
vault operator init
vault policy write drupal-service infrastructure/vault/policies/drupal-service.hcl
vault policy write ci-policy infrastructure/vault/policies/ci-policy.hcl
```

### Rotação de senha do banco

```bash
# Manual
./infrastructure/vault/scripts/rotate-db-password.sh

# Automática (via Vault rotation)
vault write -f database/rotate-root/postgresql
```

### Rotação de senha do Redis

```bash
./infrastructure/vault/scripts/rotate-redis-password.sh
```

### Verificar secrets

```bash
vault kv get secret/data/archtech/db
vault kv get secret/data/archtech/drupal
```

## Troubleshooting

| Problema | Causa | Solução |
|----------|-------|---------|
| Permission denied | Política incorreta | Verificar `vault policy read <policy>` |
| Token expirado | TTL excedido | `vault login -method=kubernetes` |
| DB connection failed | Credenciais rotacionadas | `vault rotate database/rotate-root/postgresql` |

## Referências

- [Vault Documentation](https://developer.hashicorp.com/vault/docs)
- [Config](file:///home/dionata/projects/local/archTech-suite/infrastructure/vault/vault-config.hcl)
- [Policies](file:///home/dionata/projects/local/archTech-suite/infrastructure/vault/policies/)
