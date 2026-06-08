import { BrowserCheck } from '@checkly/cli/constructs';
import { test, expect } from '@playwright/test';

new BrowserCheck('homepage-browser-check', {
  name: 'Homepage — E2E Browser Check',
  activated: true,
  frequency: 10,
  locations: ['us-east-1', 'sa-east-1', 'eu-west-1'],
  code: {
    entrypoint: './homepage.spec.ts',
  },
  tags: ['production', 'critical', 'e2e'],
});

test('homepage loads correctly', async ({ page }) => {
  const response = await page.goto('https://archtech.com.br', {
    waitUntil: 'networkidle',
  });
  expect(response?.status()).toBe(200);
  await expect(page).toHaveTitle(/ArchTech Suite/);
  await expect(page.locator('html')).toHaveAttribute('lang', 'pt-BR');
});

test('login page has form', async ({ page }) => {
  await page.goto('https://archtech.com.br/login');
  await expect(page.getByRole('button', { name: /sign in|entrar/i })).toBeVisible();
});

test('404 page renders', async ({ page }) => {
  const response = await page.goto('https://archtech.com.br/non-existent-page', {
    waitUntil: 'networkidle',
  });
  expect(response?.status()).toBe(404);
});
