# Domain Events — JSON Schema

Schemas dos domain events do ArchTech Suite. Cada evento segue a estrutura base abaixo.

## Estrutura Base

```json
{
  "event_id": "uuid-v4",
  "event_type": "EventName",
  "event_version": "1.0",
  "source_module": "ia_{squad}",
  "trace_id": "trace-id",
  "occurred_at": "2025-06-01T10:00:00Z",
  "payload": {}
}
```

## Eventos por Bounded Context

| Evento | Emissor | Consumidores |
|--------|---------|-------------|
| ClientPortalAccessGranted | ia_client_portal | — |
| ApprovalRequested | ia_client_portal | ia_projetos |
| ApprovalResponded | ia_client_portal | ia_projetos, ia_suporte |
| DocumentUploadedByClient | ia_client_portal | ia_projetos |
| BriefingCreated | ia_crm | ia_atendimento, ia_marketing |
| OpportunityWon | ia_crm | ia_projetos, ia_client_portal |
| OpportunityLost | ia_crm | ia_marketing, ia_insights |
| ContractSigned | ia_crm | ia_projetos, ia_financeiro |
| ProposalCreated | ia_proposals | ia_crm |
| ProposalSent | ia_proposals | ia_crm, ia_client_portal |
| ProposalApproved | ia_proposals | ia_crm, ia_financeiro |
| ProposalSigned | ia_proposals | ia_crm, ia_projetos, ia_financeiro |
| InvoiceCreated | ia_financeiro | ia_suporte |
| PaymentReceived | ia_financeiro | ia_crm, ia_client_portal |
| InvoiceOverdue | ia_financeiro | ia_suporte |
| BudgetDeviationFinancial | ia_financeiro | ia_obras, ia_insights |
| DocumentIndexed | ia_library | ia_projetos, ia_suporte |
| StandardUpdated | ia_library | ia_projetos, ia_suporte |
| PermitProcessCreated | ia_permits | ia_projetos |
| RequirementReceived | ia_permits | ia_projetos, ia_suporte |
| PermitApproved | ia_permits | ia_projetos, ia_obras, ia_client_portal |
| SupplierActivated | ia_suppliers | ia_obras |
| SLAViolated | ia_suppliers | ia_obras, ia_suporte, ia_financeiro |
| SupplierRated | ia_suppliers | ia_insights |
| ProjectDelivered | ia_obras | ia_facilities |
| MaintenanceScheduled | ia_facilities | ia_client_portal, ia_suppliers |
| WarrantyExpiring | ia_facilities | ia_client_portal, ia_crm |
| RenovationOpportunity | ia_facilities | ia_crm |
| BIMModelProcessed | ia_bim_twin | ia_projetos, ia_facilities |
| SensorAnomalyDetected | ia_bim_twin | ia_facilities, ia_suporte |
