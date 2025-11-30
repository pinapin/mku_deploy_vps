<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Lagi Rebahan Sebentar 🛠️</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #285aa6 0%, #febf32 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 20px;
            margin: 0;
        }

        /* Animated Background */
        .bg-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-code {
            position: absolute;
            font-family: 'Courier New', monospace;
            color: rgba(255, 255, 255, 0.1);
            font-size: 14px;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            from {
                transform: translateY(100vh) rotate(0deg);
            }
            to {
                transform: translateY(-100vh) rotate(360deg);
            }
        }

        .main-container {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            width: 100%;
            align-items: stretch;
            height: calc(100vh - 40px);
            max-height: 800px;
        }

        .maintenance-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .game-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 30px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            display: none;
            flex-direction: column;
        }

        .game-section.active {
            display: flex;
        }

        .robot-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            animation: bounce 2s infinite ease-in-out;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            40% {
                transform: translateY(-20px) rotate(5deg);
            }
            60% {
                transform: translateY(-10px) rotate(-5deg);
            }
        }

        .robot {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.5);
        }

        .robot-eyes {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 20px;
        }

        .eye {
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            position: relative;
            animation: blink 4s infinite;
        }

        @keyframes blink {
            0%, 90%, 100% {
                transform: scaleY(1);
            }
            95% {
                transform: scaleY(0.1);
            }
        }

        .eye::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: #333;
            border-radius: 50%;
            top: 5px;
            left: 5px;
            animation: look 3s infinite;
        }

        @keyframes look {
            0%, 100% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(3px, 0);
            }
            75% {
                transform: translate(-3px, 0);
            }
        }

        .robot-mouth {
            position: absolute;
            bottom: 30%;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 15px;
            background: #333;
            border-radius: 0 0 15px 15px;
        }

        .wrench {
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 40px;
            color: #ff6b6b;
            animation: spin 3s infinite linear;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        h1 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #285aa6 0%, #febf32 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .message {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 25px;
            line-height: 1.6;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .time-estimate {
            background: linear-gradient(135deg, #febf32 0%, #285aa6 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            display: inline-block;
            font-weight: 600;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(40, 90, 166, 0.3);
            font-size: 0.9em;
        }

        /* Mini Game Styles */
        .game-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .game-title {
            color: #333;
            font-size: 1.2em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .game-area {
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            height: 200px;
            position: relative;
            overflow: hidden;
            cursor: crosshair;
            margin-bottom: 20px;
            flex: 1;
        }

        .bug {
            position: absolute;
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            box-shadow: 0 3px 10px rgba(255, 107, 107, 0.4);
            animation: wobble 2s infinite ease-in-out;
        }

        @keyframes wobble {
            0%, 100% {
                transform: rotate(-5deg) scale(1);
            }
            50% {
                transform: rotate(5deg) scale(1.1);
            }
        }

        .bug:hover {
            transform: scale(1.2) rotate(180deg);
            background: linear-gradient(135deg, #00d2d3 0%, #01a3a4 100%);
        }

        .bug-clicked {
            animation: explode 0.5s ease-out forwards;
        }

        @keyframes explode {
            0% {
                transform: scale(1) rotate(0deg);
            }
            50% {
                transform: scale(1.5) rotate(180deg);
            }
            100% {
                transform: scale(0) rotate(360deg);
                opacity: 0;
            }
        }

        .game-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .stat-item {
            background: linear-gradient(135deg, #285aa6 0%, #febf32 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin: 5px;
            box-shadow: 0 3px 10px rgba(40, 90, 166, 0.3);
            font-size: 0.85em;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #febf32 0%, #285aa6 100%);
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 11px;
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #285aa6 0%, #febf32 100%);
            color: white;
            box-shadow: 0 3px 10px rgba(40, 90, 166, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(40, 90, 166, 0.5);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #febf32 0%, #285aa6 100%);
            color: white;
            box-shadow: 0 3px 10px rgba(254, 191, 50, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(254, 191, 50, 0.5);
        }

        .btn-game {
            background: linear-gradient(135deg, #febf32 0%, #285aa6 100%);
            color: white;
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(254, 191, 50, 0.4);
            transition: all 0.3s ease;
        }

        .btn-game:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(254, 191, 50, 0.6);
        }

        .btn-back {
            background: linear-gradient(135deg, #285aa6 0%, #febf32 100%);
            color: white;
            padding: 10px 20px;
            font-size: 0.9em;
            border-radius: 20px;
            box-shadow: 0 3px 10px rgba(40, 90, 166, 0.3);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(40, 90, 166, 0.5);
        }

        .coffee-animation {
            font-size: 30px;
            animation: steam 3s infinite ease-in-out;
        }

        @keyframes steam {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-10px) scale(1.1);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-container {
                flex-direction: column;
                height: auto;
                max-height: none;
            }

            .maintenance-section,
            .game-section {
                min-height: 400px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .main-container {
                gap: 20px;
            }

            .maintenance-section {
                padding: 30px 20px;
            }

            .game-section {
                padding: 20px;
            }

            h1 {
                font-size: 1.6em;
                margin-bottom: 15px;
            }

            .message {
                font-size: 0.9em;
                margin-bottom: 20px;
            }

            .robot-container {
                width: 100px;
                height: 100px;
                margin-bottom: 20px;
            }

            .robot {
                width: 75px;
                height: 75px;
            }

            .time-estimate {
                padding: 10px 20px;
                margin-bottom: 20px;
                font-size: 0.85em;
            }

            .game-area {
                height: 180px;
            }

            .bug {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .btn-game {
                padding: 12px 25px;
                font-size: 1em;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .main-container {
                gap: 15px;
            }

            .maintenance-section {
                padding: 25px 15px;
            }

            .game-section {
                padding: 15px;
            }

            h1 {
                font-size: 1.4em;
                margin-bottom: 12px;
            }

            .message {
                font-size: 0.85em;
                margin-bottom: 15px;
            }

            .robot-container {
                width: 80px;
                height: 80px;
                margin-bottom: 15px;
            }

            .robot {
                width: 60px;
                height: 60px;
            }

            .time-estimate {
                padding: 8px 15px;
                margin-bottom: 15px;
                font-size: 0.8em;
            }

            .game-title {
                font-size: 1.1em;
                margin-bottom: 10px;
            }

            .game-area {
                height: 150px;
                margin-bottom: 15px;
            }

            .bug {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }

            .game-stats {
                margin-top: 10px;
            }

            .stat-item {
                padding: 6px 12px;
                font-size: 0.75em;
                margin: 3px;
            }

            .btn-game {
                padding: 10px 20px;
                font-size: 0.9em;
            }

            .btn {
                padding: 6px 15px;
                font-size: 12px;
            }

            .progress-bar {
                height: 15px;
                margin: 10px 0;
            }

            .progress-fill {
                font-size: 9px;
            }

            .action-buttons {
                margin-top: 10px;
                gap: 8px;
            }
        }

        @media (max-height: 700px) {
            .main-container {
                max-height: 600px;
            }
        }

        /* Loading Animation */
        .loading-dots {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 11px;
        }

        .loading-dots div {
            position: absolute;
            top: 0;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #ffffff;
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }

        .loading-dots div:nth-child(1) {
            left: 8px;
            animation: dots1 0.6s infinite;
        }

        .loading-dots div:nth-child(2) {
            left: 8px;
            animation: dots2 0.6s infinite;
        }

        .loading-dots div:nth-child(3) {
            left: 32px;
            animation: dots2 0.6s infinite;
        }

        .loading-dots div:nth-child(4) {
            left: 56px;
            animation: dots3 0.6s infinite;
        }

        @keyframes dots1 {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes dots3 {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(0);
            }
        }

        @keyframes dots2 {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(24px, 0);
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-code" style="left: 10%; animation-delay: 0s;">{ function: debug }</div>
        <div class="floating-code" style="left: 20%; animation-delay: 3s;">while(server.down) { wait() }</div>
        <div class="floating-code" style="left: 30%; animation-delay: 6s;">console.log('fixing...')</div>
        <div class="floating-code" style="left: 40%; animation-delay: 9s;">if(bug) { fixBug() }</div>
        <div class="floating-code" style="left: 50%; animation-delay: 12s;">git commit -m "fix all bugs"</div>
        <div class="floating-code" style="left: 60%; animation-delay: 15s;">npm install magic</div>
        <div class="floating-code" style="left: 70%; animation-delay: 18s;">return true;</div>
        <div class="floating-code" style="left: 80%; animation-delay: 21s;">try { upgrade() }</div>
        <div class="floating-code" style="left: 90%; animation-delay: 24s;">server.restart()</div>
    </div>

    <div class="main-container">
        <!-- Maintenance Section -->
        <div class="maintenance-section">
            <!-- Robot Animation -->
            <div class="robot-container">
                <div class="robot">
                    <div class="robot-eyes">
                        <div class="eye"></div>
                        <div class="eye"></div>
                    </div>
                    <div class="robot-mouth"></div>
                </div>
                <div class="wrench">🔧</div>
            </div>

            <h1>Sistem Sedang Maintenance 🛠️</h1>

            <div class="message">
                <p>Sistem kami sedang <strong>upgrade otak</strong> dan <strong>perbaikan bug-bug nakal</strong>.
                   Biar makin cepat dan makin canggih!</p>
                <p style="margin-top: 8px;">
                    <span class="coffee-animation">☕</span> Sambil menunggu, yuk main game untuk ngusir boring!
                </p>
            </div>

            <div class="time-estimate">
                <i class="fas fa-clock"></i> Estimasi <strong>30 menit</strong>
                <div class="loading-dots">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>

            <button class="btn btn-game" onclick="showGame()">
                <i class="fas fa-gamepad"></i> Main Game Tangkap Bug 🎮
            </button>

            <!-- Contact Info -->
            <div class="contact-info" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="color: #666; margin-bottom: 15px; font-size: 0.9em;">
                    <i class="fas fa-info-circle"></i> Butuh bantuan?
                </p>
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="mailto:support@umk.ac.id" style="color: #285aa6; text-decoration: none; font-weight: 600; padding: 8px 15px; border-radius: 20px; background: rgba(40, 90, 166, 0.1); transition: all 0.3s ease; font-size: 0.85em;">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                    <a target="_blank" href="https://wa.me/628152010029" style="color: #285aa6; text-decoration: none; font-weight: 600; padding: 8px 15px; border-radius: 20px; background: rgba(40, 90, 166, 0.1); transition: all 0.3s ease; font-size: 0.85em;">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Game Section -->
        <div class="game-section" id="gameSection">
            <div class="game-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="game-title" style="margin-bottom: 0;">🎮 Tangkap Bug</h2>
                    <button class="btn btn-back" onclick="hideGame()">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>

                <p style="color: #666; margin-bottom: 15px; font-size: 0.9em;">Klik bug-bug untuk membantu percepat maintenance!</p>

                <div class="progress-bar">
                    <div class="progress-fill" id="progressBar" style="width: 0%;">
                        Progress: 0%
                    </div>
                </div>

                <div class="game-area" id="gameArea">
                    <!-- Bugs will be dynamically added here -->
                </div>

                <div class="game-stats">
                    <div class="stat-item">
                        <i class="fas fa-bug"></i> Bug: <span id="bugsFound">0</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-clock"></i> <span id="gameTime">0</span>s
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-trophy"></i> <span id="gameScore">0</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="startGame()">
                        <i class="fas fa-play"></i> Mulai
                    </button>
                    <button class="btn btn-secondary" onclick="resetGame()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Game Variables
        let gameActive = false;
        let bugsFound = 0;
        let gameScore = 0;
        let gameTime = 0;
        let gameTimer = null;
        let bugSpawnTimer = null;
        let gameTimeInterval = null;

        // Bug Emojis
        const bugEmojis = ['🐛', '🦠', '🐜', '🪲', '🦂', '🕷️', '🦗', '🦟'];

        // UI Functions
        function showGame() {
            document.getElementById('gameSection').classList.add('active');
            document.querySelector('.maintenance-section').style.display = 'none';
            showNotification('🎮 Game dimulai! Tangkap semua bug!', 'success');
        }

        function hideGame() {
            document.getElementById('gameSection').classList.remove('active');
            document.querySelector('.maintenance-section').style.display = 'flex';
            if (gameActive) {
                endGame(false);
            }
        }

        // Game Functions
        function startGame() {
            if (gameActive) return;

            gameActive = true;
            bugsFound = 0;
            gameScore = 0;
            gameTime = 0;
            updateStats();

            // Start spawning bugs
            spawnBug();
            bugSpawnTimer = setInterval(spawnBug, 2000);

            // Start game timer
            gameTimeInterval = setInterval(() => {
                gameTime++;
                document.getElementById('gameTime').textContent = gameTime;
            }, 1000);
        }

        function spawnBug() {
            if (!gameActive) return;

            const gameArea = document.getElementById('gameArea');
            const bug = document.createElement('div');
            bug.className = 'bug';
            bug.innerHTML = bugEmojis[Math.floor(Math.random() * bugEmojis.length)];

            // Random position (adjusted for smaller bug size)
            const bugSize = 35;
            const maxX = gameArea.offsetWidth - bugSize;
            const maxY = gameArea.offsetHeight - bugSize;
            const randomX = Math.random() * maxX;
            const randomY = Math.random() * maxY;

            bug.style.left = randomX + 'px';
            bug.style.top = randomY + 'px';

            // Click handler
            bug.addEventListener('click', function() {
                catchBug(this);
            });

            gameArea.appendChild(bug);

            // Remove bug after 3 seconds if not clicked
            setTimeout(() => {
                if (bug.parentNode) {
                    bug.remove();
                }
            }, 3000);
        }

        function catchBug(bug) {
            if (!gameActive) return;

            // Add animation
            bug.classList.add('bug-clicked');

            // Update stats
            bugsFound++;
            gameScore += 10;
            updateStats();
            updateProgress();

            // Remove bug
            setTimeout(() => {
                bug.remove();
            }, 500);

            // Check win condition
            if (bugsFound >= 10) {
                endGame(true);
            }
        }

        function updateStats() {
            document.getElementById('bugsFound').textContent = bugsFound;
            document.getElementById('gameScore').textContent = gameScore;
        }

        function updateProgress() {
            const progress = Math.min((bugsFound / 10) * 100, 100);
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = progress + '%';
            progressBar.textContent = 'Progress: ' + Math.round(progress) + '%';
        }

        function endGame(won = false) {
            gameActive = false;

            // Clear timers
            clearInterval(bugSpawnTimer);
            clearInterval(gameTimeInterval);

            // Clear remaining bugs
            const gameArea = document.getElementById('gameArea');
            const bugs = gameArea.querySelectorAll('.bug');
            bugs.forEach(bug => bug.remove());

            // Show result
            if (won) {
                showNotification(`🎉 Selamat! Kamu menang dengan skor ${gameScore}!`, 'success');
                updateProgress();
            } else {
                showNotification(`Game selesai! Skor kamu: ${gameScore}`, 'info');
            }
        }

        function resetGame() {
            endGame(false);
            bugsFound = 0;
            gameScore = 0;
            gameTime = 0;
            updateStats();
            updateProgress();
            showNotification('Game di-reset!', 'info');
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #00b894, #00cec9)' : 'linear-gradient(135deg, #74b9ff, #0984e3)'};
                color: white;
                padding: 15px 25px;
                border-radius: 25px;
                font-weight: 600;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                max-width: 300px;
            `;
            notification.textContent = message;

            // Add slide in animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Auto refresh every 5 minutes
        setInterval(() => {
            location.reload();
        }, 300000);

        // Page visibility change handler
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, pause game
                if (gameActive) {
                    endGame(false);
                }
            }
        });

        // Initial message
        window.addEventListener('load', function() {
            setTimeout(() => {
                showNotification('👋 Halo! Selamat datang di halaman maintenance!', 'info');
            }, 1000);
        });

        // Add some fun interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to robot
            const robot = document.querySelector('.robot');
            robot.addEventListener('click', function() {
                this.style.animation = 'bounce 0.5s ease-in-out';
                setTimeout(() => {
                    this.style.animation = 'bounce 2s infinite ease-in-out';
                }, 500);
                showNotification('Robot sedang bekerja keras! 🤖', 'info');
            });

            // Add hover effect to container
            const container = document.querySelector('.container');
            container.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
            });
            container.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>