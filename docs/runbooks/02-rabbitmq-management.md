# Runbook: Gerenciamento do RabbitMQ

## Acessos

| Interface     | URL                                      | Credenciais     |
| ------------- | ---------------------------------------- | --------------- |
| Management UI | https://archtech.ddev.site:15672         | archet / archet |
| AMQP          | amqp://archtech:archtech@localhost:5672/ |

## Estrutura

### Exchanges

| Exchange                 | Type   | Routing Key Pattern          |
| ------------------------ | ------ | ---------------------------- |
| `archtech.leads`         | topic  | `lead.#`, `meeting.#`        |
| `archtech.marketing`     | topic  | `content.#`, `campaign.#`    |
| `archtech.projects`      | topic  | `project.#`, `render.#`      |
| `archtech.construction`  | topic  | `construction.#`, `budget.#` |
| `archtech.internal`      | topic  | `internal.#`                 |
| `archtech.diary`         | topic  | `diary.#`                    |
| `archtech.meetings`      | topic  | `meetings.#`                 |
| `archtech.financial_adv` | topic  | `financial_adv.#`            |
| `archtech.tasks`         | topic  | `task.#`                     |
| `archtech.ai.jobs`       | direct | `ai.jobs`                    |
| `archtech.retry`         | direct | `retry`                      |
| `archtech.dlq`           | fanout | —                            |

### Dead Letter Queue

- TTL: 7 dias
- Máx: 100.000 mensagens
- Alerta via Prometheus se > 10 mensagens

## Operações

### Verificar status

```bash
# Via DDEV
ddev rabbitmq-setup

# Via CLI do container
ddev ssh -s rabbitmq
rabbitmqctl list_queues
rabbitmqctl list_exchanges
rabbitmqctl list_bindings
```

### Consumidores por fila

```bash
rabbitmqctl list_consumers
```

### Resetar fila

```bash
rabbitmqctl purge_queue archet.<contexto>.<evento>
```

## Troubleshooting

| Problema                | Causa Comum            | Solução                                        |
| ----------------------- | ---------------------- | ---------------------------------------------- |
| Mensagens acumuladas    | Consumer caiu          | Reiniciar consumer, verificar logs             |
| DLQ com mensagens       | Falha no processamento | Verificar logs do consumer, reprocessar da DLQ |
| Exchange não encontrada | Config ausente         | Executar `ddev rabbitmq-setup`                 |
| Conexão recusada        | RabbitMQ não iniciou   | `ddev restart`                                 |

## Referências

- [RabbitMQ Documentation](https://www.rabbitmq.com/documentation.html)
- [Definições](file:///home/dionata/projects/local/archTech-suite/infrastructure/rabbitmq/definitions.json)
