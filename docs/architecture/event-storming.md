# Event Storming — ArchTech Suite

## Visão Geral

Workshop de Event Storming realizado para mapear os domínios, bounded contexts, agregados e domain events do ArchTech Suite.

## Contextos e Eventos Mapeados

| Bounded Context | Aggregate Root | Domain Events |
|----------------|---------------|---------------|
| Client Portal | Project, ClientApprovalRequest | ClientPortalAccessGranted, ApprovalRequested, ApprovalResponded, DocumentUploadedByClient |
| Architecture CRM | Lead, Opportunity, Contract | BriefingCreated, OpportunityWon, OpportunityLost, ContractSigned |
| Commercial Proposals | CommercialProposal, DigitalSignature | ProposalCreated, ProposalSent, ProposalApproved, ProposalSigned |
| Financial Management | AccountReceivable, AccountPayable | InvoiceCreated, PaymentReceived, InvoiceOverdue, BudgetDeviationFinancial |
| Technical Library | TechnicalDocument, Standard | DocumentIndexed, StandardUpdated |
| Permit Approval | PermitProcess, Requirement | PermitProcessCreated, RequirementReceived, PermitApproved |
| Supplier Management | Supplier, ServiceLevelAgreement | SupplierActivated, SLAViolated, SupplierRated |
| Facilities | Warranty, MaintenanceSchedule | ProjectDelivered, MaintenanceScheduled, WarrantyExpiring, RenovationOpportunity |
| BIM Digital Twin | BIMModel, DigitalTwinAsset | BIMModelProcessed, SensorAnomalyDetected |

## Regras de Comunicação Cross-Context

```
❌ ia_atendimento → ia_marketing (proibido — acoplamento direto)
✅ ia_atendimento → archtech_events → RabbitMQ → ia_marketing (evento LeadCreated)
```

## Fluxos Core Identificados

1. **Lead → Cliente:** LeadCreated → BriefingCreated → OpportunityWon → ContractSigned → ProjectDelivered
2. **Proposta → Contrato:** ProposalCreated → ProposalApproved → ProposalSigned → ContractSigned
3. **Projeto → Obra → Entrega:** ProjectCreated → ConstructionStarted → ProjectDelivered
4. **Documento → Indexação:** DocumentUploaded → DocumentIndexed (biblioteca técnica)
