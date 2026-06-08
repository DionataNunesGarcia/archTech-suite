RUN apt-get update && apt-get install -y --no-install-recommends \
    shellcheck \
    python3-pip \
    curl \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

RUN pip3 install --no-cache-dir --break-system-packages yamllint==1.35.1

RUN KUBECONFORM_VERSION=v0.6.7 \
    && curl -fsSL "https://github.com/yannh/kubeconform/releases/download/${KUBECONFORM_VERSION}/kubeconform-linux-amd64.tar.gz" \
    | tar -xz -C /usr/local/bin kubeconform \
    && chmod +x /usr/local/bin/kubeconform

RUN PROMTOOL_VERSION=2.53.0 \
    && curl -fsSL "https://github.com/prometheus/prometheus/releases/download/v${PROMTOOL_VERSION}/prometheus-${PROMTOOL_VERSION}.linux-amd64.tar.gz" \
    | tar -xz -C /tmp \
    && mv "/tmp/prometheus-${PROMTOOL_VERSION}.linux-amd64/promtool" /usr/local/bin/promtool \
    && chmod +x /usr/local/bin/promtool \
    && rm -rf "/tmp/prometheus-${PROMTOOL_VERSION}.linux-amd64"
