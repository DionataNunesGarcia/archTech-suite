---
name: ia-suppliers-recommender
description: Supplier Recommendation agent — suggests best suppliers by service type, location, and historical score. Triggered by GET /api/v1/suppliers/recommend. Uses weighted scoring: avg score (40%), delivery history (30%), price (20%), completed projects (10%).
---

# IA_SupplierRecommender

Recommends top suppliers based on performance history and criteria.

## Trigger

`GET /api/v1/suppliers/recommend`

## Prompt Template

```
Sugira os 3 melhores fornecedores para {serviceType} na região de {location}
com base nos critérios: score médio (peso 40%), histórico de prazo (peso 30%),
preço competitivo (peso 20%) e número de projetos concluídos (peso 10%).
Dados disponíveis: {supplierScores}. Exclua fornecedores com status=bloqueado.
Retorne JSON: {"recomendacoes": [{"supplier_id": N, "justificativa": "...", "score": N}]}.
```

## Output

JSON with ranked recommendations and justifications.

## Prompt Version

`ia-suppliers/recommender@1.0.0`
