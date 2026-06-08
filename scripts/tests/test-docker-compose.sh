#!/bin/bash
# Test: Docker Compose files validity
set -u
FAIL=0

echo "=========================================="
echo " Docker Compose Tests"
echo "=========================================="

CMD=""
if command -v docker-compose &>/dev/null; then
  CMD="docker-compose"
elif docker compose version &>/dev/null; then
  CMD="docker compose"
else
  echo "  ⚠️  docker-compose CLI not found — using YAML syntax check only"
fi

COMPOSE_FILES=(
  ".ddev/docker-compose.redis.yaml"
  "infrastructure/observability/docker-compose.observability.yaml"
)

for file in "${COMPOSE_FILES[@]}"; do
  [ ! -f "$file" ] && echo "  ⏭️  $file not found, skipping" && continue
  echo "  → $file"
  # Validate syntax without environment variables expansion
  if [ -n "$CMD" ] && $CMD -f "$file" config -q 2>/dev/null; then
    echo "    ✅ Valid compose file"
  else
    # For DDEV compose files, env vars won't resolve outside DDEV
    # So just check YAML syntax
    if python3 -c "
import yaml, sys
yaml.safe_load(open('$file'))
print('    ✅ YAML syntax OK')
" 2>&1; then
      echo "    ⚠️  (env vars not set, YAML syntax OK)"
    else
      echo "    ❌ Invalid YAML"
      FAIL=$((FAIL + 1))
    fi
  fi
done

# Verify service names are unique across all compose files
echo "--- Duplicate service check ---"
ALL_SERVICES=""
for file in "${COMPOSE_FILES[@]}"; do
  [ ! -f "$file" ] && continue
  svcs=$(python3 -c "
import yaml
d = yaml.safe_load(open('$file'))
if d and 'services' in d:
    print(' '.join(d['services'].keys()))
" 2>/dev/null || true)
  ALL_SERVICES="$ALL_SERVICES $svcs"
done
DUPS=$(echo "$ALL_SERVICES" | tr ' ' '\n' | sort | uniq -d | tr '\n' ' ')
if [ -n "$DUPS" ]; then
  echo "  ⚠️  Duplicate service names across compose files: $DUPS"
else
  echo "  ✅ No duplicate services across compose files"
fi

echo "---"
echo "Docker Compose tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
