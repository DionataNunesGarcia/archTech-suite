---
name: ia-bim-twin-integrator
description: IFC Processing agent — extracts metadata, quantities, and structure from BIM models. Triggered by IFC file upload via POST /api/v1/bim/models/ifc (async processing). Returns structured JSON for Drupal persistence.
---

# IA_BIM_Integrator

Processes IFC files and integrates BIM model data into the platform.

## Trigger

Upload de arquivo IFC via `POST /api/v1/bim/models/ifc` (processamento assíncrono)

## Prompt Template

```
Processe o arquivo IFC {ifcFilePath} e integre-o ao modelo BIM do projeto {projectName}.
Extraia: lista de elementos por categoria (paredes, lajes, pilares, etc.), quantitativos de materiais,
dados de espaços (áreas, volumes), sistemas MEP identificados e metadados relevantes.
Identifique conflitos ou inconsistências no modelo.
Retorne JSON estruturado para persistência no Drupal.
```

## Pipeline

1. IfcOpenShell (Python worker) extracts raw geometry + metadata
2. IA_BIM_Integrator processes and structures data
3. Metadata persisted in Drupal; geometry in 3D viewer (Speckle/Three.js)
4. Publishes BIMModelProcessed event

## Prompt Version

`ia-bim-twin/integrator@1.0.0`
