import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Accessibility (WCAG AA)', () => {
	test('home page has zero WCAG AA violations', async ({ page }) => {
		await page.goto('/');
		const results = await new AxeBuilder({ page })
			.withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
			.analyze();
		expect(results.violations).toEqual([]);
	});

	test('404 page has no critical violations', async ({ page }) => {
		await page.goto('/non-existent', { waitUntil: 'networkidle' });
		const results = await new AxeBuilder({ page })
			.withTags(['wcag2a', 'wcag2aa'])
			.analyze();
		const critical = results.violations.filter(v => v.impact === 'critical');
		expect(critical).toEqual([]);
	});
});
