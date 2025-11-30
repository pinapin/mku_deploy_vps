import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = 'http://localhost:8000';

export default function () {
    console.log('=== Testing CSRF Token Extraction ===');

    // Step 1: Get login page
    console.log('1. Getting login page...');
    const pageResponse = http.get(`${BASE_URL}/login`, {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
    });

    const pageSuccess = check(pageResponse, {
        'login page status is 200': (r) => r.status === 200,
        'login page contains testing mode': (r) => r.body.includes('MODE TESTING'),
        'login page contains csrf token': (r) => r.body.includes('csrf-token'),
    });

    if (!pageSuccess) {
        console.error('Failed to get login page');
        console.log('Response status:', pageResponse.status);
        console.log('Response body:', pageResponse.body.substring(0, 500));
        return;
    }

    // Step 2: Extract CSRF token
    console.log('2. Extracting CSRF token...');
    const csrfToken = extractCsrfToken(pageResponse);

    if (!csrfToken) {
        console.error('Failed to extract CSRF token');
        return;
    }

    console.log(`CSRF Token extracted: ${csrfToken.substring(0, 20)}...`);

    // Step 3: Test login with extracted CSRF token
    console.log('3. Testing login...');
    const loginPayload = JSON.stringify({
        'kode': '202111030', // Use first user from updated list
        'password': 'test123',
        '_token': csrfToken
    });

    const loginResponse = http.post(`${BASE_URL}/login`, loginPayload, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Referer': `${BASE_URL}/login`,
        },
    });

    const loginSuccess = check(loginResponse, {
        'login status is 200': (r) => r.status === 200,
        'login response is success': (r) => {
            if (r.status !== 200) return false;
            try {
                const body = JSON.parse(r.body);
                return body.success === true;
            } catch (e) {
                return false;
            }
        },
        'no CSRF token mismatch': (r) => {
            if (r.status !== 200) return false;
            try {
                const body = JSON.parse(r.body);
                return !body.message || !body.message.includes('CSRF');
            } catch (e) {
                return false;
            }
        },
    });

    if (loginSuccess) {
        console.log('✅ CSRF token test PASSED');
        const responseBody = JSON.parse(loginResponse.body);
        console.log('Login response:', responseBody);
    } else {
        console.log('❌ CSRF token test FAILED');
        console.log('Response status:', loginResponse.status);
        console.log('Response body:', loginResponse.body);
    }

    sleep(1);
}

function extractCsrfToken(response) {
    try {
        // Try to extract CSRF token from meta tag
        const html = response.body;
        const metaTagMatch = html.match(/<meta\s+name="csrf-token"\s+content="([^"]+)"\s*\/?>/i);
        if (metaTagMatch && metaTagMatch[1]) {
            return metaTagMatch[1];
        }

        // Try to extract from hidden input
        const hiddenInputMatch = html.match(/<input\s+type="hidden"\s+name="_token"\s+value="([^"]+)"\s*\/?>/i);
        if (hiddenInputMatch && hiddenInputMatch[1]) {
            return hiddenInputMatch[1];
        }

        console.warn('Could not extract CSRF token from response');
        return null;
    } catch (e) {
        console.error('Error extracting CSRF token:', e);
        return null;
    }
}

export let options = {
    vus: 1,
    iterations: 1,
};