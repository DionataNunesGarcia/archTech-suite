# E2E Testing Guide

## Overview

E2E tests validate critical user flows using Playwright (Chromium).

## Test Structure

```
frontend/e2e/
├── home.spec.ts    # Home page: happy path + error scenarios
└── a11y.spec.ts    # Accessibility (WCAG AA) audits
```

## Running Locally

```bash
# Install Playwright browsers
npx playwright install --with-deps chromium

# Run all E2E tests
cd frontend && npx playwright test

# Run with UI mode
cd frontend && npx playwright test --ui

# Run specific test file
cd frontend && npx playwright test e2e/home.spec.ts
```

## CI Integration

E2E tests run as part of the `test.yml` workflow. A dedicated `e2e` job:
1. Starts DDEV (or Next.js standalone)
2. Runs Playwright against `localhost:3000`
3. Publishes HTML report as artifact

## Test Coverage per Flow

| Flow       | Happy Path | Error 1 | Error 2 |
|------------|------------|---------|---------|
| Home Page  | Heading + description visible | 404 route | Network failure |
| Layout     | PT-BR lang attribute | Slow network | Server error |
| A11y       | Zero WCAG AA violations | 404 page no critical issues | — |
