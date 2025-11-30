import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

// Custom metrics for measuring specific operations
const loginPageLoadRate = new Rate('login_page_load_rate');
const loginSubmitRate = new Rate('login_submit_rate');

// Test configuration options - focus on login page performance
export let options = {
    stages: [
        { duration: '30s', target: 100 },   // Ramp up to 50 users in 30s
        // { duration: '1m', target: 100 },   // Ramp up to 100 users in 1m
        // { duration: '2m', target: 200 },   // Ramp up to 200 users in 2m
        // { duration: '2m', target: 200 },   // Stay at 200 users for 2m
        // { duration: '1m', target: 0 },     // Ramp down to 0 users in 1m
    ],
    thresholds: {
        http_req_duration: ['p(95)<3000'], // 95% of requests must complete below 3s
        http_req_failed: ['rate<0.05'],    // Error rate must be less than 5%
        login_page_load_rate: ['rate>0.98'], // 98% of page loads must succeed
        login_submit_rate: ['rate>0.95'],   // 95% of login attempts must succeed
    },
};

// Base URL configuration
const BASE_URL = 'http://localhost:8000';

// User data pool for simulation
const USERS = [
    { nim: '202111030', password: 'test123' },
    { nim: '202211156', password: 'test123' },
    { nim: '202211600', password: 'test123' },
    { nim: '202311134', password: 'test123' },
    { nim: '202311139', password: 'test123' },
    { nim: '202311141', password: 'test123' },
    { nim: '202311145', password: 'test123' },
    { nim: '202311146', password: 'test123' },
    { nim: '202311229', password: 'test123' },
    { nim: '202311258', password: 'test123' },
    { nim: '202311298', password: 'test123' },
    { nim: '202311302', password: 'test123' },
    { nim: '202311306', password: 'test123' },
    { nim: '202311307', password: 'test123' },
    { nim: '202311308', password: 'test123' },
    { nim: '202311358', password: 'test123' },
    { nim: '202311366', password: 'test123' },
    { nim: '202311372', password: 'test123' },
    { nim: '202311421', password: 'test123' },
    { nim: '202311430', password: 'test123' },
    { nim: '202311441', password: 'test123' },
    { nim: '202311449', password: 'test123' },
    { nim: '202311457', password: 'test123' },
    { nim: '202311462', password: 'test123' },
    { nim: '202311496', password: 'test123' },
    { nim: '202320040', password: 'test123' },
    { nim: '202320053', password: 'test123' },
    { nim: '202320066', password: 'test123' },
    { nim: '202320067', password: 'test123' },
    { nim: '202320095', password: 'test123' },
    { nim: '202320096', password: 'test123' },
    { nim: '202320106', password: 'test123' },
    { nim: '202320122', password: 'test123' },
    { nim: '202320150', password: 'test123' },
    { nim: '202320151', password: 'test123' },
    { nim: '202333118', password: 'test123' },
    { nim: '202333139', password: 'test123' },
    { nim: '202351029', password: 'test123' },
    { nim: '202351205', password: 'test123' },
    { nim: '202354057', password: 'test123' },
    { nim: '202360053', password: 'test123' },
    { nim: '202360066', password: 'test123' },
    { nim: '202011265', password: 'test123' },
    { nim: '202011280', password: 'test123' },
    { nim: '202111476', password: 'test123' },
    { nim: '202111507', password: 'test123' },
    { nim: '202111629', password: 'test123' },
    { nim: '202211038', password: 'test123' },
    { nim: '202211205', password: 'test123' },
    { nim: '202211233', password: 'test123' },
    { nim: '202211518', password: 'test123' },
    { nim: '202211532', password: 'test123' },
    { nim: '202211552', password: 'test123' },
    { nim: '202211563', password: 'test123' },
    { nim: '202211588', password: 'test123' },
    { nim: '202311003', password: 'test123' },
    { nim: '202311011', password: 'test123' },
    { nim: '202311020', password: 'test123' },
    { nim: '202311025', password: 'test123' },
    { nim: '202311036', password: 'test123' },
    { nim: '202311038', password: 'test123' },
    { nim: '202311057', password: 'test123' },
    { nim: '202311063', password: 'test123' },
    { nim: '202311072', password: 'test123' },
    { nim: '202311085', password: 'test123' },
    { nim: '202311090', password: 'test123' },
    { nim: '202311101', password: 'test123' },
    { nim: '202311132', password: 'test123' },
    { nim: '202311136', password: 'test123' },
    { nim: '202311228', password: 'test123' },
    { nim: '202311233', password: 'test123' },
    { nim: '202311240', password: 'test123' },
    { nim: '202311250', password: 'test123' },
    { nim: '202311252', password: 'test123' },
    { nim: '202311253', password: 'test123' },
    { nim: '202311269', password: 'test123' },
    { nim: '202311296', password: 'test123' },
    { nim: '202311351', password: 'test123' },
    { nim: '202311352', password: 'test123' },
    { nim: '202311354', password: 'test123' },
    { nim: '202311363', password: 'test123' },
    { nim: '202311367', password: 'test123' },
    { nim: '202311380', password: 'test123' },
    { nim: '202311394', password: 'test123' },
    { nim: '202311408', password: 'test123' },
    { nim: '202311420', password: 'test123' },
    { nim: '202311425', password: 'test123' },
    { nim: '202311431', password: 'test123' },
    { nim: '202311433', password: 'test123' },
    { nim: '202311447', password: 'test123' },
    { nim: '202311459', password: 'test123' },
    { nim: '202311489', password: 'test123' },
    { nim: '202311499', password: 'test123' },
    { nim: '202311503', password: 'test123' },
    { nim: '202311512', password: 'test123' },
    { nim: '202311519', password: 'test123' },
    { nim: '202311529', password: 'test123' },
    { nim: '202311533', password: 'test123' },
    { nim: '202311544', password: 'test123' },
];

export function setup() {
    console.log('=== Login Page Load Test Configuration ===');
    console.log(`Base URL: ${BASE_URL}`);
    console.log(`Target Users: 200`);
    console.log(`Test Duration: ~6.5 minutes`);
    console.log('Focus: Login page load and submission performance');
    console.log('==========================================');
    console.log('Setup completed. Starting login page load test...');
}

export default function () {
    const user = USERS[__VU % USERS.length];
    const userId = __VU;

    try {
        // Step 1: Load login page (main focus of this test)
        loadLoginPage();

        // Step 2: Optional: Submit login form (to test backend performance)
        // This helps identify if backend authentication becomes bottleneck
        if (Math.random() > 0.3) { // 70% chance to attempt login
            submitLoginForm(user);
        }

        // Random delay between requests
        sleep(Math.random() * 3 + 1);

    } catch (error) {
        console.error(`User ${userId} error: ${error.message}`);
        sleep(5); // Wait before retry
    }
}

function loadLoginPage() {
    const startTime = new Date().getTime();

    const response = http.get(`${BASE_URL}/login`, {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5',
            'Accept-Encoding': 'gzip, deflate, br',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
            'Sec-Fetch-Dest': 'document',
            'Sec-Fetch-Mode': 'navigate',
            'Sec-Fetch-Site': 'none',
            'Cache-Control': 'max-age=0',
        },
        timeout: '10s',
    });

    const loadTime = new Date().getTime() - startTime;

    const success = check(response, {
        'login page status is 200': (r) => r.status === 200,
        'login page contains form': (r) => r.body.includes('<form'),
        'login page contains CSRF token': (r) => r.body.includes('csrf-token'),
        'login page load time < 3s': () => loadTime < 3000,
        'login page size > 0': (r) => r.body.length > 0,
    });

    loginPageLoadRate.add(success);

    if (success) {
        console.log(`Login page loaded in ${loadTime}ms`);
    } else {
        console.error(`Failed to load login page: Status ${response.status}, Size: ${response.body.length}`);
    }
}

function submitLoginForm(user) {
    // First, get fresh login page to extract CSRF token
    const pageResponse = http.get(`${BASE_URL}/login`);
    const csrfToken = extractCsrfToken(pageResponse);

    if (!csrfToken) {
        console.error(`Failed to extract CSRF token for user ${user.nim}`);
        return;
    }

    const loginPayload = JSON.stringify({
        'kode': user.nim,
        'password': user.password,
        '_token': csrfToken
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Referer': `${BASE_URL}/login`,
            'Origin': BASE_URL,
        },
    };

    const response = http.post(`${BASE_URL}/login`, loginPayload, params);

    const success = check(response, {
        'login status is 200': (r) => r.status === 200,
        'login response is JSON': (r) => {
            try {
                JSON.parse(r.body);
                return true;
            } catch (e) {
                return false;
            }
        },
        'login response has success field': (r) => {
            try {
                const body = JSON.parse(r.body);
                return body.hasOwnProperty('success');
            } catch (e) {
                return false;
            }
        },
    });

    loginSubmitRate.add(success);

    if (success) {
        try {
            const body = JSON.parse(response.body);
            console.log(`Login submitted for user ${user.nim}: ${body.success ? 'Success' : 'Failed'}`);
        } catch (e) {
            console.log(`Login submitted for user ${user.nim}: Invalid JSON response`);
        }
    } else {
        console.error(`Login failed for user ${user.nim}: Status ${response.status}`);
    }
}

function extractCsrfToken(response) {
    try {
        const html = response.body;

        // Try to extract CSRF token from meta tag
        const metaTagMatch = html.match(/<meta\s+name="csrf-token"\s+content="([^"]+)"\s*\/?>/i);
        if (metaTagMatch && metaTagMatch[1]) {
            return metaTagMatch[1];
        }

        // Try to extract from hidden input
        const hiddenInputMatch = html.match(/<input\s+type="hidden"\s+name="_token"\s+value="([^"]+)"\s*\/?>/i);
        if (hiddenInputMatch && hiddenInputMatch[1]) {
            return hiddenInputMatch[1];
        }

        // Try to extract from JSON (if page contains JSON configuration)
        const jsonMatch = html.match(/"csrf_token"\s*:\s*"([^"]+)"/i);
        if (jsonMatch && jsonMatch[1]) {
            return jsonMatch[1];
        }

        console.warn('Could not extract CSRF token from login page');
        return null;
    } catch (e) {
        console.error('Error extracting CSRF token:', e);
        return null;
    }
}

export function teardown() {
    console.log('=== Login Page Load Test Completed ===');
    console.log('Test finished. Check results for login page performance metrics.');
    console.log('Focus areas:');
    console.log('- Page load time under normal and peak load');
    console.log('- Server response stability');
    console.log('- Authentication endpoint performance');
    console.log('==========================================');
}