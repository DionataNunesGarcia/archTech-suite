#!/usr/bin/env python3
"""CI validation script for ArchTech Prompt Registry.

Validates all YAML prompt files in docs/ai-prompts/registry/
against the JSON Schema in docs/ai-prompts/prompt-registry-schema.json.

Usage:
    python3 scripts/ci/validate-prompts.py
    python3 scripts/ci/validate-prompts.py --file path/to/prompt.yaml
"""

import argparse
import glob
import json
import os
import sys
from pathlib import Path

import jsonschema
import yaml


def main() -> int:
    parser = argparse.ArgumentParser(description="Validate ArchTech Prompt Registry YAML files")
    parser.add_argument(
        "--file",
        help="Validate a single prompt file instead of all registry files",
    )
    parser.add_argument(
        "--schema",
        default=None,
        help="Path to JSON Schema (default: docs/ai-prompts/prompt-registry-schema.json)",
    )
    args = parser.parse_args()

    project_root = Path(__file__).resolve().parents[2]
    schema_path = args.schema or str(project_root / "docs" / "ai-prompts" / "prompt-registry-schema.json")
    registry_dir = project_root / "docs" / "ai-prompts" / "registry"

    with open(schema_path) as f:
        schema = json.load(f)

    if args.file:
        prompt_files = [args.file]
    else:
        prompt_files = sorted(glob.glob(str(registry_dir / "*.yaml")))
        if not prompt_files:
            print("ERROR: No prompt files found in registry directory.")
            return 1

    errors = 0
    for filepath in prompt_files:
        filename = os.path.basename(filepath)
        try:
            with open(filepath) as f:
                prompt = yaml.safe_load(f)
        except yaml.YAMLError as e:
            print(f"FAIL  {filename} — YAML parse error: {e}")
            errors += 1
            continue

        try:
            jsonschema.validate(instance=prompt, schema=schema)
        except jsonschema.ValidationError as e:
            print(f"FAIL  {filename} — Schema validation error: {e.message}")
            errors += 1
            continue

        version = prompt.get("version", "unknown")
        prompt_id = prompt.get("prompt_id", "unknown")
        expected_filename = f"{prompt_id.replace('/', '-')}@{version}.yaml"
        if filename != expected_filename:
            print(
                f"WARN  {filename} — Filename does not match convention. "
                f"Expected: {expected_filename}"
            )

        print(f"PASS  {filename} ({prompt_id}@{version})")

    total = len(prompt_files)
    print(f"\n{total - errors}/{total} prompt files validated successfully.")
    return 1 if errors > 0 else 0


if __name__ == "__main__":
    sys.exit(main())
