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
		const response = await page.goto('/this-page-does-not-exist', { waitUntil: 'networkidle' });
		expect(response?.status()).toBe(404);
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

	test('handles slow network', async ({ page }) => {
		await page.context().addInitScript(() => {
			window.fetch = new Proxy(window.fetch, {
				apply(target, thisArg, args) {
					return new Promise((_, reject) =>
						setTimeout(() => reject(new Error('Network timeout')), 5000)
					);
				},
			});
		});
		await page.goto('/');
		await expect(page.getByRole('heading', { name: /archtech suite/i })).toBeVisible();
	});
});
