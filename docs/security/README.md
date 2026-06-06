# Segurança

Diretrizes de segurança do ArchTech Suite.

## Princípios

- **Security by Design:** Análise de segurança na fase de design, não no deploy
- **Shift Left:** SAST/DAST rodam no pipeline CI
- **Zero Trust:** Secrets nunca aparecem em código ou logs

## Ferramentas

| Ferramenta | Uso | Fase |
|------------|-----|------|
| Snyk | SAST (vulnerabilidades em dependências) | CI |
| OWASP ZAP | DAST (pentest automatizado) | CI / Staging |
| HashiCorp Vault | Gestão de secrets | Infra |
| STRIDE | Threat modeling | Design |

## Documentos Planejados

- Threat Model (STRIDE) — superfícies de ataque por squad
- LGPD compliance — dados pessoais, consentimento, DPO
- OWASP Top 10 — mitigação por camada
- Access control matrix — RBAC por squad e role
- API security — rate limiting, authentication, audit
