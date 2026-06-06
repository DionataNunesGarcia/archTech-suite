# ADR-003: RabbitMQ como Message Broker Principal

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2025-06 |

**Decisão:** RabbitMQ para mensageria principal + Redis para cache e pub/sub efêmero.

**Critério para migrar para Kafka:** Volume acima de 1 milhão de eventos/dia.
