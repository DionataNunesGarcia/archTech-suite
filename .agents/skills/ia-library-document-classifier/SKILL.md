---
name: ia-library-document-classifier
description: Document Classification agent — categorizes and extracts metadata from new technical documents automatically. Triggered by DocumentUploaded event on RabbitMQ (async processing). Returns JSON with extracted fields.
---

# IA_DocumentClassifier

Classifies and extracts metadata from uploaded technical documents.

## Trigger

Evento `DocumentUploaded` no RabbitMQ (exchange: `archtech.library`)

## Prompt Template

```
Classifique o documento '{documentTitle}' (preview: {documentPreview}) nas categorias existentes da biblioteca.
Extraia: tipo (NBR/lei/manual/etc), número da norma (se aplicável), órgão emissor, data de publicação,
municípios de aplicação, tags relevantes e fase de projeto onde se aplica.
Retorne apenas JSON com os campos extraídos.
```

## Output

JSON-only response with classified metadata fields.

## Prompt Version

`ia-library/document-classifier@1.0.0`
