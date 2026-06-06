# Prompt Registry

Repositório versionado de prompts para os 20 agentes de IA do ArchTech Suite.

## Estrutura

```
ai-prompts/
├── registry/                    ← Prompts versionados (formato YAML)
│   ├── ia-client-portal/
│   │   ├── notifier@1.0.0.yaml
│   │   └── approval-reminder@1.0.0.yaml
│   ├── ia-crm/
│   ├── ia-proposals/
│   ├── ia-financeiro/
│   ├── ia-library/
│   ├── ia-permits/
│   ├── ia-suppliers/
│   ├── ia-facilities/
│   └── ia-bim-twin/
└── README.md
```

## Versionamento

- Formato: `{agent-name}@major.minor.patch`
- Breaking change (mudança de comportamento esperado) = major bump
- Toda versão tem test cases automatizados
- Mudanças passam pelo mesmo pipeline de review do código

## Agentes

Ver referência completa em `.agents/skills/` (SKILL.md de cada agente).
