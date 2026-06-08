# Runbook: Onboarding de Novo Desenvolvedor

## Tempo esperado: < 10 minutos

## Passo 1: Pré-requisitos

- Docker Desktop 24+ ou OrbStack
- DDEV v1.25+
- Git
- Editor: VS Code ou PHPStorm

## Passo 2: Setup do Projeto

```bash
# Clone
git clone https://github.com/archtech/suite.git
cd suite

# Iniciar ambiente
ddev start

# Verificar serviços
ddev describe

# Instalar dependências
ddev composer install

# Verificar Drupal
ddev drush core:status
ddev drush cr
```

## Passo 3: Verificar Acessos

| URL | Credenciais |
|-----|-------------|
| https://archtech.ddev.site | admin / admin |
| https://archtech.ddev.site:15672 | archtech / archtech |
| https://archtech.ddev.site:3001 | admin / admin |

## Passo 4: Conhecendo o Projeto

```bash
# Estrutura
ls infrastructure/          # Terraform, K8s, Docker
ls web/modules/custom/      # Módulos Drupal custom
ls recipes/                 # Drupal Recipes
ls docs/                    # Documentação
```

## Passo 5: Workflow diário

```bash
ddev start                  # Iniciar ambiente
ddev composer install       # Instalar dependências
ddev drush cr               # Limpar cache
ddev drush updb             # Atualizar banco
ddev logs                   # Ver logs
```

## Troubleshooting

```bash
# Se algo falhar:
ddev poweroff && ddev start
ddev describe
```

## Documentação Relacionada

- [DDEV Setup](../guides/ddev-setup.md)
- [Coding Standards](../guides/coding-standards.md)
- [Event Storming](../architecture/event-storming.md)
