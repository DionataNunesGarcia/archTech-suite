# RabbitMQ — ArchTech Suite

## Visão Geral

RabbitMQ é o message broker principal do ArchTech Suite.
Implementa comunicação assíncrona entre squads via domain events.

## Arquitetura

### Exchanges

| Exchange                | Type   | Propósito                               |
| ----------------------- | ------ | --------------------------------------- |
| `archtech.leads`        | topic  | Eventos de leads e reuniões             |
| `archtech.marketing`    | topic  | Eventos de conteúdo e campanhas         |
| `archtech.projects`     | topic  | Eventos de projetos e renders           |
| `archtech.construction` | topic  | Eventos de obra e orçamento             |
| `archtech.internal`     | topic  | Eventos internos (documentos, insights) |
| `archtech.ai.jobs`      | direct | Jobs assíncronos de IA                  |
| `archtech.dlq`          | fanout | Dead letter queue (alertas)             |
| `archtech.retry`        | direct | Retry queue (backoff exponencial)       |

### Filas

Cada evento tem sua própria fila nomeada como `archtech.<squad>.<event_name>`.
Todas as filas têm DLQ configurada e TTL de 7 dias.

### Políticas

- **DLQ Policy**: toda fila `archtech.*` redireciona mensagens não processadas para `archtech.dlq`
- **Retry Policy**: filas `archtech.retry.*` com TTL para retry com backoff

## Gerenciamento Local (DDEV)

```bash
# Acessar Management UI
open https://archtech.ddev.site:15672
# Usuário: archnet / Senha: archnet

# Importar definições (via API)
curl -u archnet:archtech \
  -X POST http://localhost:15672/api/definitions \
  -H "Content-Type: application/json" \
  -d @infrastructure/rabbitmq/definitions.json
```

## DDEV

O serviço RabbitMQ é iniciado automaticamente com `ddev start`.
Configurado em `.ddev/docker-compose.redis.yaml` (mesmo arquivo).

Conectando de dentro do container DDEV:

- **Host**: `rabbitmq`
- **Port**: `5672` (AMQP) / `15672` (Management)
- **User**: `archtech`
- **Password**: `archtech`

## Monitoramento

- Management UI: `http://archtech.ddev.site:15672`
- Queue depth monitorado via Prometheus (rabbitmq_exporter)
- Alerta PagerDuty se DLQ > 10 mensagens

## DR (Disaster Recovery)

Backup das definições:

```bash
curl -u archnet:archtech \
  -X GET http://localhost:15672/api/definitions \
  -o backups/rabbitmq-$(date +%Y%m%d).json
```

Restore:

```bash
curl -u archnet:archtech \
  -X POST http://localhost:15672/api/definitions \
  -H "Content-Type: application/json" \
  -d @backups/rabbitmq-20250101.json
```
