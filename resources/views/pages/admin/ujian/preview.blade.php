<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Ujian - {{ $ujian->nama_ujian }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            background: #f5f5f5;
        }
        .exam-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .exam-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .exam-info {
            background: #e3f2fd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .question {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
        }
        .question-number {
            font-weight: bold;
            color: #007bff;
            font-size: 1.1em;
        }
        .question-text {
            margin: 10px 0 15px 0;
            font-size: 1.1em;
        }
        .options {
            margin-left: 20px;
        }
        .option {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .option.correct {
            background: #d4edda;
            border-color: #c3e6cb;
        }
        .option-label {
            font-weight: bold;
            margin-right: 10px;
        }
        .timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.1em;
        }
        .essay-answer {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            font-size: 1em;
            resize: vertical;
        }
        @media print {
            .timer {
                display: none;
            }
            .exam-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="timer" id="timer">
        Waktu: <span id="time-display">{{ $ujian->durasi_menit }}:00</span>
    </div>

    <div class="exam-container">
        <div class="exam-header">
            <h1>{{ $ujian->nama_ujian }}</h1>
            <p>Preview Mode - Ujian tidak akan disimpan</p>
        </div>

        <div class="exam-info">
            <strong>Durasi:</strong> {{ $ujian->durasi_menit }} menit<br>
            <strong>Jumlah Soal:</strong> {{ $ujian->soal->count() }} soal
            @if($ujian->deskripsi)
                <br><strong>Deskripsi:</strong> {{ $ujian->deskripsi }}
            @endif
        </div>

        @if($ujian->soal->count() > 0)
            @foreach($ujian->soal->sortBy('nomor_soal') as $soal)
                <div class="question">
                    <div class="question-number">Soal {{ $soal->nomor_soal }}</div>
                    <div class="question-text">
                        {!! nl2br(e($soal->teks_soal)) !!}
                    </div>

                    @if($soal->tipe == 'pilihan_ganda')
                        <div class="options">
                            @foreach($soal->pilihan->sortBy('huruf_pilihan') as $pilihan)
                                <div class="option {{ $pilihan->is_benar ? 'correct' : '' }}">
                                    <span class="option-label">{{ $pilihan->huruf_pilihan }}.</span>
                                    {{ $pilihan->teks_pilihan }}
                                    @if($pilihan->is_benar)
                                        <span style="color: #155724; font-weight: bold;"> ✓</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <textarea class="essay-answer" placeholder="Jawaban Anda akan muncul di sini..." readonly></textarea>
                    @endif
                </div>
            @endforeach
        @else
            <p style="text-align: center; color: #666;">Belum ada soal untuk ujian ini.</p>
        @endif

        <div style="text-align: center; margin-top: 30px; color: #666;">
            <small>Preview Ujian - Mode ini hanya untuk melihat tampilan ujian</small>
        </div>
    </div>

    <script>
        let totalSeconds = {{ $ujian->durasi_menit }} * 60;
        let currentSeconds = totalSeconds;

        function updateTimer() {
            const minutes = Math.floor(currentSeconds / 60);
            const seconds = currentSeconds % 60;
            document.getElementById('time-display').textContent =
                `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (currentSeconds <= 0) {
                document.getElementById('time-display').textContent = '00:00';
                document.getElementById('time-display').style.color = '#ffeb3b';
                return;
            }

            currentSeconds--;
            setTimeout(updateTimer, 1000);
        }

        // Start timer
        updateTimer();
    </script>
</body>
</html>