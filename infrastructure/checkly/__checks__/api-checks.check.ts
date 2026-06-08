import { ApiCheck, AssertionBuilder } from '@checkly/cli/constructs';

new ApiCheck('homepage-health', {
  name: 'Homepage — HTTP 200',
  activated: true,
  frequency: 5,
  locations: ['us-east-1', 'sa-east-1', 'eu-west-1'],
  request: {
    url: 'https://archtech.com.br',
    method: 'GET',
    followRedirects: true,
    skipSSL: false,
    assertions: [
      AssertionBuilder.statusCode().equals(200),
      AssertionBuilder.responseTime().lessThan(3000),
    ],
  },
  tags: ['production', 'critical', 'frontend'],
});

new ApiCheck('api-health', {
  name: 'API Health Check',
  activated: true,
  frequency: 5,
  locations: ['us-east-1', 'sa-east-1', 'eu-west-1'],
  request: {
    url: 'https://api.archtech.com.br/health',
    method: 'GET',
    followRedirects: false,
    skipSSL: false,
    assertions: [
      AssertionBuilder.statusCode().equals(200),
      AssertionBuilder.jsonBody('$.status').equals('ok'),
      AssertionBuilder.responseTime().lessThan(2000),
    ],
  },
  tags: ['production', 'critical', 'backend'],
});

new ApiCheck('login-flow', {
  name: 'Login Page Availability',
  activated: true,
  frequency: 5,
  locations: ['us-east-1', 'sa-east-1', 'eu-west-1'],
  request: {
    url: 'https://archtech.com.br/login',
    method: 'GET',
    followRedirects: false,
    skipSSL: false,
    assertions: [
      AssertionBuilder.statusCode().equals(200),
      AssertionBuilder.responseTime().lessThan(3000),
    ],
  },
  tags: ['production', 'critical', 'auth'],
});

new ApiCheck('api-leads-endpoint', {
  name: 'API Leads — Auth Required',
  activated: true,
  frequency: 5,
  locations: ['us-east-1', 'sa-east-1', 'eu-west-1'],
  request: {
    url: 'https://api.archtech.com.br/api/v1/leads',
    method: 'GET',
    followRedirects: false,
    skipSSL: false,
    assertions: [
      AssertionBuilder.statusCode().equals(401),
      AssertionBuilder.responseTime().lessThan(2000),
    ],
  },
  tags: ['production', 'critical', 'api', 'crm'],
});

new ApiCheck('rabbitmq-health', {
  name: 'RabbitMQ Health',
  activated: true,
  frequency: 10,
  locations: ['us-east-1', 'sa-east-1'],
  request: {
    url: 'https://rabbitmq.archtech.com.br/api/health/checks/alarms',
    method: 'GET',
    followRedirects: false,
    skipSSL: true,
    assertions: [
      AssertionBuilder.statusCode().equals(200),
      AssertionBuilder.responseTime().lessThan(1000),
    ],
  },
  tags: ['production', 'infrastructure', 'rabbitmq'],
});
