import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

// Custom metrics for measuring specific operations
const answerSubmitRate = new Rate('answer_submit_rate');
const examStartRate = new Rate('exam_start_rate');
const examFinishRate = new Rate('exam_finish_rate');

// Test configuration options
export let options = {
    stages: [
        { duration: '2m', target: 100 },  // Ramp up to 100 users in 2 minutes
        // { duration: '5m', target: 300 },  // Ramp up to 300 users in 5 minutes
        // { duration: '10m', target: 1000 }, // Ramp up to 1000 users in 10 minutes
        // { duration: '10m', target: 1000 }, // Stay at 1000 users for 10 minutes
        // { duration: '5m', target: 0 },     // Ramp down to 0 users in 5 minutes
    ],
    thresholds: {
        http_req_duration: ['p(95)<5000'], // 95% of requests must complete below 5s
        http_req_failed: ['rate<0.1'],     // Error rate must be less than 10%
        answer_submit_rate: ['rate>0.95'], // 95% of answer submissions must succeed
        exam_start_rate: ['rate>0.95'],     // 95% of exam starts must succeed
        exam_finish_rate: ['rate>0.95'],    // 95% of exam finishes must succeed
    },
};

// Base URL configuration
const BASE_URL = 'https://upt-mku.umk.ac.id/uji'; // Adjust to your Laravel app URL

// User data pool for simulation (sesuai config/testing.php yang sudah diupdate user list)
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

// Exam data for testing - sesuai dengan data di ujian.sql
const EXAM_DATA = {
    examId: 2, // Pretest Kewirausahaan Industri (ID: 2) dengan 30 soal
    questionCount: 30, // Jumlah soal sesuai database (soal ID 20-49)
    answerDelay: 2, // Seconds between answers (simulating thinking time)
};

// Session storage per virtual user
let userSessions = {};

export function setup() {
    console.log('=== Load Test Configuration ===');
    console.log(`Base URL: ${BASE_URL}`);
    console.log(`Target Users: 100`);
    console.log(`Test Duration: ~2 minutes`);
    console.log(`Exam ID: ${EXAM_DATA.examId} (Pretest Kewirausahaan Industri)`);
    console.log(`Total Questions: ${EXAM_DATA.questionCount}`);
    console.log(`Question IDs: 20-49`);
    console.log(`Answer Delay: ${EXAM_DATA.answerDelay} seconds`);
    console.log('================================');

    // You can pre-populate exam data here if needed
    console.log('Setup completed. Starting load test...');
}

export default function () {
    // Get user credentials
    const user = USERS[__VU % USERS.length];
    const userId = __VU;

    // Initialize user session if not exists
    if (!userSessions[userId]) {
        userSessions[userId] = {
            loggedIn: false,
            sessionId: null,
            currentExam: null,
            answeredQuestions: []
        };
    }

    const session = userSessions[userId];

    try {
        // Step 1: Login (if not already logged in)
        if (!session.loggedIn) {
            login(user, session);
        }

        // Step 2: Access exam list
        const examListResponse = accessExamList(session);

        // Step 3: Start exam (if not started)
        if (!session.currentExam) {
            startExam(EXAM_DATA.examId, session);
        }

        // Step 4: Answer questions (main load testing activity)
        answerQuestions(session);

        // Step 5: Finish exam (after answering all questions)
        if (session.answeredQuestions.length >= EXAM_DATA.questionCount) {
            finishExam(session);
        }

        // Random delay between operations
        sleep(Math.random() * 2 + 1);

    } catch (error) {
        console.error(`User ${userId} error: ${error.message}`);
        // Reset session on error
        userSessions[userId] = {
            loggedIn: false,
            sessionId: null,
            currentExam: null,
            answeredQuestions: []
        };
        sleep(5); // Wait before retry
    }
}

function login(user, session) {
    // Step 1: Get login page to extract CSRF token
    const pageResponse = http.get(`${BASE_URL}/login`, {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
    });

    // Extract CSRF token from the page
    const csrfToken = extractCsrfToken(pageResponse);

    if (!csrfToken) {
        throw new Error(`Failed to extract CSRF token for user ${user.nim}`);
    }

    // Step 2: Perform login with extracted CSRF token
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
        },
    };

    const loginResponse = http.post(`${BASE_URL}/login`, loginPayload, params);

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
    });

    if (loginSuccess) {
        session.loggedIn = true;
        session.sessionId = extractSessionId(loginResponse);
        session.csrfToken = csrfToken; // Store CSRF token for subsequent requests
        console.log(`User ${user.nim} logged in successfully`);
    } else {
        console.error(`Login failed for user ${user.nim}: ${loginResponse.body}`);
        console.error(`CSRF Token used: ${csrfToken}`);
        throw new Error(`Login failed for user ${user.nim}: Status ${loginResponse.status}`);
    }
}

function accessExamList(session) {
    const params = {
        headers: {
            'Cookie': session.sessionId,
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
    };

    const response = http.get(`${BASE_URL}/ujian`, params);

    check(response, {
        'exam list status is 200': (r) => r.status === 200,
        'exam list contains exams': (r) => r.html('html').find('table').length > 0,
    });

    return response;
}

function startExam(examId, session) {
    // First generate encrypted URL
    const encryptResponse = http.post(`${BASE_URL}/ujian/generate-encrypted-url`,
        JSON.stringify({
            exam_id: examId,
            action: 'start',
            _token: session.csrfToken || getCsrfToken()
        }), {
        headers: {
            'Cookie': session.sessionId,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Referer': `${BASE_URL}/ujian`,
        },
    });

    if (encryptResponse.status !== 200) {
        throw new Error(`Failed to generate encrypted URL: Status ${encryptResponse.status}`);
    }

    const encryptData = JSON.parse(encryptResponse.body);
    if (!encryptData.success) {
        throw new Error(`Encryption failed: ${encryptData.message}`);
    }

    // Start exam with encrypted URL
    const response = http.get(`${BASE_URL}/ujian/start/${encryptData.encrypted_url.split('/').pop()}`, {
        headers: {
            'Cookie': session.sessionId,
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
    });

    const success = check(response, {
        'exam start status is 200 or 302': (r) => r.status === 200 || r.status === 302,
        'exam page loaded': (r) => r.status === 200 || r.status === 302,
    });

    examStartRate.add(success);

    if (success) {
        session.currentExam = examId;
        console.log(`User started exam ${examId}`);
    } else {
        throw new Error(`Failed to start exam: Status ${response.status}`);
    }
}

function answerQuestions(session) {
    const questionsToAnswer = Math.min(5, EXAM_DATA.questionCount - session.answeredQuestions.length);

    for (let i = 0; i < questionsToAnswer; i++) {
        // Gunakan ID soal yang sesuai dengan database (20-49 untuk Exam ID 2)
        const questionId = 20 + session.answeredQuestions.length + i;

        // Pilihan jawaban yang tersedia sesuai database (setiap soal memiliki 4 pilihan)
        // ID pilihan berkisar antara 54-173 untuk exam ID 2
        const baseAnswerId = 54 + ((questionId - 20) * 4);
        const answerId = baseAnswerId + Math.floor(Math.random() * 4);

        const answerPayload = {
            'id_soal': questionId,
            'id_pilihan': answerId,
            '_token': session.csrfToken || getCsrfToken(),
        };

        const response = http.post(`${BASE_URL}/ujian/submit-answer`, answerPayload, {
            headers: {
                'Cookie': session.sessionId,
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Referer': `${BASE_URL}/ujian`,
            },
        });

        const success = check(response, {
            'answer submit status is 200': (r) => r.status === 200,
            'answer response is success': (r) => {
                if (r.status !== 200) return false;
                try {
                    const body = JSON.parse(r.body);
                    return body.success === true;
                } catch (e) {
                    return false;
                }
            },
        });

        answerSubmitRate.add(success);

        if (success) {
            session.answeredQuestions.push(questionId);
            console.log(`Answer submitted for question ${questionId} with choice ${answerId}`);
        } else {
            console.error(`Failed to submit answer for question ${questionId}: ${response.body}`);
        }

        // Simulate thinking time between questions
        sleep(EXAM_DATA.answerDelay);
    }
}

function finishExam(session) {
    const payload = {
        '_token': session.csrfToken || getCsrfToken(),
    };

    const response = http.post(`${BASE_URL}/ujian/finish`, payload, {
        headers: {
            'Cookie': session.sessionId,
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Referer': `${BASE_URL}/ujian`,
        },
    });

    const success = check(response, {
        'exam finish status is 200': (r) => r.status === 200,
        'exam finish response is success': (r) => {
            if (r.status !== 200) return false;
            try {
                const body = JSON.parse(r.body);
                return body.success === true;
            } catch (e) {
                return false;
            }
        },
    });

    examFinishRate.add(success);

    if (success) {
        console.log(`User finished exam successfully`);
        // Reset for next exam iteration
        session.currentExam = null;
        session.answeredQuestions = [];
    } else {
        console.error(`Failed to finish exam: ${response.body}`);
    }
}

function getCsrfToken() {
    // Deprecated: Use extractCsrfToken instead
    return 'test-csrf-token';
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

        // Try to extract from JSON (if page contains JSON configuration)
        const jsonMatch = html.match(/"csrf_token"\s*:\s*"([^"]+)"/i);
        if (jsonMatch && jsonMatch[1]) {
            return jsonMatch[1];
        }

        console.warn('Could not extract CSRF token from response');
        return null;
    } catch (e) {
        console.error('Error extracting CSRF token:', e);
        return null;
    }
}

function extractSessionId(response) {
    // Extract session cookie from response headers
    const setCookieHeader = response.headers['Set-Cookie'];
    if (setCookieHeader) {
        const sessionId = setCookieHeader.split(';')[0].split('=')[1];
        return `laravel_session=${sessionId}`;
    }
    return 'laravel_session=test-session';
}

export function teardown() {
    console.log('=== Load Test Completed ===');
    console.log('Test finished. Check results for performance metrics.');
    console.log('=============================');
}