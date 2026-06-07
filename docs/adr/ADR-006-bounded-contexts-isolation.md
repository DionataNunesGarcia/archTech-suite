# ADR-006: Isolamento de Bounded Contexts via Drupal Recipes

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2025-06 |

**Decisão:** Cada bounded context é empacotado como uma Drupal Recipe independente, isolada por módulo customizado.

**Contexto:** O PRD define 10 bounded contexts que precisam ser desenvolvidos por squads independentes. Sem isolamento, um bug em um contexto pode impactar os demais, e squads ficam bloqueados por dependências cruzadas.

**Regra:** Módulos de squad nunca importam outros módulos de squad diretamente. Toda comunicação cross-squad via domain events (RabbitMQ) ou APIs públicas documentadas.

**Alternativas descartadas:**
- **Módulo monolítico único** — quebra o isolamento entre squads, impossibilita deploy independente
- **Microserviços completos** — overhead operacional desnecessário para o estágio atual; Drupal Recipes oferecem isolamento suficiente com custo de infraestrutura muito menor
