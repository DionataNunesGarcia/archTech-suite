#!/bin/bash
# RabbitMQ setup script for ArchTech Suite
# Usage: ./scripts/setup-rabbitmq.sh [host] [port] [user] [password]

set -euo pipefail

RABBIT_HOST="${1:-localhost}"
RABBIT_PORT="${2:-15672}"
RABBIT_USER="${3:-archtech}"
RABBIT_PASS="${4:-archtech}"

BASE_URL="http://${RABBIT_HOST}:${RABBIT_PORT}"
AUTH="${RABBIT_USER}:${RABBIT_PASS}"

echo "🔧 Setting up RabbitMQ for ArchTech Suite..."
echo "   Host: ${BASE_URL}"

# Ensure password is correct
echo "→ Ensuring user password..."
docker exec ddev-archtech-rabbitmq rabbitmqctl change_password "${RABBIT_USER}" "${RABBIT_PASS}" > /dev/null 2>&1 || true

# Import definitions
echo "→ Importing exchanges, queues, bindings, and policies..."
HTTP_CODE=$(curl -s -w "%{http_code}" -o /dev/null -u "${AUTH}" -X POST \
  "${BASE_URL}/api/definitions" \
  -H "Content-Type: application/json" \
  -d @infrastructure/rabbitmq/definitions.json)

if [ "$HTTP_CODE" = "204" ]; then
  echo "✅ RabbitMQ setup complete!"
else
  echo "⚠️  Import returned HTTP ${HTTP_CODE}. Check credentials."
  exit 1
fi

# Summary
echo ""
echo "📊 Resources created:"
echo "   Exchanges: archtech.leads, archtech.marketing, archtech.projects,"
echo "              archtech.construction, archtech.internal, archtech.ai.jobs,"
echo "              archtech.dlq, archtech.retry"
echo "   Queues:    18 domain-specific queues + 1 DLQ"
echo "   Policies:  DLQ policy + Retry policy"
echo ""
echo "🔗 Management UI: ${BASE_URL}"
