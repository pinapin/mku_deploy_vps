@extends('layouts.master')

@section('title', $ujian->nama_ujian)

@push('css')
    <style>
        /* Hide sidebar and navbar during exam for focus */
        .main-header,
        .main-sidebar,
        .main-footer,
        .content-header,
        .control-sidebar {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }

        body {
            background: #f8f9fa;
            overflow: hidden;
        }

        .exam-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Compact Minimap Sidebar */
        .question-sidebar {
            width: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-right: none;
            overflow-y: auto;
            padding: 8px 6px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .question-nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: center;
        }

        .question-number {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 11px;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
        }

        .question-number.unanswered {
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.7);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .question-number.answered {
            background: rgba(40, 167, 69, 0.9);
            color: white;
            border-color: #28a745;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
        }

        .question-number.active {
            background: #fff;
            color: #667eea;
            border-color: #fff;
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }

        .question-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: white;
            margin: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }

        /* Improved Exam Header */
        .exam-header {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .exam-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .exam-title i {
            margin-right: 10px;
            color: #667eea;
        }

        /* Floating Timer */
        .floating-timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 20px rgba(238, 90, 82, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideInRight 0.5s ease;
        }

        .floating-timer i {
            animation: rotate 2s linear infinite;
        }

        .floating-timer.warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            animation: pulse-warning 1s infinite, slideInRight 0.5s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse-warning {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 4px 20px rgba(255, 152, 0, 0.4);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 4px 25px rgba(255, 152, 0, 0.6);
            }
        }

        /* Enhanced Question Card */
        .question-card {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .question-header {
            display: flex;
            /* align-items: center; */
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f4;
        }

        .question-number-label {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            font-size: 16px;
        }

        .question-text {
            font-size: 18px;
            line-height: 1.7;
            color: #2c3e50;
            margin: 0;
        }

        .option-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .option-item {
            margin: 12px 0;
        }

        .option-item label {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #fafbfc;
            margin: 0;
        }

        .option-item label:hover {
            background: #f0f4ff;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .option-item input[type="radio"]:checked+label {
            background: #f0f4ff;
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .option-item input[type="radio"] {
            display: none;
        }

        .option-label {
            display: flex;
            align-items: flex-start;
            width: 100%;
        }

        .option-indicator {
            background: #e9ecef;
            color: #6c757d;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            font-size: 14px;
            flex-shrink: 0;
        }

        .option-item input[type="radio"]:checked~.option-label .option-indicator {
            background: #667eea;
            color: white;
        }

        .option-text {
            flex: 1;
            line-height: 1.6;
            color: #495057;
        }

        /* Enhanced Navigation */
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 0;
            margin-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .nav-btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .finish-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .finish-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }

        /* Question Counter */
        .question-counter {
            text-align: center;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 20px;
            font-weight: 500;
            color: #6c757d;
            border: 1px solid #e9ecef;
        }

        /* Progress Bar */
        .progress-bar-container {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
            transition: width 0.4s ease, background 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .question-sidebar {
                width: 50px;
                padding: 6px 4px;
            }

            .question-number {
                width: 32px;
                height: 32px;
                font-size: 10px;
            }

            .question-content {
                padding: 20px;
                margin: 10px;
            }

            .floating-timer {
                top: 10px;
                right: 10px;
                padding: 10px 15px;
                font-size: 14px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Floating Timer -->
    <div class="floating-timer" id="floatingTimer" data-seconds="{{ $remainingTime }}">
        <i class="fas fa-clock"></i>
        <span id="timerDisplay">00:00</span>
    </div>

    <div class="exam-container">
        <!-- Compact Minimap Sidebar -->
        <div class="question-sidebar">
            <div class="question-nav" id="minimap-container">
                <!-- Minimap will be generated dynamically via JavaScript -->
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="question-content">
            <!-- Progress Bar -->
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
            </div>

            <!-- Improved Exam Header -->
            <div class="exam-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="exam-title">
                            <i class="fas fa-clipboard-list"></i>
                            {{ $ujian->nama_ujian }}
                        </h3>
                        <small class="text-muted">Durasi: {{ $ujian->durasi_menit }} menit | Total Soal:
                            {{ $questions->count() }}</small>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="question-counter">
                            <span id="currentQuestion">1</span> / <span id="totalQuestions">{{ $questions->count() }}</span>
                            <small class="text-muted d-block mt-1">
                                <span id="answeredCount">0</span> dijawab
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Container -->
            <div id="questionContainer">
                <!-- Questions will be loaded here via JavaScript -->
            </div>

            <!-- Enhanced Navigation Buttons -->
            <div class="navigation-buttons">
                <button type="button" class="btn btn-secondary nav-btn" id="prevBtn" disabled>
                    <i class="fas fa-chevron-left"></i> Previous
                </button>

                <div class="question-counter d-none d-md-block">
                    Pertanyaan <span id="currentQuestionNav">1</span> dari <span
                        id="totalQuestionsNav">{{ $questions->count() }}</span>
                </div>

                <button type="button" class="finish-btn" id="finishBtn" data-allowed>
                    <i class="fas fa-flag-checkered"></i> Selesai Ujian
                </button>

                <button type="button" class="btn btn-primary nav-btn" id="nextBtn">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Data from Laravel - Use cached questions
        const originalQuestions = @json($questions->sortBy('nomor_soal')->values());
        const jawabanSiswa = @json($jawabanMahasiswa);
        const sesiId = {{ $sesiUjian->id }};

        // Shuffle questions based on student session (consistent shuffle)
        function shuffleArray(array, seed) {
            const shuffled = [...array];
            let currentSeed = seed;

            // Simple seeded random function
            function random() {
                currentSeed = (currentSeed * 9301 + 49297) % 233280;
                return currentSeed / 233280;
            }

            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }

            return shuffled;
        }

        // Create consistent shuffle based on student ID and session
        const seed = sesiId * 1000 + (originalQuestions.length * 7);
        const questions = shuffleArray(originalQuestions, seed);

        // State management
        let currentQuestionIndex = 0;
        let timerInterval;
        let startTime = Date.now(); // Track exam start time
        let pendingAnswers = {}; // Store answers for batch submission
        let batchSubmissionTimer;

        // Local storage key for this exam session
        const storageKey = `exam_answers_${sesiId}`;

        // Local Storage Functions
        let lastSaveData = null; // Cache to prevent unnecessary writes
        let saveAttempts = 0;
        const maxSaveAttempts = 3; // Prevent infinite loops

        function saveAnswersToLocalStorage() {
            try {
                // Prevent excessive saves - only save if data actually changed
                const currentSaveData = {
                    answers: jawabanSiswa,
                    pendingAnswers: pendingAnswers,
                    currentQuestionIndex: currentQuestionIndex
                };

                // Quick check if data actually changed (performance optimization)
                if (lastSaveData && JSON.stringify(currentSaveData) === JSON.stringify(lastSaveData)) {
                    return; // Skip saving if data is identical
                }

                const storageData = {
                    ...currentSaveData,
                    timestamp: Date.now()
                };

                localStorage.setItem(storageKey, JSON.stringify(storageData));
                lastSaveData = currentSaveData; // Update cache
                saveAttempts = 0; // Reset counter on success

            } catch (error) {
                saveAttempts++;
                console.error('Error saving to localStorage (attempt ' + saveAttempts + '):', error);

                // If we hit storage quota or other errors, try to clear old data
                if (saveAttempts >= maxSaveAttempts) {
                    try {
                        // Emergency: clear localStorage to prevent crashes
                        localStorage.removeItem(storageKey);
                        showNotification('⚠️ Local storage penuh, data lama dibersihkan', 'warning');
                    } catch (clearError) {
                        console.error('Failed to clear localStorage:', clearError);
                    }
                }
            }
        }

        function loadAnswersFromLocalStorage() {
            try {
                const storedData = localStorage.getItem(storageKey);
                if (storedData) {
                    const data = JSON.parse(storedData);

                    // Check if data is not too old (within current exam session)
                    const maxAge = 2 * 60 * 60 * 1000; // 2 hours
                    if (Date.now() - data.timestamp < maxAge) {
                        // Create a Set of valid question IDs for this exam (performance optimization)
                        const validQuestionIds = new Set();
                        for (let j = 0; j < questions.length; j++) {
                            validQuestionIds.add(String(questions[j].id));
                        }

                        // Load pending answers first (for batch submission) with validation
                        const storedPendingAnswers = data.pendingAnswers || {};
                        const pendingKeys = Object.keys(storedPendingAnswers);
                        let validPendingCount = 0;
                        let invalidPendingCount = 0;

                        for (let k = 0; k < pendingKeys.length; k++) {
                            const soalId = pendingKeys[k];
                            const pendingAnswer = storedPendingAnswers[soalId];

                            // Validate that this pending answer belongs to the current exam
                            if (validQuestionIds.has(soalId) && pendingAnswer && pendingAnswer.id_pilihan) {
                                pendingAnswers[soalId] = pendingAnswer;
                                validPendingCount++;
                            } else {
                                invalidPendingCount++;
                            }
                        }

                        // Log invalid pending answers for debugging
                        if (invalidPendingCount > 0) {
                            console.log(
                                `Skipped ${invalidPendingCount} invalid pending answers from different exam session`);
                        }

                        // Merge stored answers with server answers efficiently
                        const storedAnswers = data.answers || {};
                        const storedAnswerKeys = Object.keys(storedAnswers);
                        let restoredCount = 0;
                        let existingCount = 0;
                        let invalidCount = 0;

                        // Early cleanup: If too many invalid answers (>90%), clear all data
                        if (storedAnswerKeys.length > questions.length * 2) {
                            console.log(
                                `Too many stored answers (${storedAnswerKeys.length}) for ${questions.length} questions. Clearing all data.`
                                );
                            localStorage.removeItem(storageKey);
                            return false;
                        }

                        // Count restored answers with validation
                        for (let i = 0; i < storedAnswerKeys.length; i++) {
                            const soalId = storedAnswerKeys[i];

                            // Validate that this answer belongs to the current exam
                            if (!validQuestionIds.has(soalId)) {
                                invalidCount++;
                                continue; // Skip invalid answers
                            }

                            // Check if answer has valid value
                            if (!storedAnswers[soalId] || storedAnswers[soalId] === '' || storedAnswers[soalId] ===
                                'null' || storedAnswers[soalId] === null) {
                                invalidCount++;
                                continue; // Skip invalid answer values
                            }

                            if (!jawabanSiswa[soalId]) {
                                // Only add stored answer if server doesn't have it
                                jawabanSiswa[soalId] = storedAnswers[soalId];
                                restoredCount++;
                            } else {
                                existingCount++;
                            }
                        }

                        // Log basic answers for debugging
                        console.log(`Debug - Total stored answers: ${storedAnswerKeys.length}`);
                        console.log(`Debug - Valid question IDs: ${validQuestionIds.size}`);
                        console.log(
                            `Debug - Restored: ${restoredCount}, Existing: ${existingCount}, Invalid: ${invalidCount}`);

                        if (invalidCount > 0) {
                            console.log(`Skipped ${invalidCount} invalid answers from different exam session`);
                        }

                        // Update currentQuestionIndex if stored (with validation)
                        if (data.currentQuestionIndex !== undefined &&
                            data.currentQuestionIndex >= 0 &&
                            data.currentQuestionIndex < questions.length) {
                            currentQuestionIndex = data.currentQuestionIndex;
                        }

                        // Calculate total answered questions correctly
                        let totalAnswered = 0;

                        // Count unique answered questions from server answers
                        Object.keys(jawabanSiswa).forEach(soalId => {
                            if (jawabanSiswa[soalId]) {
                                totalAnswered++;
                            }
                        });

                        // Count additional pending answers not already counted
                        Object.keys(pendingAnswers).forEach(soalId => {
                            if (pendingAnswers[soalId] && pendingAnswers[soalId].id_pilihan && !jawabanSiswa[
                                soalId]) {
                                totalAnswered++;
                            }
                        });

                        // Log total answered calculation
                        console.log(`Debug - Total answered: ${totalAnswered}, Questions length: ${questions.length}`);

                        // Show comprehensive notification with accurate counts
                        // Only show restoredCount if it's meaningful and reasonable
                        if (restoredCount > 0 && restoredCount <= questions.length && restoredCount !== totalAnswered) {
                            showNotification(`📋 ${restoredCount} jawaban dipulihkan • ${totalAnswered} total terjawab`,
                                'info');
                        } else if (totalAnswered > 0) {
                            // Show total answered count for all other cases
                            showNotification(`📋 ${totalAnswered} jawaban tersedia`, 'info');
                        } else {
                            showNotification(`📋 Tidak ada jawaban yang tersimpan`, 'info');
                        }

                        // Clean up localStorage by removing invalid entries to prevent future issues
                        if (invalidCount > 0 || invalidPendingCount > 0) {
                            // Create cleaned data object with only valid answers
                            const cleanedData = {
                                ...data,
                                answers: {},
                                pendingAnswers: {},
                                timestamp: Date.now()
                            };

                            // Only keep valid answers
                            Object.keys(storedAnswers).forEach(soalId => {
                                if (validQuestionIds.has(soalId)) {
                                    cleanedData.answers[soalId] = storedAnswers[soalId];
                                }
                            });

                            // Only keep valid pending answers
                            Object.keys(storedPendingAnswers).forEach(soalId => {
                                if (validQuestionIds.has(soalId) && storedPendingAnswers[soalId] &&
                                    storedPendingAnswers[soalId].id_pilihan) {
                                    cleanedData.pendingAnswers[soalId] = storedPendingAnswers[soalId];
                                }
                            });

                            // Save cleaned data back to localStorage
                            try {
                                localStorage.setItem(storageKey, JSON.stringify(cleanedData));
                                console.log(
                                    `Data cleanup: ${invalidCount} invalid answers, ${invalidPendingCount} invalid pending answers removed and cleaned`
                                    );
                            } catch (cleanupError) {
                                console.error('Failed to clean localStorage data:', cleanupError);
                            }
                        }

                        return true;
                    } else {
                        // Clear old data
                        localStorage.removeItem(storageKey);
                    }
                }
            } catch (error) {
                console.error('Error loading from localStorage:', error);
                localStorage.removeItem(storageKey); // Clear corrupted data
            }
            return false;
        }

        function clearAnswersFromLocalStorage() {
            try {
                localStorage.removeItem(storageKey);
                // console.log('🗑️ Cleared localStorage');
            } catch (error) {
                console.error('Error clearing localStorage:', error);
            }
        }

        // Initialize exam
        document.addEventListener('DOMContentLoaded', function() {
            loadAnswersFromLocalStorage(); // Load saved answers first
            generateMinimapHTML(); // Generate minimap after loading localStorage data
            loadQuestion(currentQuestionIndex);
            startTimer();
            attachEventListeners();
            updateProgressBar();
            updateAnsweredProgress(); // Initialize with existing answers

            // Adaptive save interval - save only when needed to improve performance
            let saveInterval = setInterval(() => {
                saveAnswersToLocalStorage();
            }, 45000); // Increased to 45 seconds for better performance

            // Clear interval when exam is finished to prevent memory leaks
            window.addEventListener('beforeunload', () => {
                if (saveInterval) {
                    clearInterval(saveInterval);
                }
                // Final save before unload
                saveAnswersToLocalStorage();
            });

            // Listen for online/offline events
            window.addEventListener('online', function() {
                showNotification('🌐 Koneksi pulih. Mengirim jawaban yang tersimpan...', 'success');
                // Try to submit any pending answers immediately
                if (Object.keys(pendingAnswers).length > 0) {
                    submitBatchAnswers();
                }
            });

            window.addEventListener('offline', function() {
                showNotification('📵 Koneksi terputus. Jawaban akan tetap disimpan di local storage.',
                    'warning');
            });
        });

        function updateProgressBar() {
            const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
            document.getElementById('progressBar').style.width = progress + '%';

            // Update question counters
            document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
            document.getElementById('currentQuestionNav').textContent = currentQuestionIndex + 1;
        }

        // Cache DOM elements to prevent repeated queries (performance optimization)
        let cachedAnsweredCountElement = null;
        let cachedProgressBarElement = null;

        function updateAnsweredProgress() {
            // Calculate total answered questions using the same logic as notification
            let answeredCount = 0;

            // Count server answers
            Object.keys(jawabanSiswa).forEach(soalId => {
                if (jawabanSiswa[soalId]) {
                    answeredCount++;
                }
            });

            // Count additional pending answers not already counted
            Object.keys(pendingAnswers).forEach(soalId => {
                if (pendingAnswers[soalId] && pendingAnswers[soalId].id_pilihan && !jawabanSiswa[soalId]) {
                    answeredCount++;
                }
            });

            const totalCount = questions.length;
            const progress = (answeredCount / totalCount) * 100;

            // Use cached DOM elements for better performance
            if (!cachedProgressBarElement) {
                cachedProgressBarElement = document.getElementById('progressBar');
            }
            if (cachedProgressBarElement) {
                cachedProgressBarElement.style.width = progress + '%';
            }

            // Update answered count display
            if (!cachedAnsweredCountElement) {
                cachedAnsweredCountElement = document.getElementById('answeredCount');
            }
            if (cachedAnsweredCountElement) {
                cachedAnsweredCountElement.textContent = answeredCount;
            }

            // Update progress bar color based on completion (using cached element)
            if (cachedProgressBarElement) {
                if (answeredCount === totalCount) {
                    cachedProgressBarElement.style.background = 'linear-gradient(90deg, #28a745 0%, #20c997 100%)';
                } else {
                    cachedProgressBarElement.style.background = 'linear-gradient(90deg, #667eea 0%, #764ba2 100%)';
                }
            }
        }

        // Generate minimap HTML dynamically based on shuffled questions
        function generateMinimapHTML() {
            let minimapHTML = '';

            questions.forEach((question, displayIndex) => {
                // Check if question is answered from either server answers or pending answers
                const hasServerAnswer = jawabanSiswa[question.id] ? true : false;
                const hasPendingAnswer = pendingAnswers[question.id] && pendingAnswers[question.id].id_pilihan ?
                    true : false;
                const isAnswered = hasServerAnswer || hasPendingAnswer;
                const isActive = displayIndex === currentQuestionIndex; // Current question is active

                const questionNumber = displayIndex + 1; // Sequential numbering for display

                minimapHTML += `
                    <div class="question-number ${isAnswered ? 'answered' : 'unanswered'} ${isActive ? 'active' : ''}"
                         data-index="${displayIndex}"
                         data-soal-id="${question.id}"
                         onclick="goToQuestion(${displayIndex})"
                         title="Soal ${questionNumber}">
                        ${questionNumber}
                    </div>
                `;
            });

            document.getElementById('minimap-container').innerHTML = minimapHTML;
        }

        // Navigation function for minimap clicks
        function goToQuestion(index) {
            loadQuestion(index);
        }

        function loadQuestion(index) {
            if (index < 0 || index >= questions.length) return;

            currentQuestionIndex = index;
            const question = questions[index];
            const questionContainer = document.getElementById('questionContainer');

            // Update minimap
            document.querySelectorAll('.question-number').forEach((el, i) => {
                el.classList.remove('active');
                if (i === index) {
                    el.classList.add('active');
                }
            });

            // Generate question HTML with new styling
            const optionsHtml = question.pilihan.map(option => `
                <div class="option-item">
                    <input type="radio"
                           id="option_${option.id}"
                           name="answer_${question.id}"
                           value="${option.id}"
                           data-soal-id="${question.id}"
                           data-pilihan-id="${option.id}"
                           ${jawabanSiswa[question.id] == option.id ? 'checked' : ''}>
                    <label for="option_${option.id}" class="option-label">
                        <div class="option-indicator">${option.huruf_pilihan}</div>
                        <div class="option-text">${option.teks_pilihan}</div>
                    </label>
                </div>
            `).join('');

            questionContainer.innerHTML = `
                <div class="question-card">
                    <div class="question-header">
                        <div class="question-number-label">${index + 1}</div>
                        <div class="question-text">${question.teks_soal}</div>
                    </div>
                    <div class="option-list">
                        ${optionsHtml}
                    </div>
                </div>
            `;

            // Update navigation buttons
            document.getElementById('prevBtn').disabled = index === 0;
            document.getElementById('nextBtn').disabled = index === questions.length - 1;

            // Update progress bar
            updateProgressBar();

            // Attach change event listeners to radio buttons
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', handleAnswerChange);
            });
        }

        function handleAnswerChange(event) {
            const soalId = event.target.dataset.soalId;
            const pilihanId = event.target.dataset.pilihanId;

            // Add to pending answers for batch submission
            pendingAnswers[soalId] = {
                id_soal: soalId,
                id_pilihan: pilihanId
            };

            // Update local state immediately for better UX
            jawabanSiswa[soalId] = pilihanId;

            // Update minimap - find the minimap element with matching soalId
            document.querySelectorAll('.question-number').forEach((element) => {
                const elementSoalId = element.getAttribute('data-soal-id');
                if (elementSoalId == soalId) {
                    element.classList.remove('unanswered');
                    element.classList.add('answered');
                }
            });

            // Update progress bar based on answered questions
            updateAnsweredProgress();

            // Save to localStorage immediately for backup
            saveAnswersToLocalStorage();

            // Clear existing timer and set new one for batch submission
            if (batchSubmissionTimer) {
                clearTimeout(batchSubmissionTimer);
            }

            // Schedule batch submission after 3 seconds of inactivity
            batchSubmissionTimer = setTimeout(() => {
                submitBatchAnswers();
            }, 3000);

            // Try to submit immediately if connection is available
            if (navigator.onLine) {
                submitBatchAnswers();
            } else {
                showNotification('📵 Offline mode. Jawaban akan disimpan di local storage.', 'warning');
            }
        }

        function submitBatchAnswers() {
            if (Object.keys(pendingAnswers).length === 0) {
                return;
            }

            const answersToSubmit = Object.values(pendingAnswers);

            // Prepare batch submission data
            fetch('{{ route('ujian.submitBatchAnswers') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        answers: answersToSubmit
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear pending answers after successful submission
                        pendingAnswers = {};
                        // Save updated state to localStorage
                        saveAnswersToLocalStorage();
                        // console.log('✅ ' + data.message);
                    } else {
                        console.error('❌ Batch submission failed:', data.message);
                        // Keep pending answers for retry
                        showNotification('Gagal menyimpan beberapa jawaban', 'error');
                        // Ensure answers are saved to localStorage even on failure
                        saveAnswersToLocalStorage();
                    }
                })
                .catch(error => {
                    console.error('❌ Error in batch submission:', error);
                    showNotification('Gagal menyimpan jawaban', 'error');
                });
        }

        function submitBatchAnswersImmediately() {
            return new Promise((resolve, reject) => {
                if (Object.keys(pendingAnswers).length === 0) {
                    resolve();
                    return;
                }

                const answersToSubmit = Object.values(pendingAnswers);

                // Prepare batch submission data
                fetch('{{ route('ujian.submitBatchAnswers') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            answers: answersToSubmit
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear pending answers after successful submission
                            pendingAnswers = {};
                            // Save updated state to localStorage
                            saveAnswersToLocalStorage();
                            // console.log('✅ ' + data.message);
                            resolve();
                        } else {
                            console.error('❌ Batch submission failed:', data.message);
                            // Save answers to localStorage even on failure
                            saveAnswersToLocalStorage();
                            reject(new Error(data.message));
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error in immediate batch submission:', error);
                        reject(error);
                    });
            });
        }

        function startTimer() {
            const timerElement = document.getElementById('floatingTimer');
            const timerDisplay = document.getElementById('timerDisplay');
            let seconds = parseInt(timerElement.dataset.seconds);

            if (seconds <= 0) {
                autoSubmit();
                return;
            }

            // Initial display
            updateTimerDisplay(seconds, timerDisplay);

            timerInterval = setInterval(() => {
                seconds--;
                updateTimerDisplay(seconds, timerDisplay);

                // Add warning class when time is low
                if (seconds <= 300 && seconds > 0) { // 5 minutes
                    timerElement.classList.add('warning');
                }

                if (seconds <= 0) {
                    clearInterval(timerInterval);
                    autoSubmit();
                }
            }, 1000);
        }

        function updateTimerDisplay(seconds, displayElement) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            let timeString = '';
            if (hours > 0) {
                timeString =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            } else {
                timeString = `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            displayElement.textContent = timeString;
        }

        function autoSubmit() {
            showNotification('⚠️ Waktu habis! Menyimpan jawaban terakhir...', 'warning');

            // Disable all UI elements
            document.getElementById('prevBtn').disabled = true;
            document.getElementById('nextBtn').disabled = true;
            document.getElementById('finishBtn').disabled = true;
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.disabled = true;
            });

            // Clear timers
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            if (batchSubmissionTimer) {
                clearTimeout(batchSubmissionTimer);
            }

            // Submit any pending batch answers first
            submitBatchAnswersImmediately().then(() => {
                    showNotification('⚠️ Waktu habis! Ujian akan disimpan otomatis.', 'warning');

                    // Then submit via timeout endpoint (no result redirect)
                    return fetch('{{ route('ujian.timeoutSubmit') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        // Clear localStorage before redirect
                        clearAnswersFromLocalStorage();
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        showNotification('Gagal menyimpan ujian', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error submitting exam:', error);
                    showNotification('Terjadi kesalahan saat menyimpan ujian', 'error');
                });
        }

        function submitExam() {
            // Disable all buttons and UI elements
            document.getElementById('prevBtn').disabled = true;
            document.getElementById('nextBtn').disabled = true;
            document.getElementById('finishBtn').disabled = true;
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.disabled = true;
            });

            // Clear timer and batch submission timer
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            if (batchSubmissionTimer) {
                clearTimeout(batchSubmissionTimer);
            }

            showNotification('🔄 Sedang menyimpan jawaban terakhir...', 'info');

            // Submit any pending batch answers first
            submitBatchAnswersImmediately().then(() => {
                    showNotification('🔄 Sedang menyelesaikan ujian...', 'info');

                    // Then submit the exam
                    return fetch('{{ route('ujian.finish') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const message = data.scoring_queued ?
                            '✅ Ujian berhasil diselesaikan! Nilai sedang diproses.' :
                            '✅ Ujian berhasil diselesaikan! Mengarahkan ke halaman ujian...';

                        showNotification(message, 'success');

                        // Hide the finish button and show completion message
                        const finishBtn = document.getElementById('finishBtn');
                        finishBtn.innerHTML = '<i class="fas fa-check"></i> Ujian Selesai';
                        finishBtn.classList.remove('finish-btn');
                        finishBtn.classList.add('btn', 'btn-success');
                        finishBtn.disabled = true;

                        // Clear localStorage before redirect
                        clearAnswersFromLocalStorage();

                        // Redirect to exam list after showing success message
                        setTimeout(() => {
                            window.location.href = '{{ route('ujian.index') }}';
                        }, 3000);
                    } else {
                        showNotification('Gagal menyimpan ujian', 'error');
                        // Re-enable buttons on error
                        document.getElementById('finishBtn').disabled = false;
                        document.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.disabled = false;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error submitting exam:', error);
                    showNotification('Gagal menyimpan ujian', 'error');
                    // Re-enable buttons on error
                    document.getElementById('finishBtn').disabled = false;
                    document.querySelectorAll('input[type="radio"]').forEach(input => {
                        input.disabled = false;
                    });
                });
        }

        function attachEventListeners() {
            // Previous button
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentQuestionIndex > 0) {
                    loadQuestion(currentQuestionIndex - 1);
                }
            });

            // Next button
            document.getElementById('nextBtn').addEventListener('click', () => {
                if (currentQuestionIndex < questions.length - 1) {
                    loadQuestion(currentQuestionIndex + 1);
                }
            });

            // Finish button
            document.getElementById('finishBtn').addEventListener('click', () => {
                const answeredCount = Object.keys(jawabanSiswa).length;
                const totalCount = questions.length;

                if (answeredCount < totalCount) {
                    if (!confirm(
                            `Anda hanya menjawab ${answeredCount} dari ${totalCount} soal. Yakin ingin menyelesaikan ujian?`
                        )) {
                        return;
                    }
                } else {
                    if (!confirm('Apakah Anda yakin ingin menyelesaikan ujian?')) {
                        return;
                    }
                }
                submitExam();
            });

            // Question number clicks are now handled by onclick attributes in the generated HTML

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft' && currentQuestionIndex > 0) {
                    loadQuestion(currentQuestionIndex - 1);
                } else if (e.key === 'ArrowRight' && currentQuestionIndex < questions.length - 1) {
                    loadQuestion(currentQuestionIndex + 1);
                }
            });
        }

        function showNotification(message, type) {
            // Enhanced notification
            const notification = document.createElement('div');
            notification.className =
                `alert alert-${type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'success'} position-fixed`;
            notification.style.cssText =
                'top: 80px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'check-circle'} mr-2"></i>
                    ${message}
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // PREVENT NAVIGATION - EXTENDED SECURITY MEASURES

        // Block all navigation attempts
        function blockNavigation() {
            showNotification('⛔️ Navigasi tidak diizinkan selama ujian berlangsung!', 'error');
            return false;
        }

        // Enhanced back navigation prevention
        window.addEventListener('popstate', function(event) {
            event.preventDefault();
            blockNavigation();
            window.history.pushState(null, null, window.location.href);
        });

        // Add multiple history states to prevent back
        window.history.pushState(null, null, window.location.href);
        window.history.pushState(null, null, window.location.href);
        window.history.pushState(null, null, window.location.href);

        // Block all link clicks
        document.addEventListener('click', function(event) {
            const link = event.target.closest('a');
            if (link && !link.hasAttribute('data-allowed')) {
                event.preventDefault();
                event.stopPropagation();
                blockNavigation();
            }
        });

        // Block form submissions
        document.addEventListener('submit', function(event) {
            if (!event.target.hasAttribute('data-allowed')) {
                event.preventDefault();
                event.stopPropagation();
                blockNavigation();
            }
        });

        // Prevent page unload
        window.addEventListener('beforeunload', function(event) {
            event.preventDefault();
            event.returnValue = '';
            return '⛔️ Ujian sedang berlangsung! Apakah Anda yakin ingin meninggalkan halaman ini?';
        });

        // Tab switching prevention with stricter measures
        let tabSwitchCount = 0;
        let warningShown = false;

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                tabSwitchCount++;

                if (tabSwitchCount === 1 && !warningShown) {
                    showNotification('⚠️ PERINGATAN: Jangan beralih tab! Ini adalah pelanggaran ' + tabSwitchCount +
                        '/3', 'warning');
                    warningShown = true;
                } else if (tabSwitchCount >= 2) {
                    showNotification('⛔️ PELANGGARAN SERIUS: Anda telah beralih tab ' + tabSwitchCount +
                        ' kali! Ujian akan otomatis disimpan.', 'error');

                    // Auto submit on repeated tab switching
                    if (tabSwitchCount >= 3) {
                        showNotification('⚠️ Ujian otomatis disimpan karena pelanggaran keamanan!', 'error');
                        autoSubmit();
                    }
                }
            } else {
                // Tab refocused
                if (tabSwitchCount > 0) {
                    showNotification('📊 Anda kembali ke ujian. Pelanggaran: ' + tabSwitchCount + '/3', 'info');
                }
            }
        });

        // Window focus/blur detection with stricter measures
        let blurCount = 0;
        window.addEventListener('blur', function() {
            blurCount++;
            if (blurCount === 1) {
                showNotification('⚠️ Jangan meninggalkan jendela ujian!', 'warning');
            } else if (blurCount >= 2) {
                showNotification('⛔️ PELANGGARAN: Jangan tinggalkan jendela ujian!', 'error');
            }
        });

        // Keyboard shortcuts prevention
        document.addEventListener('keydown', function(event) {
            // Block common navigation shortcuts
            if (
                // Alt + Arrow keys (navigation)
                (event.altKey && (event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key ===
                    'ArrowUp' || event.key === 'ArrowDown')) ||
                // Alt + Tab (tab switching)
                event.key === 'Tab' && event.altKey ||
                // F5 (refresh)
                event.key === 'F5' ||
                // Ctrl + R (refresh)
                (event.ctrlKey || event.metaKey) && event.key === 'r' ||
                // Ctrl + Shift + R (hard refresh)
                (event.ctrlKey || event.metaKey) && event.shiftKey && event.key === 'r' ||
                // Escape key
                event.key === 'Escape' ||
                // Backspace (navigation)
                event.key === 'Backspace' && !event.target.matches('input, textarea')
            ) {
                event.preventDefault();
                event.stopPropagation();
                blockNavigation();
                return false;
            }

            // Block copy-paste shortcuts with stricter messages
            if ((event.ctrlKey || event.metaKey) && (event.key === 'c' || event.key === 'v' || event.key === 'x' ||
                    event.key === 'a')) {
                event.preventDefault();
                event.stopPropagation();
                const action = event.key === 'c' ? 'menyalin' : (event.key === 'v' ? 'menempel' : (event.key ===
                    'x' ? 'memotong' : 'memilih semua'));
                showNotification('🚫 ' + action.toUpperCase() + ' tidak diizinkan selama ujian!', 'error');
                return false;
            }

            // Block F12 (developer tools)
            if (event.key === 'F12' || (event.ctrlKey && event.shiftKey && event.key === 'I')) {
                event.preventDefault();
                showNotification('🚫 Developer Tools tidak diizinkan selama ujian!', 'error');
                return false;
            }
        });

        // Prevent right-click with enhanced message
        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
            event.stopPropagation();
            showNotification('🚫 Klik kanan tidak diizinkan selama ujian!', 'error');
            return false;
        });

        // Block text selection
        document.addEventListener('selectstart', function(event) {
            if (!event.target.matches('input, textarea')) {
                event.preventDefault();
                return false;
            }
        });

        // Block drag and drop
        document.addEventListener('dragstart', function(event) {
            event.preventDefault();
            return false;
        });

        // Prevent zoom with Ctrl + Scroll
        document.addEventListener('wheel', function(event) {
            if (event.ctrlKey) {
                event.preventDefault();
                return false;
            }
        }, {
            passive: false
        });

        // Fullscreen recommendation at start
        setTimeout(() => {
            showNotification('💡 Rekomendasi: Tekan F11 untuk mode fullscreen dan fokus penuh pada ujian', 'info');
        }, 2000);

        // Periodic security reminders
        setInterval(() => {
            const elapsed = (Date.now() - startTime) / 1000;
            showNotification('⏱️ Waktu berlalu: ' + Math.floor(elapsed / 60) + ' menit. Tetap fokus pada ujian!',
                'info');
        }, 600000); // Every 10 minutes
    </script>
@endpush
