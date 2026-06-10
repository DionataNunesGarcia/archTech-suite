import { test, expect } from '@playwright/test';

test.describe('Home Page — Happy Path', () => {
	test('renders heading and dashboard cards', async ({ page }) => {
		await page.goto('/');
		await expect(page.getByRole('heading', { name: /archtech suite/i })).toBeVisible();
		await expect(page.getByText('Dashboard')).toBeVisible();
		await expect(page.getByText('Backend')).toBeVisible();
		await expect(page.getByText('Database')).toBeVisible();
	});

	test('has correct page title', async ({ page }) => {
		await page.goto('/');
		await expect(page).toHaveTitle(/ArchTech Suite/);
	});

	test('layout has Portuguese lang attribute', async ({ page }) => {
		await page.goto('/');
		await expect(page.locator('html')).toHaveAttribute('lang', 'pt-BR');
	});

	test('header shows Sistema Online badge', async ({ page }) => {
		await page.goto('/');
		await expect(page.getByText('Sistema Online')).toBeVisible();
	});

	test('squad table is visible', async ({ page }) => {
		await page.goto('/');
		await expect(page.getByText('Atendimento')).toBeVisible();
		await expect(page.getByText('Squads — Visão Geral')).toBeVisible();
	});

	test('quick action buttons are rendered', async ({ page }) => {
		await page.goto('/');
		await expect(page.getByText('Novo Lead')).toBeVisible();
		await expect(page.getByText('Nova Proposta')).toBeVisible();
	});
});

test.describe('Home Page — Error Scenarios', () => {
	test('shows 404 for unknown route', async ({ page }) => {
		await page.goto('/this-page-does-not-exist', { waitUntil: 'networkidle' });
		await expect(page.getByText(/This page could not be found/i)).toBeVisible();
	});

	test('renders header even when backend is unavailable', async ({ page }) => {
		await page.route('**/health', route => route.abort('connectionrefused'));
		await page.goto('/');
		await expect(page.getByText('ArchTech Suite')).toBeVisible();
	});
});
