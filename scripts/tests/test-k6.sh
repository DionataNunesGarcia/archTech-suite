#!/bin/bash
set -euo pipefail

echo "=== k6 Load Tests ==="

if ! command -v k6 &>/dev/null; then
	echo "⚠️  k6 not installed. Skipping load tests."
	echo "Install with: brew install k6  or  docker pull grafana/k6"
	exit 0
fi

cd "$(dirname "$0")/../.."

k6 run infrastructure/k6/load-test.js --vus 10 --duration 10s 2>&1
echo "✅ k6 load tests passed"

k6 run infrastructure/k6/ai-agent-load.js --vus 5 --duration 10s 2>&1
echo "✅ AI agent load tests passed"
