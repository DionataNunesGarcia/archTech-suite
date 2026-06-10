# Runbook: Configuração do WAF

## Stack

- **Cloudflare WAF** com OWASP CRS (Core Rule Set)
- **Rate Limiting** por IP e endpoint
- **DDoS Layer 7** protection
- **Bot Management** para proteção contra scrapers

## Regras OWASP CRS

| Categoria        | Ação  | Descrição       |
| ---------------- | ----- | --------------- |
| SQL Injection    | Block | `942100-942999` |
| XSS              | Block | `941100-941999` |
| LFI/RFI          | Block | `931100-931999` |
| RCE              | Block | `932100-932999` |
| PHP Injection    | Block | `933100-933999` |
| Session Fixation | Block | `943100-943999` |
| Java Injection   | Block | `944100-944999` |

## Rate Limiting

| Endpoint         | Limite        | Janela     | Ação         |
| ---------------- | ------------- | ---------- | ------------ |
| `/api/v1/*`      | 1000 requests | 10 minutos | Block por 1h |
| `/api/v1/auth/*` | 50 requests   | 10 minutos | Block por 1h |
| `/api/v1/leads`  | 100 requests  | 10 minutos | Block por 1h |

## DDoS Protection

- Layer 7: inspeção de headers User-Agent, referrer, cookie
- Auto-mitigation para ataques volumétricos
- Rate limiting adaptativo baseado em perfil de tráfego

## Bot Management

| Tipo                         | Ação      |
| ---------------------------- | --------- |
| Verified bots (Google, Bing) | Allow     |
| Known malicious              | Block     |
| Spoofed UA                   | Block     |
| Headless browsers            | Challenge |

## Operações

### Cloudflare

```bash
# Verificar regras ativas
curl -X GET "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/rulesets"

# Atualizar WAF
terraform apply -target=module.waf
```

### Terraform

O WAF é gerenciado via Terraform em `infrastructure/waf/cloudflare-waf.tf`.

## Troubleshooting

| Problema               | Causa                  | Solução                        |
| ---------------------- | ---------------------- | ------------------------------ |
| Falso positivo         | Regra muito restritiva | Adicionar exceção no WAF       |
| Rate limit atingido    | Bug ou ataque          | Verificar logs, ajustar limite |
| Bot legítimo bloqueado | UA não reconhecido     | Adicionar à allowlist          |

## Referências

- [Cloudflare WAF Documentation](https://developers.cloudflare.com/waf/)
- [OWASP CRS](https://owasp.org/www-project-modsecurity-core-rule-set/)
- [Terraform WAF](file:///home/dionata/projects/local/archTech-suite/infrastructure/waf/)
