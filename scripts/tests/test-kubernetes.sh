#!/bin/bash
# Test: Kubernetes manifests against K8s schema
set -u
FAIL=0
KUBE_VERSION="${KUBE_VERSION:-1.30.0}"

echo "=========================================="
echo " Kubernetes Manifest Tests"
echo "=========================================="

KUBECONFORM=$(command -v kubeconform || echo "$HOME/.local/bin/kubeconform")
if [ ! -x "$KUBECONFORM" ]; then
  echo "❌ kubeconform not found — install with:"
  echo "  wget -qO- https://github.com/yannh/kubeconform/releases/download/v0.6.7/kubeconform-linux-amd64.tar.gz | tar xz -C /tmp/ && cp /tmp/kubeconform ~/.local/bin/"
  exit 1
fi

DIRS=(
  "infrastructure/kubernetes/namespaces"
  "infrastructure/kubernetes/rbac"
  "infrastructure/kubernetes/network-policies"
  "infrastructure/kubernetes/service-account.yaml"
)

echo "--- Validating against Kubernetes $KUBE_VERSION ---"
for target in "${DIRS[@]}"; do
  [ ! -e "$target" ] && echo "  ⏭️  $target not found, skipping" && continue
  echo "  → $target"
  if [ -f "$target" ]; then
    "$KUBECONFORM" -kubernetes-version "$KUBE_VERSION" -strict "$target" 2>&1 || FAIL=$((FAIL + 1))
  else
    "$KUBECONFORM" -kubernetes-version "$KUBE_VERSION" -strict "$target"/*.yaml 2>&1 || FAIL=$((FAIL + 1))
  fi
done

echo "---"
echo "K8s tests: $([ "$FAIL" -eq 0 ] && echo 'ALL PASSED' || echo "FAILURES: $FAIL")"
exit $FAIL
