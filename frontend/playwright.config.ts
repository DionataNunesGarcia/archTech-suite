import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
	testDir: './e2e',
	fullyParallel: false,
	retries: 1,
	workers: 1,
	reporter: [['html', { outputFolder: 'playwright-report' }]],
	use: {
		baseURL: 'http://localhost:3000',
		trace: 'on-first-retry',
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices['Desktop Chrome'] },
		},
	],
	webServer: {
		command: 'npm run start',
		url: 'http://localhost:3000',
		reuseExistingServer: true,
		timeout: 30000,
	},
});
