# Checkly Synthetic Monitoring Runbook

## Overview

Checkly monitors critical ArchTech Suite endpoints every 5 minutes from 3 global locations.

## Checks

### API Checks (every 5 min)

| Check              | Endpoint            | Expected              |
| ------------------ | ------------------- | --------------------- |
| Homepage Health    | `GET /`             | 200, < 3s             |
| API Health         | `GET /health`       | 200, status=ok        |
| Login Availability | `GET /login`        | 200, < 3s             |
| Auth Required      | `GET /api/v1/leads` | 401 (unauthenticated) |
| RabbitMQ Health    | RabbitMQ API        | 200, < 1s             |

### Browser Checks (every 10 min)

| Check        | URL             | Validates                |
| ------------ | --------------- | ------------------------ |
| Homepage E2E | `/`             | Title, lang attr, layout |
| Login Form   | `/login`        | Button visible           |
| 404 Page     | `/non-existent` | 404 response             |

## Adding a New Check

1. Create check file in `infrastructure/checkly/__checks__/`
2. Run locally: `npx checkly test`
3. Deploy: `npx checkly deploy`

## Interpreting Failures

### HTTP 5xx

- Check if the service is healthy via `/health`
- Check Pod status: `kubectl get pods -n arctech-prod`
- Check recent deployments in Argo CD

### Timeout (>3s)

- Check downstream dependencies (DB, Redis, AI provider)
- Check if network latency from Checkly locations is abnormal
- Review Grafana API latency dashboard

### Unexpected Status

- Check if recent deployment changed API behavior
- Review recent commits to affected endpoint

## Alerting

- Checkly alerts → PagerDuty (via webhook integration)
- Slack notification in `#archtech-alerts`
