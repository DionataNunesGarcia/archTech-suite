#!/usr/bin/env python3
"""Validate all YAML files in the project.
Handles multi-document (---) YAML and skips Go template files."""
import glob
import sys
import yaml

# Files that use Go templates (Helm) — not valid YAML until rendered
TEMPLATE_PATTERNS = (
    '/templates/',
    'helm/templates/',
)

fail = 0
patterns = [
    '.github/workflows/*.yml',
    'infrastructure/**/*.yml',
    'infrastructure/**/*.yaml',
]

files = []
for p in patterns:
    files.extend(glob.glob(p, recursive=True))

for f in sorted(set(files)):
    # Skip Helm template files (Go templated YAML)
    if any(t in f for t in TEMPLATE_PATTERNS):
        print(f'⏭️  {f}: Helm template (skipped)')
        continue

    try:
        with open(f) as fh:
            docs = list(yaml.safe_load_all(fh))
        print(f'✅ {f}: {len(docs)} document(s)')
    except Exception as e:
        print(f'❌ {f}: {e}')
        fail += 1

if fail:
    print(f'❌ {fail} file(s) failed YAML validation')
    sys.exit(1)
else:
    print(f'✅ All YAML files valid')
