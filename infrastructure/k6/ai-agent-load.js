import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

const circuitBreakerRate = new Rate('circuit_breaker_triggered');
const errorRate = new Rate('errors');

export const options = {
	stages: [
		{ duration: '30s', target: 50 },
		{ duration: '1m', target: 100 },
		{ duration: '2m', target: 100 },
		{ duration: '30s', target: 0 },
	],
	thresholds: {
		errors: ['rate<0.05'],
		circuit_breaker_triggered: ['rate<0.1'],
	},
};

const AGENTS = [
	{ name: 'lead-scorer', path: '/api/ai/crm/lead-scorer' },
	{ name: 'proposal-generator', path: '/api/ai/proposals/generator' },
	{ name: 'cashflow-predictor', path: '/api/ai/financeiro/cashflow' },
	{ name: 'knowledge-retriever', path: '/api/ai/library/retriever' },
];

const BASE_URL = __ENV.BASE_URL || 'http://archtech.ddev.site';

export default function () {
	const agent = AGENTS[Math.floor(Math.random() * AGENTS.length)];

	const payload = JSON.stringify({
		context: { userId: 'load-test-user', sessionId: `k6-${__VU}-${__ITER}` },
		data: { query: 'test load simulation' },
	});

	const params = {
		headers: { 'Content-Type': 'application/json' },
		tags: { name: agent.name },
	};

	const res = http.post(`${BASE_URL}${agent.path}`, payload, params);

	const isCircuitBreaker = res.status === 503 || res.status === 429;
	if (isCircuitBreaker) circuitBreakerRate.add(1);

	const ok = check(res, {
		'status is 200 or circuit breaker': r => r.status === 200 || isCircuitBreaker,
		'response time < 5000ms': r => r.timings.duration < 5000,
	});

	if (!ok) errorRate.add(1);

	sleep(0.5);
}
