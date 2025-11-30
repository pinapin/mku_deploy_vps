import http from "k6/http";
import { check, sleep } from "k6";
import { Rate } from "k6/metrics";

let errorRate = new Rate("errors");

export let options = {
    stages: [
        { duration: "5s", target: 20 }, // Warm up
        { duration: "30s", target: 1000 }, // Naik ke 300 users dalam 10 detik
        { duration: "30s", target: 1000 }, // Pertahan3an 500 users selama 10 detik
        { duration: "5s", target: 0 }, // Cool down
    ],
    thresholds: {
        http_req_duration: ["p(95)<3000"], // 95% request < 3 detik
        http_req_failed: ["rate<0.1"], // Error rate < 10%
        errors: ["rate<0.1"],
    },
};

export default function () {
    let url = "http://34.128.94.103/api/ujian/random/question";

    let params = {
        headers: {
            Accept: "application/json",
            "User-Agent": "k6-test",
        },
    };

    let response = http.get(url, params);

    let success = check(response, {
        "status is 200": (r) => r.status === 200,
        "response time < 3000ms": (r) => r.timings.duration < 3000,
    });

    errorRate.add(!success);

    sleep(1); // Small delay between requests
}
