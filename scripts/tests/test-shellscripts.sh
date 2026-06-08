#!/bin/bash
# Test: Shell scripts with shellcheck
set -u
FAIL=0

echo "=========================================="
echo " Shell Script Tests (shellcheck)"
echo "=========================================="

SHELLCHECK=$(command -v shellcheck || echo "$HOME/.local/bin/shellcheck")
if [ ! -x "$SHELLCHECK" ]; then
  echo "⚠️  shellcheck not installed, skipping"
  echo "  Install: curl -sL \"https://github.com/koalaman/shellcheck/releases/download/v0.10.0/shellcheck-v0.10.0.linux.x86_64.tar.xz\" | tar xJ -C /tmp/ && cp /tmp/shellcheck-v0.10.0/shellcheck ~/.local/bin/"
  exit 0
fi

while IFS= read -r script; do
  echo "  → $script"
  output=$("$SHELLCHECK" -x -S error "$script" 2>&1) && status=0 || status=$?
  if [ $status -eq 0 ]; then
    echo "    ✅ OK"
  else
    echo "    ⚠️  Issues found (warning+error level)"
    echo "$output" | grep -E '(SC[0-9]{4}|error|warning)' | head -5
    FAIL=$((FAIL + 1))
  fi
done < <(find . -name '*.sh' -not -path './vendor/*' -not -path './web/*' | sort)

echo "---"
echo "Shellcheck tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
