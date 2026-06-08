#!/bin/bash
set -euo pipefail

echo "=== Accessibility Audit ==="

cd "$(dirname "$0")/../../frontend"

npx playwright install --with-deps chromium 2>/dev/null

npx playwright test --reporter=list e2e/a11y.spec.ts 2>&1

echo "✅ Accessibility audit passed"
