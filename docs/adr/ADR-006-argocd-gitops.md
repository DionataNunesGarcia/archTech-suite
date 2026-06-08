# ADR-006 · GitOps with Argo CD

| Campo | Valor |
|-------|-------|
| Status | **accepted** |
| Data | 2026-06-08 |

**Decisão:** Usar Argo CD para GitOps deployment em todos os ambientes (dev, staging, production).

**Contexto:** A Fase 7 exige deploy automatizado e reproduzível. Deploys manuais via kubectl/Helm CLI são propensos a erro e não auditáveis.

**Justificativa:** 
- Sync automático mantém cluster sempre igual ao repositório Git
- Self-healing reverte mudanças manuais no cluster
- Histórico completo de deploys com rollback via UI ou CLI
- Suporte nativo a Helm charts com value files por ambiente
- Sync Windows para evitar deploys fora do horário comercial

**Alternativas descartadas:**
- **Flux CD** — Ecossistema maior mas mais complexo; Argo CD tem UI mais madura
- **Jenkins X** — Overhead operacional desnecessário para o estágio atual
- **Helm CLI manual** — Sem audit trail, propenso a erro humano

**Configuração:** Manifests em `infrastructure/argocd/` — AppProject + Application por ambiente.
