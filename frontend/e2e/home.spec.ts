import { test, expect } from '@playwright/test';

test.describe('Home Page — Happy Path', () => {
	test('renders heading and description', async ({ page }) => {
		await page.goto('/');
		await expect(page.getByRole('heading', { name: /archtech suite/i })).toBeVisible();
		await expect(page.getByText(/AI-powered platform for architecture firms/i)).toBeVisible();
	});

	test('has correct page title', async ({ page }) => {
		await page.goto('/');
		await expect(page).toHaveTitle(/ArchTech Suite/);
	});

	test('layout has Portuguese lang attribute', async ({ page }) => {
		await page.goto('/');
		await expect(page.locator('html')).toHaveAttribute('lang', 'pt-BR');
	});
});

test.describe('Home Page — Error Scenarios', () => {
	test('shows 404 for unknown route', async ({ page }) => {
		await page.goto('/this-page-does-not-exist', { waitUntil: 'networkidle' });
		// Next.js 15 static pages return 200 but render 404 content
		await expect(page.getByText(/This page could not be found/i)).toBeVisible();
	});

	test('handles server error gracefully', async ({ page }) => {
		await page.route('**/*', route => {
			if (route.request().url().includes('page-data')) {
				route.abort('connectionrefused');
			} else {
				route.continue();
			}
		});
		await page.goto('/');
		const body = page.locator('body');
		await expect(body).toBeVisible();
	});

	test('handles network failure gracefully', async ({ page }) => {
		await page.route('**/*', route => {
			if (route.request().resourceType() === 'document') {
				route.continue();
			} else {
				route.abort('connectionrefused');
			}
		});
		await page.goto('/');
		await expect(page.getByRole('heading', { name: /archtech suite/i })).toBeVisible();
	});
});
