---
name: ia-permits-advisor
description: Permit Advisory agent — guides on updated requirements by municipality and project type. Triggered by GET /api/v1/permits/advisor. Uses RAG with library context (municipal norms, zoning laws).
---

# IA_PermitAdvisor

Advises on permit requirements by municipality and project type.

## Trigger

`GET /api/v1/permits/advisor`

## Prompt Template

```
Quais são os requisitos para aprovação de um projeto {projectType} na prefeitura de {city}?
Considere: zoneamento ({zoningType}), área ({area}m²), uso ({usage}).
Base de conhecimento: {libraryContext}. Liste os documentos necessários, prazos típicos e principais exigências.
Alerte sobre mudanças recentes na legislação municipal se houver.
```

## RAG Config

Source: `ia_library` (normas municipais, leis de zoneamento)

## Prompt Version

`ia-permits/advisor@1.0.0`
