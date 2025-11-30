<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Jawaban - {{ $sesiUjian->ujian->nama_ujian ?? 'Ujian' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            background: #f5f5f5;
        }
        .review-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .info-section {
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
            margin-bottom: 10px;
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
        .option.selected {
            background: #fff3cd;
            border-color: #ffc107;
            font-weight: bold;
        }
        .option.correct {
            background: #d4edda;
            border-color: #c3e6cb;
        }
        .option.selected.correct {
            background: #d4edda;
            border-color: #28a745;
            font-weight: bold;
        }
        .option-label {
            font-weight: bold;
            margin-right: 10px;
        }
        .essay-answer {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-height: 60px;
            white-space: pre-wrap;
        }
        .result-indicator {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            margin-left: 10px;
        }
        .result-correct {
            background: #d4edda;
            color: #155724;
        }
        .result-wrong {
            background: #f8d7da;
            color: #721c24;
        }
        .result-essay {
            background: #d1ecf1;
            color: #0c5460;
        }
        .score-summary {
            background: #d4edda;
            padding: 20px;
            margin-top: 30px;
            border-radius: 5px;
            text-align: center;
        }
        .score-summary h2 {
            margin: 0;
            color: #155724;
        }
        @media print {
            .review-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="header">
            <h1>{{ $sesiUjian->ujian->nama_ujian ?? 'Review Ujian' }}</h1>
            <p>Mode Review - Jawaban Mahasiswa</p>
        </div>

        <div class="info-section">
            <strong>Informasi Mahasiswa:</strong><br>
            NIM: {{ $sesiUjian->nim ?? '-' }}<br>
            Nama: {{ $sesiUjian->mahasiswa->nama ?? 'Unknown' }}<br>
            Waktu Mulai: {{ $sesiUjian->waktu_mulai->format('d/m/Y H:i:s') }}<br>
            Waktu Selesai: {{ $sesiUjian->waktu_selesai ? $sesiUjian->waktu_selesai->format('d/m/Y H:i:s') : 'Belum selesai' }}<br>
            Status: {{ $sesiUjian->status ?? '-' }}<br>
            Skor Akhir: {{ $sesiUjian->skor_akhir ?? 0 }}%
        </div>

        @if(isset($reviewData) && count($reviewData) > 0)
            @foreach($reviewData as $answerData)
                <div class="question">
                    <div class="question-number">
                        Soal {{ $answerData['soal']->nomor_soal }}
                        @if($answerData['isCorrect'])
                            <span class="result-indicator result-correct">✓ Benar</span>
                        @elseif($answerData['soal']->tipe == 'pilihan_ganda')
                            <span class="result-indicator result-wrong">✗ Salah</span>
                        @else
                            <span class="result-indicator result-essay">? Essay</span>
                        @endif
                    </div>
                    <div class="question-text">
                        {!! nl2br(e($answerData['soal']->teks_soal)) !!}
                    </div>

                    @if($answerData['soal']->tipe == 'pilihan_ganda')
                        <div class="options">
                            @foreach($answerData['allOptions'] as $pilihan)
                                <div class="option
                                    @if($pilihan->is_benar) correct @endif
                                    @if($answerData['jawaban'] && $answerData['jawaban']->id_pilihan_dipilih == $pilihan->id) selected @endif">
                                    <span class="option-label">{{ $pilihan->huruf_pilihan }}.</span>
                                    {{ $pilihan->teks_pilihan }}
                                    @if($pilihan->is_benar)
                                        <span style="color: #155724; font-weight: bold;"> ✓ (Kunci Jawaban)</span>
                                    @endif
                                    @if($answerData['jawaban'] && $answerData['jawaban']->id_pilihan_dipilih == $pilihan->id && !$pilihan->is_benar)
                                        <span style="color: #721c24; font-weight: bold;"> ✗ (Jawaban Mahasiswa)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="essay-answer">
                            @if($answerData['jawaban'] && $answerData['jawaban']->jawaban_essay)
                                {{ $answerData['jawaban']->jawaban_essay }}
                            @else
                                <em style="color: #6c757d;">Tidak ada jawaban</em>
                            @endif
                        </div>
                        @if($answerData['soal']->kunci_jawaban)
                            <p><strong>Kunci Jawaban (Referensi):</strong></p>
                            <div class="essay-answer">
                                {{ $answerData['soal']->kunci_jawaban }}
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach

            <div class="score-summary">
                <h2>Total Skor: {{ $sesiUjian->skor_akhir ?? 0 }}%</h2>
                <p>Benar: {{ collect($reviewData)->filter(function($a) { return $a['isCorrect']; })->count() }} dari {{ count($reviewData) }} soal</p>
            </div>
        @else
            <div style="text-align: center; color: #666; padding: 40px;">
                <h3>Belum ada jawaban untuk direview</h3>
                <p>Mahasiswa ini belum menjawab soal ujian.</p>
            </div>
        @endif

        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>

    <script>
        // Auto-print untuk mode review (optional)
        // window.print();

        // Close window after printing
        window.onafterprint = function() {
            // window.close();
        };
    </script>
</body>
</html>