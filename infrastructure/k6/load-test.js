import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

const errorRate = new Rate('errors');
const latencyP95 = new Trend('latency_p95');
const latencyP99 = new Trend('latency_p99');

export const options = {
	stages: [
		{ duration: '2m', target: 1000 },
		{ duration: '3m', target: 5000 },
		{ duration: '5m', target: 10000 },
		{ duration: '5m', target: 10000 },
		{ duration: '2m', target: 0 },
	],
	thresholds: {
		http_req_duration: ['p(95)<200', 'p(99)<500'],
		errors: ['rate<0.01'],
	},
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost:3000';

export default function () {
	const responses = http.batch([
		['GET', `${BASE_URL}/`, null, { tags: { name: 'home' } }],
		['GET', `${BASE_URL}/_next/static/chunks/pages/home.js`, null, { tags: { name: 'static' } }],
	]);

	responses.forEach(res => {
		const ok = check(res, {
			'status is 200': r => r.status === 200,
			'response time < 500ms': r => r.timings.duration < 500,
		});
		if (!ok) errorRate.add(1);
		latencyP95.add(res.timings.duration);
		latencyP99.add(res.timings.duration);
	});

	sleep(1);
}
