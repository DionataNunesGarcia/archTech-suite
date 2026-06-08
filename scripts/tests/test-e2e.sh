#!/bin/bash
set -euo pipefail

echo "=== E2E Tests (Playwright) ==="

cd "$(dirname "$0")/../../frontend"

npx playwright install --with-deps chromium 2>/dev/null

npx playwright test --reporter=list 2>&1

echo "✅ E2E tests passed"
