# Load Testing Guide

## Overview

Load tests validate SLOs under simulated traffic using k6.

## Scripts

| Script | Purpose | Target |
|--------|---------|--------|
| `infrastructure/k6/load-test.js` | General ramp-up to 10K concurrent users | Frontend (static + SSR) |
| `infrastructure/k6/ai-agent-load.js` | 100 parallel requests per AI agent | AI API endpoints |

## Running Locally

```bash
# Install k6
# macOS: brew install k6
# Linux: https://k6.io/docs/getting-started/installation/

# General load test
k6 run infrastructure/k6/load-test.js

# AI agent load test
k6 run infrastructure/k6/ai-agent-load.js

# With custom base URL
BASE_URL=https://archtech.ddev.site k6 run infrastructure/k6/load-test.js
```

## SLOs

| Metric | Threshold |
|--------|-----------|
| Latency p95 | < 200ms |
| Latency p99 | < 500ms |
| Error rate | < 1% |
| Circuit breaker rate | < 10% |

## Load Profile

```
10k  |                    ████████████
 5k  |          ██████████▒▒▒▒▒▒▒▒▒▒▒▒
 1k  |   ██████▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
  0  └───┬────┬────┬────┬────┬────┬──
      2m   5m   10m  15m  17m  19m
```

Ramp-up: 2m → 1K, 3m → 5K, 5m → 10K, hold 5m, ramp-down 2m.
