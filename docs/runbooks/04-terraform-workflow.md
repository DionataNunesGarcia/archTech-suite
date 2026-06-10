# Runbook: Workflow de Infraestrutura Terraform

## Módulos

| Módulo | Diretório                                 | Descrição                 |
| ------ | ----------------------------------------- | ------------------------- |
| VPC    | `infrastructure/terraform/modules/vpc/`   | VPC, subnets, NAT gateway |
| EKS    | `infrastructure/terraform/modules/eks/`   | Cluster EKS, node groups  |
| RDS    | `infrastructure/terraform/modules/rds/`   | PostgreSQL 18 gerenciado  |
| Redis  | `infrastructure/terraform/modules/redis/` | ElastiCache Redis         |

## Ambientes

| Ambiente   | Workspace | TFVars                                  |
| ---------- | --------- | --------------------------------------- |
| Dev        | `dev`     | `environments/dev/terraform.tfvars`     |
| Staging    | `staging` | `environments/staging/terraform.tfvars` |
| Production | `prod`    | `environments/prod/terraform.tfvars`    |

## Workflow

```bash
# Navegar para o ambiente
cd infrastructure/terraform/environments/dev

# Inicializar
terraform init

# Selecionar workspace
terraform workspace select dev

# Validar
terraform validate
tflint

# Visualizar mudanças
terraform plan -var-file=terraform.tfvars

# Aplicar
terraform apply -var-file=terraform.tfvars

# Destruir (apenas dev)
terraform destroy -var-file=terraform.tfvars
```

## CI/CD via GitHub Actions

O Terraform é executado via workflow manual ou automático em `main`.

## Boas Práticas

- Sempre revisar `terraform plan` antes de aplicar
- State file no backend S3 com DynamoDB lock
- Secrets via Vault, nunca em variáveis de ambiente do Terraform
- Módulos versionados com tags semânticas

## Troubleshooting

| Problema             | Solução                                         |
| -------------------- | ----------------------------------------------- |
| State lock           | `terraform force-unlock <lock_id>`              |
| Credenciais AWS      | Verificar `AWS_PROFILE` ou `~/.aws/credentials` |
| Dependência circular | Revisar `depends_on` nos módulos                |

## Referências

- [Terraform Documentation](https://developer.hashicorp.com/terraform/docs)
- [Módulos](file:///home/dionata/projects/local/archTech-suite/infrastructure/terraform/)
