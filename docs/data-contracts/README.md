# Data Contracts

JSON Schema dos domain events do ArchTech Suite.

## Estrutura

```
data-contracts/
├── events/                    ← JSON Schema de cada domain event
│   ├── client-portal/         ← Eventos do Portal do Cliente
│   ├── crm/                   ← Eventos do CRM
│   ├── proposals/             ← Eventos de Propostas
│   ├── financeiro/            ← Eventos Financeiros
│   ├── library/               ← Eventos da Biblioteca
│   ├── permits/               ← Eventos de Aprovação
│   ├── suppliers/             ← Eventos de Fornecedores
│   ├── facilities/            ← Eventos de Facilities
│   └── bim-twin/              ← Eventos BIM
└── README.md
```

## Regras

- Todo domain event tem schema JSON Schema versionado
- Schemas são validados no CI
- Alterações em schemas exigem PR aprovado
- Eventos seguem estrutura base:

```json
{
	"event_id": "uuid-v4",
	"event_type": "EventName",
	"event_version": "1.0",
	"source_module": "ia_{squad}",
	"trace_id": "trace-id",
	"occurred_at": "ISO8601",
	"payload": {}
}
```

## Catálogo de Eventos

Ver `archtech-prd.md` seção 7 para catálogo completo.
