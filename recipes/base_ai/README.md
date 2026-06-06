# Base AI Recipe

AI Core setup for Drupal with OpenAI and Ollama providers.

## What's Installed

- `drupal/ai` - AI Core module
- `drupal/ai_provider_openai` - OpenAI provider
- `drupal/ai_provider_ollama` - Ollama provider (local)
- `drupal/ai_api_explorer` - Development testing UI

## Configuration

### OpenAI

```bash
# Add API key
ddev drush key:save openai_api sk-YOUR-KEY --key-provider=config

# Set as default
ddev drush config-set ai.settings default_provider openai
ddev drush config-set ai_provider_openai.settings api_key openai_api
```

### Ollama (Local)

```bash
# Ensure Ollama is running on host
ollama serve

# Configure in Drupal
ddev drush config-set ai_provider_ollama.settings host_name 'http://host.docker.internal'
ddev drush config-set ai_provider_ollama.settings port 11434

# Set as default
ddev drush config-set ai.settings default_provider ollama
```

## Testing

```bash
# Via Drush
ddev drush ai:chat "Hello" --provider=openai

# Via DDEV command
ddev ai ai-chat "Hello"
ddev ai ai-test
```

## Resources

- `/admin/config/ai` - AI settings
- `/admin/config/ai/providers` - Configure providers
- `/admin/config/ai/explorers` - Test APIs