#!/bin/bash
# Test: Terraform syntax, formatting, and basic security
set -u
FAIL=0

echo "=========================================="
echo " Terraform Tests"
echo "=========================================="

if command -v terraform &>/dev/null; then
  for dir in infrastructure/terraform/modules/*/ infrastructure/terraform/environments/*/; do
    [ -d "$dir" ] || continue
    count=$(find "$dir" -maxdepth 1 -name '*.tf' | wc -l)
    [ "$count" -eq 0 ] && continue
    name=$(echo "$dir" | sed 's|/$||')
    echo "--- [$name] terraform fmt -check ---"
    if terraform fmt -check -recursive "$dir" 2>/dev/null; then
      echo "  ✅ fmt pass"
    else
      echo "  ❌ fmt fail"
      FAIL=$((FAIL + 1))
    fi
  done
else
  echo "⚠️  Terraform CLI not installed — checking HCL syntax with Python"
  while IFS= read -r f; do
    if python3 -c "
import sys
with open('$f') as fh:
    c = fh.read()
if c.count('{') != c.count('}'):
    sys.exit(1)
" 2>&1; then
      echo "  ✅ $f: syntax OK"
    else
      echo "  ❌ $f: unbalanced braces"
      FAIL=$((FAIL + 1))
    fi
  done < <(find infrastructure/terraform -name '*.tf' -not -path '*/.*')
fi

# Security: check for hardcoded secrets
echo "--- Security: hardcoded secrets ---"
SECRET_ISSUES=0
while IFS= read -r file; do
  while IFS= read -r line; do
    echo "  ⚠️  Possible hardcoded password in $file: $line"
    SECRET_ISSUES=$((SECRET_ISSUES + 1))
  done < <(grep -n 'password\s*=\s*"' "$file" 2>/dev/null || true)
done < <(find infrastructure/terraform -name '*.tf' -not -path '*/.*')

if [ "$SECRET_ISSUES" -eq 0 ]; then
  echo "  ✅ No hardcoded passwords found"
else
  echo "  ❌ Found $SECRET_ISSUES possible hardcoded passwords"
  FAIL=$((FAIL + 1))
fi

echo "---"
echo "Terraform tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
