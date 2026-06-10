# Runbook: Resposta a Incidentes

## Severidade

| Severidade   | Exemplo                         | SLA              | Notificação              |
| ------------ | ------------------------------- | ---------------- | ------------------------ |
| **Critical** | Site down, dados perdidos       | < 15min resposta | PagerDuty + Slack        |
| **High**     | Funcionalidade crítica quebrada | < 1h resposta    | Slack @archtech-platform |
| **Medium**   | Bug não-bloqueante              | < 4h             | Slack #archtech-dev      |
| **Low**      | Melhoria/feature request        | < 1 sprint       | GitHub Issue             |

## Incidente Crítico

1. **Detectar** — PagerDuty alerta ou monitor detecta falha
2. **Triage** — Verificar painel Grafana "ArchTech Suite Overview"
3. **Comunicar** — Postar no Slack #incidentes com `@archtech-platform`
4. **Mitigar** — Aplicar runbook específico:
   - Banco lento → [03-database-backup.md](03-database-backup.md)
   - Filas acumuladas → [02-rabbitmq-management.md](02-rabbitmq-management.md)
   - Erro HTTP 5xx → Verificar logs no Loki + rollout anterior
5. **Resolver** — Aplicar fix, validar em staging, promover para produção
6. **Post-mortem** — Documentar causa raiz, timeline, ações preventivas

## Rollback

```bash
# Helm rollback
helm rollback archnet -n archtech-prod 1

# Verificar status
helm status archnet -n archtech-prod

# Histórico de revisões
helm history archnet -n archtech-prod
```

## Escalação

| Nível | Quem                      | Meio      |
| ----- | ------------------------- | --------- |
| L1    | Platform engineer on-call | PagerDuty |
| L2    | Tech Lead                 | Telefone  |
| L3    | Engineering Manager       | Telefone  |
