#!/bin/bash
# Test: Grafana dashboards and datasource configs
set -u
FAIL=0

echo "=========================================="
echo " Grafana Tests"
echo "=========================================="

# Validate dashboard JSON
echo "--- Dashboard JSON validation ---"
while IFS= read -r db; do
  if python3 -c "
import json, sys
with open('$db') as f:
    d = json.load(f)
required = ['title', 'panels']
for field in required:
    if field not in d:
        print(f'missing field: {field}')
        sys.exit(1)
print(f'title: {d.get(\"title\",\"?\")}')
" 2>&1; then
    echo "  ✅ $db"
  else
    echo "  ❌ $db"
    FAIL=$((FAIL + 1))
  fi
done < <(find infrastructure/observability/grafana/dashboards -name '*.json')

# Validate datasource YAML
echo "--- Datasource YAML validation ---"
while IFS= read -r ds; do
  if python3 -c "
import yaml, sys
d = yaml.safe_load(open('$ds'))
if not d or 'datasources' not in d:
    print('missing datasources key')
    sys.exit(1)
for src in d['datasources']:
    if 'name' not in src or 'type' not in src or 'url' not in src:
        print(f'incomplete datasource: {src.get(\"name\",\"?\")}')
        sys.exit(1)
names = [s['name'] for s in d['datasources']]
print(f'datasources: {\", \".join(names)}')
" 2>&1; then
    echo "  ✅ $ds"
  else
    echo "  ❌ $ds"
    FAIL=$((FAIL + 1))
  fi
done < <(find infrastructure/observability/grafana/datasources -name '*.yml' -o -name '*.yaml')

# Validate dashboard provider config
echo "--- Dashboard provider config ---"
if [ -f "infrastructure/observability/grafana/dashboards/dashboards.yaml" ]; then
  if python3 -c "
import yaml, sys
d = yaml.safe_load(open('infrastructure/observability/grafana/dashboards/dashboards.yaml'))
if not d or 'providers' not in d:
    print('missing providers key')
    sys.exit(1)
for p in d['providers']:
    if 'name' not in p or 'type' not in p or 'options' not in p:
        print(f'incomplete provider: {p.get(\"name\",\"?\")}')
        sys.exit(1)
    if 'path' not in p['options']:
        print(f'provider {p[\"name\"]} missing options.path')
        sys.exit(1)
print(f'providers: {\", \".join(p[\"name\"] for p in d[\"providers\"])}')
" 2>&1; then
    echo "  ✅ Provider config valid"
  else
    echo "  ❌ Provider config invalid"
    FAIL=$((FAIL + 1))
  fi
fi

echo "---"
echo "Grafana tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
