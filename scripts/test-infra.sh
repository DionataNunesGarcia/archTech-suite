#!/bin/bash
# Master test runner for Phase 4 — Infrastructure, CI/CD, Security
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ALL_PASS=1
SKIPPED=0

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}=========================================="
echo " ArchTech Suite — Phase 4 Test Suite"
echo -e "==========================================${NC}"
echo "Date: $(date -u '+%Y-%m-%d %H:%M:%S UTC')"
echo "Root: $ROOT_DIR"
echo ""

export PATH="$HOME/.local/bin:$PATH"

TESTS=(
  "test-terraform.sh"
  "test-kubernetes.sh"
  "test-shellscripts.sh"
  "test-prometheus.sh"
  "test-docker-compose.sh"
  "test-grafana.sh"
  "test-ci-workflows.sh"
  "test-vault.sh"
  "test-rabbitmq.sh"
)

for test in "${TESTS[@]}"; do
  test_script="$SCRIPT_DIR/tests/$test"
  if [ ! -f "$test_script" ]; then
    echo -e "${YELLOW}⏭️  $test not found, skipping${NC}"
    SKIPPED=$((SKIPPED + 1))
    continue
  fi

  echo ""
  echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
  echo -e "${YELLOW} Running: $test${NC}"
  echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

  set +e
  bash "$test_script"
  EXIT_CODE=$?
  set -e

  if [ $EXIT_CODE -eq 0 ]; then
    echo -e "${GREEN}✅ $test PASSED${NC}"
  else
    echo -e "${RED}❌ $test FAILED (exit: $EXIT_CODE)${NC}"
    ALL_PASS=0
  fi
done

echo ""
echo -e "${YELLOW}=========================================="
echo " Summary"
echo -e "==========================================${NC}"
if [ $ALL_PASS -eq 1 ]; then
  echo -e "${GREEN}All tests passed!${NC}"
else
  echo -e "${RED}Some tests failed!${NC}"
fi

exit $(( ALL_PASS ^ 1 ))
