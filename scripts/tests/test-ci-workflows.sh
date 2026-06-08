#!/bin/bash
# Test: GitHub Actions workflow YAML validity
set -u
FAIL=0

echo "=========================================="
echo " CI/CD Workflow Tests"
echo "=========================================="

echo "--- YAML syntax validation ---"
while IFS= read -r wf; do
  if python3 -c "
import yaml, sys
with open('$wf') as f:
    d = yaml.safe_load(f)
if d is None:
    print('empty file')
    sys.exit(1)
name = d.get('name', d.get('on', '?'))
print(f'name: {name}')
" 2>&1; then
    echo "  ✅ $wf"
  else
    echo "  ❌ $wf"
    FAIL=$((FAIL + 1))
  fi
done < <(find .github/workflows -name '*.yml')

# Check for stale patterns: non-blocking quality gates
echo "--- Quality gate check (non-blocking) ---"
for wf in .github/workflows/*.yml; do
  [ ! -f "$wf" ] && continue
  suspicious=$(grep -n '|| true' "$wf" 2>/dev/null || true)
  if [ -n "$suspicious" ]; then
    echo "  ⚠️  Non-blocking step(s) in $wf:"
    echo "$suspicious" | while IFS= read -r line; do echo "     $line"; done
  fi
done

# Verify required jobs exist in each workflow
echo "--- Required job check ---"
while IFS= read -r wf; do
  name=$(basename "$wf")
  case "$name" in
    backend-ci.yml)
      for job in lint sast test api-lint build deploy; do
        grep -q "  $job:" "$wf" 2>/dev/null && echo "  ✅ $name has $job" || echo "  ⚠️  $name missing job: $job"
      done
      ;;
    frontend-ci.yml)
      for job in lint type-check test lighthouse a11y build deploy; do
        grep -q "  $job:" "$wf" 2>/dev/null && echo "  ✅ $name has $job" || echo "  ⚠️  $name missing job: $job"
      done
      ;;
  esac
done < <(find .github/workflows -name '*.yml')

echo "---"
echo "CI/CD tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
