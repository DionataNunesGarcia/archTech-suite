# Runbook: Observabilidade

## Stack

| Ferramenta | Versão | Porta |
|------------|--------|-------|
| Prometheus | 2.53.0 | 9090 |
| Grafana | 11.1.0 | 3000 |
| Loki | 3.0.0 | 3100 |
| Jaeger | 1.60 | 16686 (UI), 4317 (OTLP gRPC), 4318 (OTLP HTTP) |

A stack roda como DDEV add-on em `infrastructure/observability/docker-compose.observability.yaml`.
Ativada automaticamente via symlink em `.ddev/docker-compose.observability.yaml`.

## Acessos

| Ferramenta | URL | Credenciais |
|------------|-----|-------------|
| Grafana | https://archtech.ddev.site:8443 (via proxy reverso) ou localhost:3000 | admin / admin |
| Prometheus | http://prometheus:9090 (in-cluster) | - |
| Jaeger | http://jaeger:16686 (in-cluster) | - |
| Loki | http://loki:3100 (API) | - |

## Dashboards

### ArchTech Suite Overview

Dashboard principal com:

- **HTTP Error Rate** — % de erros 5xx
- **P99 Latency** — Latência no percentil 99
- **RabbitMQ Queue Depth** — Mensagens pendentes por fila
- **Pod CPU/Memory** — Uso de recursos por pod
- **DLQ Messages** — Mensagens na dead letter queue
- **Database Connections** — Conexões ativas no PostgreSQL
- **Service Uptime** — Disponibilidade geral dos serviços
- **Recent Error Logs** — Logs de erro via Loki

## Alertas

| Alerta | Gatilho | Severidade | Ação |
|--------|---------|------------|------|
| HighErrorRate | 5xx > 5% em 5min | Critical | Verificar logs + rollback |
| ServiceDown | up == 0 por 2min | Critical | Verificar pods |
| RabbitMQQueueDepth | > 10.000 mensagens | Warning | Verificar consumers |
| RabbitMQDLQNonEmpty | DLQ > 10 mensagens | Critical | Investigar falha consumer |
| P99LatencyHigh | > 500ms por 5min | Warning | Verificar performance |
| DiskSpaceLow | < 15% livre | Warning | Expandir volume |

## Tracing

Todas as requisições propagam `trace_id` do frontend ao backend.

### Headers HTTP

- `X-Trace-Id` — trace ID propagado
- `X-Span-Id` — span ID atual
- `X-Parent-Span-Id` — span pai

### Formato de Log Estruturado

```json
{
  "trace_id": "abc123",
  "squad": "ia_atendimento",
  "agent": "qualificadora",
  "action": "qualify_lead",
  "duration_ms": 183,
  "level": "info",
  "timestamp": "2025-06-01T10:00:00Z"
}
```

## Referências

- [Prometheus Documentation](https://prometheus.io/docs/)
- [Grafana Documentation](https://grafana.com/docs/)
- [Jaeger Documentation](https://www.jaegertracing.io/docs/)
- [Loki Documentation](https://grafana.com/docs/loki/)
