# ArchTech Suite — Documentação

Índice central da documentação técnica e de produto.

## Produto

| Documento | Descrição |
|-----------|-----------|
| `archtech-prd.md` (raiz) | PRD completo — visão, squads, stack, fases |
| `archtech-prd-enhanced.xml` (raiz) | Features machine-readable (9 bounded contexts) |
| `archtech_architecture_overview.svg` (raiz) | Diagrama de arquitetura em camadas |

## Arquitetura

| Diretório | Descrição |
|-----------|-----------|
| `architecture/` | Visão arquitetural, modelos C4, decisões |
| `adr/` | Architectural Decision Records (7 ADRs) |
| `api-specifications/` | Contratos OpenAPI 3.1 (Contract-First) |
| `data-contracts/` | JSON Schema dos domain events |

## Desenvolvimento

| Diretório | Descrição |
|-----------|-----------|
| `guides/` | Guias de coding standards, recipes, DDEV setup |
| `runbooks/` | Runbooks operacionais (DR, deploy, troubleshooting) |
| `security/` | Diretrizes de segurança, threat model |

## IA e Prompts

| Diretório | Descrição |
|-----------|-----------|
| `ai-prompts/` | Prompt Registry — prompts versionados (semver) |
| `.agents/skills/` (raiz) | SKILL.md dos 19 agentes de IA do PRD |
| `.ai/skills/` (raiz) | Skills de workflow de desenvolvimento |

## Skills Externas

| Arquivo | Descrição |
|---------|-----------|
| `skills-lock.json` (raiz) | 16 skills externas (qualidade, debug, TDD) |
| `opencode.json` (raiz) | Registro de skills para a ferramenta opencode |
