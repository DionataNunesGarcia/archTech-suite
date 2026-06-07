# ADR-003: RabbitMQ como Message Broker Principal

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2025-06 |

**Decisão:** RabbitMQ para mensageria principal + Redis para cache e pub/sub efêmero.

**Contexto:** O sistema precisa de comunicação assíncrona entre 10 bounded contexts, com garantia de entrega, DLQ para mensagens com falha, e suporte a múltiplos padrões de exchange (topic, direct, fanout).

**Alternativas descartadas:**
- **Redis Pub/Sub** — sem persistência, mensagens perdidas se consumer offline
- **Kafka** — complexidade operacional desnecessária para o volume atual (< 1M eventos/dia)

**Critério para migrar para Kafka:** Volume acima de 1 milhão de eventos/dia ou necessidade de replay de eventos históricos (event sourcing).
