#!/bin/bash
set -euo pipefail

echo "=== OWASP ZAP Baseline Scan ==="

if ! command -v zap-cli &>/dev/null; then
	echo "⚠️  zap-cli not installed. Skipping ZAP scan."
	echo "Install with: pip install zap-cli"
	echo "Or run via Docker:"
	echo "  docker run -v \$(pwd):/zap/wrk -t ghcr.io/zaproxy/zaproxy \\"
	echo "    zap-baseline.py -t http://localhost:3000"
	exit 0
fi

TARGET="${1:-http://localhost:3000}"
REPORT_DIR="docs/security/zap-reports"
mkdir -p "$REPORT_DIR"
REPORT="$REPORT_DIR/zap-baseline-$(date +%Y%m%d-%H%M%S).html"

zap-cli start
zap-cli open-url "$TARGET"
zap-cli spider "$TARGET"
zap-cli active-scan "$TARGET"
zap-cli report -o "$REPORT" -f html
zap-cli shutdown

echo "ZAP report saved to $REPORT"
echo "✅ OWASP ZAP scan completed"
