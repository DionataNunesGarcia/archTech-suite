# API Specifications

Contratos OpenAPI 3.1 das APIs do ArchTech Suite (Contract-First).

## Estrutura

```
api-specifications/
├── client-portal/       ← APIs do Portal do Cliente
├── crm/                 ← APIs do CRM
├── proposals/           ← APIs de Propostas
├── financeiro/          ← APIs Financeiras
├── library/             ← APIs da Biblioteca Técnica
├── permits/             ← APIs de Aprovação
├── suppliers/           ← APIs de Fornecedores
├── facilities/          ← APIs de Facilities
└── bim-twin/            ← APIs de BIM Digital Twin
```

## Regras

- Toda API é especificada via OpenAPI 3.1 **antes** da implementação
- Contrato é o artefato primário (Contract-First)
- Validação via Spectral (CI bloqueante)
- Mock server via Prism para desenvolvimento paralelo

## Endpoints por Contexto

Ver `archtech-prd-enhanced.xml` para lista completa de endpoints.
