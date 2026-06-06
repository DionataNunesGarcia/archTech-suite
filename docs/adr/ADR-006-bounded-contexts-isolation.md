# ADR-006: Isolamento de Bounded Contexts via Drupal Recipes

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2025-06 |

**Decisão:** Cada bounded context é empacotado como uma Drupal Recipe independente, isolada por módulo customizado.

**Regra:** Módulos de squad nunca importam outros módulos de squad diretamente. Toda comunicação cross-squad via domain events (RabbitMQ) ou APIs públicas.
