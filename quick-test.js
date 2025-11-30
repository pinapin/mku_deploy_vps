// Quick test untuk memverifikasi login testing berfungsi
import http from 'k6/http';

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    console.log('=== Quick Test Login Form ===');

    // Test 1: Akses halaman login
    console.log('1. Testing login page access...');
    const loginPage = http.get(`${BASE_URL}/login`, {
        headers: {
            'Accept': 'text/html',
        },
    });

    console.log(`Login page status: ${loginPage.status}`);

    if (loginPage.status !== 200) {
        console.log('❌ Login page failed');
        console.log(loginPage.body);
        return;
    }

    // Test 2: Verifikasi testing mode
    const isTestingMode = loginPage.body.includes('MODE TESTING');
    console.log(`Testing mode enabled: ${isTestingMode}`);

    if (!isTestingMode) {
        console.log('❌ Testing mode not detected');
        console.log('Check LOAD_TESTING_ENABLED in .env');
        return;
    }

    // Test 3: Ekstrak CSRF token
    const csrfMatch = loginPage.body.match(/<meta\s+name="csrf-token"\s+content="([^"]+)"\s*\/?>/i);
    const csrfToken = csrfMatch ? csrfMatch[1] : null;

    console.log(`CSRF Token found: ${csrfToken ? 'Yes' : 'No'}`);

    if (!csrfToken) {
        console.log('❌ CSRF Token not found');
        return;
    }

    console.log(`CSRF Token: ${csrfToken.substring(0, 20)}...`);

    // Test 4: Coba login
    console.log('2. Testing login...');
    const loginResponse = http.post(`${BASE_URL}/login`, JSON.stringify({
        kode: '202111030', // Use first user from updated list
        password: 'test123',
        _token: csrfToken
    }), {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    console.log(`Login status: ${loginResponse.status}`);
    console.log(`Login response: ${loginResponse.body}`);

    try {
        const loginData = JSON.parse(loginResponse.body);
        if (loginData.success) {
            console.log('✅ Login successful!');
            console.log(`Redirect to: ${loginData.redirect}`);
        } else {
            console.log('❌ Login failed');
            console.log(`Error: ${loginData.message}`);
        }
    } catch (e) {
        console.log('❌ Invalid JSON response');
        console.log(loginResponse.body);
    }
}

export let options = {
    vus: 1,
    iterations: 1,
};