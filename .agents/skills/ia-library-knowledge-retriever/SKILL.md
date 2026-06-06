---
name: ia-library-knowledge-retriever
description: RAG-based Knowledge Retrieval agent — answers technical questions grounded in the technical library documents. Triggered by GET /api/v1/library/ask. Uses pgvector (cosine distance, top-K=5), text-embedding-3-large, chunk size 512 tokens with 50 token overlap.
---

# IA_KnowledgeRetriever

Retrieves and answers questions based on indexed technical documents.

## Trigger

`GET /api/v1/library/ask`

## Prompt Template

```
Responda à pergunta '{question}' utilizando APENAS informações da biblioteca técnica.
Documentos relevantes recuperados: {retrievedChunks}.
Cite a fonte (norma/documento) para cada afirmação.
Se a resposta não estiver na biblioteca, informe explicitamente: "Esta informação não está disponível na biblioteca técnica."
Seja preciso e objetivo. Inclua referências a artigos/seções específicas das normas.
```

## RAG Config

- Embedding model: text-embedding-3-large (1536d)
- Vector store: pgvector (cosine distance, top-K=5)
- Chunk size: 512 tokens, overlap: 50 tokens

## Prompt Version

`ia-library/knowledge-retriever@1.0.0`
