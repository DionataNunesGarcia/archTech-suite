// Checkly configuration for ArchTech Suite
// Docs: https://www.checklyhq.com/docs/cli/

const { defineConfig } = require('@checkly/cli');

module.exports = defineConfig({
  projectName: 'ArchTech Suite',
  logicalId: 'archtech-suite',
  repoUrl: 'https://github.com/DionataNunesGarcia/drupal-recipes-base',
  checks: {
    activated: true,
    muted: false,
    runtimeId: '2025.02',
    frequency: 5,
    locations: ['us-east-1', 'sa-east-1', 'eu-west-1'],
    tags: ['production', 'critical'],
    checkMatch: '**/__checks__/**/*.check.ts',
    ignoreDirectoriesMatch: [],
    browserChecks: {
      frequency: 10,
      testMatch: '**/__checks__/**/*.spec.ts',
    },
  },
  cli: {
    runLocation: 'sa-east-1',
    reporters: ['list'],
  },
});
