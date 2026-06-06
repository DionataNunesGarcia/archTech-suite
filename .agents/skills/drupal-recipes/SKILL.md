---
name: drupal-recipes
description: Create or modify ArchTech Drupal recipes. Follows Drupal 11 recipe conventions with recipe.yml, composer.json, config/actions, and optional content/. Use when building new bounded contexts or features.
---

# Drupal Recipes — ArchTech

Create or modify Drupal recipes following the ArchTech architecture (Bounded Contexts, Contract-First, Event-Driven).

## Recipe Structure

```
recipes/archtech_{context}/
├── recipe.yml          # Required
├── composer.json       # Required
├── config/             # Config YAML files
│   └── actions/        # Config action plugins
├── content/            # Default content (YAML)
└── assets/             # CSS/JS assets
```

## recipe.yml Template

```yaml
name: 'ArchTech {Context Name}'
description: '{Description of the bounded context}'
type: 'Site'
recipes:
  - archtech_base
install:
  - {modules}
config:
  strict: false
  import:
    {module}:
      - {config_entity}
  actions:
    {config_object}:
      simpleConfigUpdate:
        {key}: {value}
```

## Rules

- Never import another squad's recipe directly
- Communication cross-context must go through archtech_events (RabbitMQ)
- Every field, content type, and taxonomy must be defined as config YAML
- Use #config_target for ConfigFormBase extensions
- Always add composer.json with module dependencies
- Tag with `archtech` and the bounded context name
