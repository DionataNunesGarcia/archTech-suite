# ArchTech Suite — Backlog Detalhado (Sprint 1)

## Sprint 1 — Portal do Cliente (MVP)

**Duração:** 2 semanas
**Squad:** Client Portal
**Dependências:** archtech_base, archtech_events

---

### US-CP-001: Timeline do Projeto
**Como** cliente  
**Quero** visualizar a timeline do meu projeto com status por fase  
**Para** acompanhar o progresso em tempo real

**Critérios de Aceite (BDD):**
- Dado que o cliente está autenticado no portal
- Quando acessa `/projetos/{id}/timeline`
- Então vê todas as fases do projeto ordenadas por data
- E cada fase exibe título, status e percentual concluído
- E fases com status "aguardando_aprovacao" destacam o botão de aprovação

**Story Points:** 5
**Prioridade:** Alta

---

### US-CP-002: Aprovação de Etapa
**Como** cliente  
**Quero** aprovar ou rejeitar uma etapa do projeto com comentário  
**Para** dar feedback formal à equipe

**Critérios de Aceite (BDD):**
- Dado que o cliente está na timeline do projeto
- Quando clica em "Aprovar" ou "Rejeitar" em uma fase pendente
- E digita um comentário opcional
- Então o sistema registra a resposta
- E publica o evento `ApprovalResponded` no RabbitMQ
- E notifica o PM por email (IA_ClientNotifier)

**Story Points:** 8
**Prioridade:** Alta

---

### US-CP-003: Upload de Documentos
**Como** cliente  
**Quero** enviar documentos (fotos, plantas) para o projeto  
**Para** compartilhar referências com a equipe

**Critérios de Aceite (BDD):**
- Dado que o cliente está no portal do projeto
- Quando faz upload de um arquivo (PDF, PNG, JPG, max 20MB)
- Então o documento fica visível na lista de documentos do projeto
- E o evento `DocumentUploadedByClient` é publicado
- E a equipe recebe notificação

**Story Points:** 5
**Prioridade:** Média

---

### US-CP-004: Notificações por Email
**Como** cliente  
**Quero** receber notificações por email sobre atualizações do projeto  
**Para** não perder prazos de aprovação

**Critérios de Aceite (BDD):**
- Dado que o cliente tem notificações habilitadas
- Quando uma nova fase é adicionada à timeline
- Então o IA_ClientNotifier envia email com resumo personalizado
- Quando uma aprovação está pendente há 48h
- Então o IA_ApprovalReminder envia lembrete

**Story Points:** 3
**Prioridade:** Alta

---

### US-CP-005: Extrato Financeiro
**Como** cliente  
**Quero** visualizar parcelas, pagamentos e próximos vencimentos  
**Para** ter transparência financeira do projeto

**Critérios de Aceite (BDD):**
- Dado que o cliente acessa a aba financeira do projeto
- Então vê valor total, valor pago e parcelas com status
- E parcelas vencidas destacam-se em vermelho

**Story Points:** 5
**Prioridade:** Média

---

## Sprint 2 — CRM (MVP)

### US-CRM-001: Criação de Lead
**Como** arquiteto comercial  
**Quero** cadastrar leads manualmente ou via formulário público  
**Para** registrar novos contatos comerciais

**Story Points:** 3

### US-CRM-002: Pipeline Kanban
**Como** gerente de vendas  
**Quero** visualizar leads em pipeline Kanban por estágio  
**Para** ter visão do funil de vendas

**Story Points:** 8

### US-CRM-003: Briefing Estruturado
**Como** arquiteto comercial  
**Quero** preencher briefing vinculado a um lead  
**Para** capturar requisitos do projeto

**Story Points:** 5

### US-CRM-004: Score Automático por IA
**Como** gerente de vendas  
**Quero** que leads sejam pontuados automaticamente (1-10)  
**Para** priorizar os mais quentes

**Story Points:** 8

---

## Sprint 3 — Propostas (MVP)

### US-PR-001: Criação de Proposta
**Como** arquiteto comercial  
**Quero** criar proposta comercial a partir de template  
**Para** padronizar o formato de apresentação

**Story Points:** 5

### US-PR-002: Versionamento
**Como** gerente de projeto  
**Quero** manter histórico de versões da proposta  
**Para** rastrear alterações

**Story Points:** 3

---

## Definição de Pronto (Definition of Ready)

- [ ] História com critérios de aceite BDD
- [ ] Design/UX aprovado
- [ ] API spec definida (OpenAPI)
- [ ] Dependências mapeadas
- [ ] Estimativa de pontos

## Definição de Concluído (Definition of Done)

- [ ] Código implementado e commitado
- [ ] Testes unitários ≥ 80% coverage
- [ ] Testes de integração passando
- [ ] API spec validada (Spectral)
- [ ] Code review aprovado
- [ ] Deploy em staging verificado
