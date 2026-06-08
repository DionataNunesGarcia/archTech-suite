#!/bin/bash
# Test: RabbitMQ definitions topology
set -u
FAIL=0

echo "=========================================="
echo " RabbitMQ Tests"
echo "=========================================="

DEFS="infrastructure/rabbitmq/definitions.json"
if [ ! -f "$DEFS" ]; then
  echo "❌ $DEFS not found"
  exit 1
fi

echo "--- Definitions JSON validation ---"
if python3 -c "
import json, sys
with open('$DEFS') as f:
    d = json.load(f)
required = ['rabbit_version', 'exchanges', 'queues', 'bindings', 'policies']
for field in required:
    if field not in d:
        print(f'missing field: {field}')
        sys.exit(1)
print(f'version: {d[\"rabbit_version\"]}')
print(f'exchanges: {len(d[\"exchanges\"])}')
print(f'queues: {len(d[\"queues\"])}')
print(f'bindings: {len(d[\"bindings\"])}')
print(f'policies: {len(d[\"policies\"])}')
" 2>&1; then
  echo "  ✅ $DEFS valid"
else
  echo "  ❌ $DEFS invalid"
  FAIL=$((FAIL + 1))
fi

# Verify DLQ + Retry policies exist
echo "--- Required policies ---"
for policy in archtech.dlq-policy archtech.retry-policy; do
  if grep -q "\"$policy\"" "$DEFS" 2>/dev/null; then
    echo "  ✅ Policy $policy found"
  else
    echo "  ❌ Policy $policy missing"
    FAIL=$((FAIL + 1))
  fi
done

# Verify DLQ exchange + queue exist
echo "--- DLQ topology ---"
for item in archtech.dlq archtech.dlq.all; do
  if grep -q "\"$item\"" "$DEFS" 2>/dev/null; then
    echo "  ✅ $item found"
  else
    echo "  ❌ $item missing"
    FAIL=$((FAIL + 1))
  fi
done

# Verify all exchanges have at least one binding (exclude DLQ fanout + retry internal)
echo "--- Binding completeness ---"
if python3 -c "
import json, sys
with open('$DEFS') as f:
    d = json.load(f)
exchanges = {e['name'] for e in d['exchanges']}
bound = {b['source'] for b in d['bindings']}
# DLQ is fanout (receive-only), retry is internal (no direct bindings)
exclude = {'archtech.dlq', 'archtech.retry'}
unbound = exchanges - bound - exclude
if unbound:
    print(f'unbound exchanges (excluding DLQ/retry): {unbound}')
    sys.exit(1)
print(f'all {len(exchanges)} exchanges have bindings')
" 2>&1; then
  echo "  ✅ All exchanges bound"
else
  echo "  ❌ Unbound exchanges found"
  FAIL=$((FAIL + 1))
fi

echo "---"
echo "RabbitMQ tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
