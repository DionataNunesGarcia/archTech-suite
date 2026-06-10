const config = {
	testEnvironment: 'jsdom',
	transform: {
		'^.+\\.(ts|tsx)$': ['@swc/jest', { jsc: { transform: { react: { runtime: 'automatic' } } } }],
	},
	moduleNameMapper: {
		'^@/(.*)$': '<rootDir>/src/$1',
		'\\.(css|less|scss)$': '<rootDir>/__mocks__/styleMock.js',
	},
	testPathIgnorePatterns: ['<rootDir>/e2e/', '<rootDir>/node_modules/'],
	setupFilesAfterEnv: ['<rootDir>/jest.setup.js'],
	collectCoverageFrom: ['src/**/*.{ts,tsx}', '!src/**/*.d.ts', '!src/app/layout.tsx'],
	coverageThreshold: {
		global: {
			statements: 80,
			branches: 70,
			functions: 80,
			lines: 80,
		},
	},
};

module.exports = config;
