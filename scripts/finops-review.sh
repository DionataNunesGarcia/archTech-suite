#!/bin/bash
set -euo pipefail

echo "=== FinOps Cost Review ==="
echo "Date: $(date +%Y-%m-%d)"

echo ""
echo "--- Cloud Infrastructure Costs ---"
if command -v aws &>/dev/null; then
	echo "Checking AWS costs for current month..."
	aws ce get-cost-and-usage \
		--time-period Start=$(date +%Y-%m-01),End=$(date +%Y-%m-%d) \
		--granularity MONTHLY \
		--metrics "BlendedCost" "UnblendedCost" \
		--group-by Type=DIMENSION,Key=SERVICE 2>/dev/null || {
		echo "⚠️  AWS Cost Explorer not available (check AWS credentials/config)"
	}
else
	echo "⚠️  AWS CLI not available. Skipping cloud cost check."
fi

echo ""
echo "--- AI Provider Costs (Estimated) ---"
AI_COST_FILE="infrastructure/finops/ai-cost-tracking.csv"
if [ -f "$AI_COST_FILE" ]; then
	echo "AI Cost tracking file found: $AI_COST_FILE"
	echo "Last 5 entries:"
	tail -5 "$AI_COST_FILE"
else
	echo "⚠️  No AI cost tracking file found. Creating template..."
	mkdir -p "$(dirname "$AI_COST_FILE")"
	cat > "$AI_COST_FILE" <<- CSVHEADER
date,squad,model,tokens_input,tokens_output,cost_usd
$(date +%Y-%m-%d),total,all,0,0,0.00
CSVHEADER
	echo "$AI_COST_FILE created with template header"
fi

echo ""
echo "--- Cost Optimization Suggestions ---"
cat << 'SUGGESTIONS'
1. Review underutilized Kubernetes nodes (right-size if CPU < 40%)
2. Consider GPT-4o-mini for high-volume/low-complexity AI tasks
3. Enable auto-scaling for non-critical services during off-hours
4. Review RDS instance sizing (IOPS vs actual usage)
5. Set budget alerts at 50%, 80%, and 100% of monthly forecast

Run this script weekly on Mondays to track cost trends.
SUGGESTIONS

echo ""
echo "✅ FinOps review completed"
