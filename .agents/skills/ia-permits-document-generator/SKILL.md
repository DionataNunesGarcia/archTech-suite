---
name: ia-permits-document-generator
description: Official Document Generation agent — generates standardized forms and memorials for permit approval processes. Triggered by POST /api/v1/permits/documents/generate. Human-in-the-loop: document requires review and signature by responsible engineer (ART/RRT).
---

# IA_DocumentGenerator

Generates official standardized documents for permit processes.

## Trigger

`POST /api/v1/permits/documents/generate`

## Prompt Template

```
Gere o {documentType} para o projeto {projectName} em {municipality}.
Dados do projeto: {projectData}. Responsável técnico: {responsibleEngineer}.
Siga rigorosamente o modelo exigido pela prefeitura de {municipality} (template: {templateRef}).
Preencha todos os campos obrigatórios. Sinaliza campos que precisam de revisão manual com [REVISAR].
```

## Human-in-Loop

true — documento gerado requer revisão e assinatura do responsável técnico (ART/RRT)

## Prompt Version

`ia-permits/document-generator@1.0.0`
