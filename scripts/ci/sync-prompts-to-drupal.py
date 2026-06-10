#!/usr/bin/env python3
"""Sync YAML Prompt Registry files to Drupal config entities.

Reads all prompt YAML files from docs/ai-prompts/registry/
and generates Drupal config YAML files in the module's config/install/ directory.

Usage:
    python3 scripts/ci/sync-prompts-to-drupal.py
    python3 scripts/ci/sync-prompts-to-drupal.py --dry-run
"""

import argparse
import glob
import os
import sys
from pathlib import Path

import yaml


def yaml_to_drupal_config(prompt: dict, prompt_id: str) -> dict:
    """Converts a Prompt Registry YAML entry to a Drupal config entity."""
    # Convert prompt_id like 'ia-crm/lead-scorer' to machine name 'lead_scorer'
    machine_name = prompt_id.split('/')[-1].replace('-', '_')

    return {
        'id': machine_name,
        'label': prompt.get('agent', machine_name).replace('ia-', '').replace('-', ' ').title(),
        'provider': prompt.get('provider', 'openai'),
        'model': prompt.get('model', 'gpt-4o-mini'),
        'squad': prompt.get('squad', 'platform'),
        'system_prompt': prompt.get('system_prompt', ''),
        'user_prompt_template': prompt.get('user_prompt_template', ''),
        'temperature': float(prompt.get('temperature', 0.7)),
        'max_tokens': int(prompt.get('max_tokens', 4096)),
        'input_cost_per_1k': float(prompt.get('input_cost_per_1k', 0.0)),
        'output_cost_per_1k': float(prompt.get('output_cost_per_1k', 0.0)),
        'cache_ttl': int(prompt.get('cache_ttl', 0)),
        'status': True,
    }


def main() -> int:
    parser = argparse.ArgumentParser(
        description='Sync YAML Prompt Registry to Drupal config entities.',
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Preview changes without writing files.',
    )
    args = parser.parse_args()

    project_root = Path(__file__).resolve().parents[2]
    registry_dir = project_root / 'docs' / 'ai-prompts' / 'registry'
    config_dir = project_root / 'web' / 'modules' / 'custom' / 'archtech_ai_gateway' / 'config' / 'install'

    os.makedirs(config_dir, exist_ok=True)

    prompt_files = sorted(glob.glob(str(registry_dir / '*.yaml')))
    if not prompt_files:
        print('ERROR: No prompt YAML files found in registry.')
        return 1

    synced = 0
    for filepath in prompt_files:
        with open(filepath) as f:
            prompt = yaml.safe_load(f)

        prompt_id = prompt.get('prompt_id', '')
        if not prompt_id:
            print(f'SKIP  {os.path.basename(filepath)} — missing prompt_id')
            continue

        config = yaml_to_drupal_config(prompt, prompt_id)
        machine_name = config['id']
        config_filename = f'archtech_ai_gateway.archtech_prompt.{machine_name}.yml'
        config_path = config_dir / config_filename

        yaml_content = yaml.dump(
            config,
            default_flow_style=False,
            allow_unicode=True,
            sort_keys=False,
        )

        if args.dry_run:
            print(f'DRY-RUN  {config_filename}  ({prompt_id})')
        else:
            with open(config_path, 'w') as f:
                f.write(yaml_content)
            print(f'WRITE  {config_filename}  ({prompt_id})')

        synced += 1

    print(f'\n{synced} prompts synced to Drupal config.')
    print(f'Run "ddev drush config:import" or enable the module to load prompts.')
    return 0


if __name__ == '__main__':
    sys.exit(main())
