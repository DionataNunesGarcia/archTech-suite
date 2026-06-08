#!/bin/bash
# Test: Prometheus alert rules and config
set -u
FAIL=0

echo "=========================================="
echo " Prometheus Tests"
echo "=========================================="

PROMTOOL=$(command -v promtool || echo "$HOME/.local/bin/promtool")
if [ ! -x "$PROMTOOL" ]; then
  echo "❌ promtool not found"
  exit 1
fi

# Test alert rules
echo "--- Alert rules ---"
RULES_FILE="infrastructure/observability/prometheus/rules/archtech-alerts.yml"
if [ -f "$RULES_FILE" ]; then
  if "$PROMTOOL" check rules "$RULES_FILE" 2>&1; then
    echo "  ✅ Rules valid"
  else
    echo "  ❌ Rules invalid"
    FAIL=$((FAIL + 1))
  fi
else
  echo "  ⏭️  Rules file not found"
fi

# Test config files
echo "--- Config files ---"
for cfg in infrastructure/observability/prometheus/prometheus.yml infrastructure/observability/prometheus/prometheus-local.yml; do
  [ ! -f "$cfg" ] && echo "  ⏭️  $cfg not found, skipping" && continue
  echo "  → $cfg"
  if "$PROMTOOL" check config "$cfg" 2>&1; then
    echo "    ✅ Config valid"
  else
    echo "    ❌ Config invalid"
    FAIL=$((FAIL + 1))
  fi
done

# Unit test: verify alert rule count matches documented
echo "--- Alert count verification ---"
RULE_COUNT=$(grep -c 'alert:' "$RULES_FILE" 2>/dev/null || echo 0)
if [ "$RULE_COUNT" -eq 10 ]; then
  echo "  ✅ 10 alert rules as documented"
else
  echo "  ⚠️  Expected 10 alert rules, found $RULE_COUNT"
fi

echo "---"
echo "Prometheus tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
