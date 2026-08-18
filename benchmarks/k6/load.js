// k6 ramping load test for KnowVerse read endpoints.
//
// Install k6:  https://k6.io/docs/get-started/installation/
// Run:
//   k6 run -e BASE=http://127.0.0.1:8000 -e POST_ID=1 benchmarks/k6/load.js
//
// Reports p50/p90/p95/p99 latency, throughput, and error rate, ramping
// virtual users 10 → 50 → 100 → 200. Per-endpoint breakdown is tagged via `name`.

import http from 'k6/http';
import { check } from 'k6';

const BASE = __ENV.BASE || 'http://127.0.0.1:8000';
const POST_ID = __ENV.POST_ID || '1';

export const options = {
  scenarios: {
    ramp: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '30s', target: 10 },
        { duration: '30s', target: 50 },
        { duration: '30s', target: 100 },
        { duration: '30s', target: 200 },
        { duration: '20s', target: 0 },
      ],
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.05'],
    'http_req_duration{name:home}': ['p(95)<800'],
    'http_req_duration{name:posts}': ['p(95)<800'],
    'http_req_duration{name:show}': ['p(95)<800'],
    'http_req_duration{name:search}': ['p(95)<1000'],
  },
  summaryTrendStats: ['avg', 'min', 'med', 'p(95)', 'p(99)', 'max'],
};

const endpoints = [
  () => http.get(`${BASE}/`, { tags: { name: 'home' } }),
  () => http.get(`${BASE}/posts`, { tags: { name: 'posts' } }),
  () => http.get(`${BASE}/posts/${POST_ID}`, { tags: { name: 'show' } }),
  () => http.get(`${BASE}/search?q=benchmark`, { tags: { name: 'search' } }),
];

export default function () {
  const res = endpoints[Math.floor(Math.random() * endpoints.length)]();
  check(res, { 'status is 200': (r) => r.status === 200 });
}
