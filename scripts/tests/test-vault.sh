#!/bin/bash
# Test: Vault policy HCL syntax
set -u
FAIL=0

echo "=========================================="
echo " Vault Policy Tests"
echo "=========================================="

echo "--- Policy HCL validation ---"
while IFS= read -r policy; do
  if python3 -c "
import re, sys
with open('$policy') as f:
    content = f.read()
# Check for basic HCL structure
paths = re.findall(r'path\s+\"[^\"]+\"\s*\{', content)
if not paths:
    print('no path blocks found')
    sys.exit(1)
# Check balanced braces
opens = content.count('{')
closes = content.count('}')
if opens != closes:
    print(f'unbalanced braces ({opens} open, {closes} close)')
    sys.exit(1)
# Check capabilities exist
caps = re.findall(r'capabilities\s*=\s*\[([^\]]+)\]', content)
if not caps:
    print('no capabilities found')
    sys.exit(1)
print(f'{len(paths)} path(s), {len(caps)} capability block(s)')
" 2>&1; then
    echo "  ✅ $policy"
  else
    echo "  ❌ $policy"
    FAIL=$((FAIL + 1))
  fi
done < <(find infrastructure/vault/policies -name '*.hcl')

# Validate vault server config
echo "--- Vault server config ---"
if [ -f "infrastructure/vault/vault-config.hcl" ]; then
  if python3 -c "
import re, sys
with open('infrastructure/vault/vault-config.hcl') as f:
    content = f.read()
required = ['storage', 'listener']
for r in required:
    if re.search(r'^' + r + r'\s', content, re.MULTILINE):
        print(f'  ✅ has {r} block')
    else:
        print(f'  ❌ missing {r} block')
        sys.exit(1)
opens = content.count('{')
closes = content.count('}')
print(f'  braces: {opens} open, {closes} close {\"✅\" if opens==closes else \"❌\"}')
" 2>&1; then
    echo "  ✅ vault-config.hcl valid"
  else
    echo "  ❌ vault-config.hcl invalid"
    FAIL=$((FAIL + 1))
  fi
fi

# Verify rotation scripts exist
echo "--- Rotation scripts ---"
for script in infrastructure/vault/scripts/rotate-db-password.sh infrastructure/vault/scripts/rotate-redis-password.sh infrastructure/vault/scripts/setup-vault.sh; do
  if [ -f "$script" ] && [ -x "$script" ]; then
    echo "  ✅ $script (executable)"
  elif [ -f "$script" ]; then
    echo "  ⚠️  $script (exists, not executable)"
  else
    echo "  ❌ $script missing"
    FAIL=$((FAIL + 1))
  fi
done

echo "---"
echo "Vault tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
