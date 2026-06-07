# ArchTech Suite — Threat Model (STRIDE)

## Metodologia

Análise STRIDE por camada da arquitetura, identificando ameaças, impacto e mitigações.

## 1. Web Application (Frontend Next.js)

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| XSS em conteúdo gerado por IA | Spoofing | Alto | Sanitização com DOMPurify + Content Security Policy |
| Exposição de tokens OAuth no client | Information Disclosure | Alto | HttpOnly cookies, state parameter PKCE |
| Clickjacking em dashboards | Elevation of Privilege | Médio | Frame-Security headers (X-Frame-Options: DENY) |
| Injeção de script via prompts de IA | Spoofing | Alto | Output encoding + OpenAI Moderation API |

## 2. API Gateway

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| Rate limiting bypass | Denial of Service | Alto | Rate limiting por IP + token (Redis sliding window) |
| Brute force em endpoints de auth | Spoofing | Alto | Account lockout após 5 tentativas + MFA |
| Injeção de headers maliciosos | Tampering | Médio | Validação de headers no gateway |
| Request smuggling | Tampering | Alto | WAF com OWASP CRS + validação de content-length |

## 3. Backend (Drupal 11)

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| SQL Injection via JSON:API filters | Tampering | Crítico | Drupal Database API (query parametrizada), input sanitization |
| Mass assignment via REST/GraphQL | Tampering | Alto | GraphQL field-level permissions, JSON:API resource wrapping |
| Insecure Direct Object Reference (IDOR) | Information Disclosure | Alto | Verificação de ownership por squad (RBAC) |
| Privilege escalation via role inheritance | Elevation of Privilege | Alto | RBAC granular, testes de permissão automatizados |
| Cache poisoning via Redis | Tampering | Médio | Prefixo de cache por tenant, validação de entrada |

## 4. Message Broker (RabbitMQ)

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| Message spoofing (evento falso) | Spoofing | Alto | Assinatura de eventos com HMAC, validação de source_module |
| Replay attack (evento reproduzido) | Tampering | Médio | Idempotência obrigatória em todos os consumers |
| Queue overflow | Denial of Service | Médio | TTL de 7 dias, max retries 3, DLQ monitoring |
| Unauthorized queue access | Information Disclosure | Alto | RabbitMQ Vhost isolation, TLS, password rotation |

## 5. AI Gateway

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| PII leakage via prompts | Information Disclosure | Crítico | Microsoft Presidio masking antes de enviar a API externa |
| Prompt injection | Spoofing | Alto | System prompt hardening, input validation, output moderation |
| Injeção indireta via RAG documents | Tampering | Alto | Chunk validation, source whitelist, citation enforcement |
| Model denial of service (abuso de créditos) | Denial of Service | Alto | Budget tracking por squad, rate limit por provider, circuit breaker |
| Model poisoning via feedback loop | Tampering | Alto | Human-in-the-loop para todos os outputs que alteram dados |

## 6. Data Layer (PostgreSQL + Redis)

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| Unauthorized data access | Information Disclosure | Crítico | Row-level security por squad, Vault para credenciais |
| Data exfiltration via backup | Information Disclosure | Alto | Backups criptografados, retention policy, audit logging |
| Redis key collision entre tenants | Tampering | Médio | Key prefixing, Redis ACLs desde v7 |
| Injeção via pgvector embeddings | Tampering | Médio | Validação de vetores antes de persistir |

## 7. CI/CD Pipeline

| Ameaça | Tipo STRIDE | Impacto | Mitigação |
|--------|-------------|---------|-----------|
| Compromised dependency (supply chain) | Tampering | Crítico | Snyk SAST + Dependabot + signature verification |
| Secret exposure in build logs | Information Disclosure | Crítico | Vault para credenciais, secrets scanning no CI |
| Unauthorized code push | Tampering | Alto | Branch protection, PR approval obrigatório, signed commits |
| Docker image vulnerability | Tampering | Alto | Image scanning no CI, minimal base images, non-root user |

## Matriz de Risco

| Severidade | Quantidade | Ação |
|------------|-----------|------|
| Crítico | 4 | Bloqueante para deploy |
| Alto | 12 | Mitigação obrigatória antes de produção |
| Médio | 4 | Mitigação planejada no roadmap |

## Revisão

| Data | Versão | Revisor | Status |
|------|--------|---------|--------|
| 2025-06 | 1.0 | Security Champion | Draft |
