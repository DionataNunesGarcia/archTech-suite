#!/bin/bash
set -euo pipefail

echo "=== UAT Validation ==="

cd "$(dirname "$0")/../.."

UAT_DIR="docs/uat"
ERRORS=0

# Check all required UAT files exist
for file in uat-guide.md feedback-form.yml approval-minutes.yml; do
	if [ -f "$UAT_DIR/$file" ]; then
		echo "✅ $file found"
	else
		echo "❌ $file missing"
		ERRORS=$((ERRORS + 1))
	fi
done

# Validate feedback form YAML
if python3 -c "import yaml; yaml.safe_load(open('$UAT_DIR/feedback-form.yml'))" 2>/dev/null; then
	echo "✅ feedback-form.yml is valid YAML"
else
	echo "❌ feedback-form.yml is invalid YAML"
	ERRORS=$((ERRORS + 1))
fi

# Validate approval minutes YAML
if python3 -c "import yaml; yaml.safe_load(open('$UAT_DIR/approval-minutes.yml'))" 2>/dev/null; then
	echo "✅ approval-minutes.yml is valid YAML"
else
	echo "❌ approval-minutes.yml is invalid YAML"
	ERRORS=$((ERRORS + 1))
fi

if [ $ERRORS -eq 0 ]; then
	echo "✅ All UAT artifacts validated"
else
	echo "❌ $ERRORS UAT artifact(s) missing or invalid"
	exit 1
fi
