import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

// Custom metrics
let errorRate = new Rate('errors');

export let options = {
    stages: [
        { duration: '30s', target: 50 },
        { duration: '30s', target: 200 },
        { duration: '30s', target: 500 },
        { duration: '30s', target: 800 },
        { duration: '1m', target: 1000 },
        { duration: '30s', target: 500 },
        { duration: '30s', target: 100 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<3000'],
        http_req_failed: ['rate<0.1'],
        errors: ['rate<0.1'],
    },
};

const BASE_URL = __ENV.API_BASE_URL || 'http://localhost/api';

export default function () {
    let responses = http.batch([
        ['GET', `${BASE_URL}/ujian/health`],
        ['GET', `${BASE_URL}/ujian/stats`],
    ]);

    responses.forEach((response, index) => {
        let endpoint = index === 0 ? 'health' : 'stats';
        let success = check(response, {
            [`${endpoint} status is 200`]: (r) => r.status === 200,
            [`${endpoint} response time < 3000ms`]: (r) => r.timings.duration < 3000,
        });

        errorRate.add(!success);
    });

    // Get exam list
    let examResponse = http.get(`${BASE_URL}/ujian`, {
        timeout: '10s',
    });

    let examSuccess = check(examResponse, {
        'exam list status is 200': (r) => r.status === 200,
        'exam list response time < 3000ms': (r) => r.timings.duration < 3000,
    });

    errorRate.add(!examSuccess);

    // If exam list successful, get questions from first exam
    if (examSuccess && examResponse.status === 200) {
        try {
            let data = JSON.parse(examResponse.body);
            if (data.success && data.data && data.data.length > 0) {
                let examId = data.data[0].id;
                let questionsResponse = http.get(`${BASE_URL}/ujian/${examId}/questions`, {
                    timeout: '10s',
                });

                let questionsSuccess = check(questionsResponse, {
                    'questions status is 200': (r) => r.status === 200,
                    'questions response time < 3000ms': (r) => r.timings.duration < 3000,
                });

                errorRate.add(!questionsSuccess);
            }
        } catch (e) {
            errorRate.add(true);
        }
    }

    sleep(0.1);
}

export function handleSummary(data) {
    return {
        'api-test-summary.json': JSON.stringify(data, null, 2),
    };
}