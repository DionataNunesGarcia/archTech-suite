# Runbook: DDEV Setup

## Pré-requisitos

- Docker Desktop 24+ ou OrbStack
- DDEV v1.25+

## Setup

```bash
# Clonar repositório
git clone https://github.com/archtech/suite.git
cd suite

# Iniciar DDEV
ddev start

# Instalar dependências
ddev composer install --no-interaction

# Verificar status
ddev drush core:status
ddev drush cr
```

## Serviços

| Serviço | Acesso |
|---------|--------|
| Drupal | https://archtech.ddev.site |
| Mailpit | https://archtech.ddev.site:8025 |
| RabbitMQ | https://archtech.ddev.site:15672 (user: archtech, pass: archtech) |
| Grafana | https://archtech.ddev.site:3001 (user: admin, pass: admin) |
| Jaeger | https://archtech.ddev.site:16686 |
| PostgreSQL | localhost:5432 (db: db, user: db, pass: db) |
| Redis | localhost:6379 |

## Comandos Úteis

```bash
ddev logs            # Logs em tempo real
ddev ssh             # SSH no container web
ddev drush <cmd>     # Drush commands
ddev composer <cmd>  # Composer commands
ddev snapshot        # Snapshot do banco
ddev restart         # Restart completo
```

## Troubleshooting

```bash
ddev logs --tail=50          # Últimas 50 linhas de log
ddev poweroff && ddev start  # Hard reset
ddev describe                # Status detalhado
```
