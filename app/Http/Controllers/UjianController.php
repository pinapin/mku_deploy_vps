<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Pilihan;
use App\Models\SesiUjian;
use App\Models\JawabanMahasiswa;
use App\Jobs\ScoreExamJob;
use App\Services\UrlEncryptionService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UjianController extends Controller
{
    protected $nim;

    public function __construct()
    {
        $this->nim = Session::get('kode');
    }

    /**
     * Get cached exam questions with options
     */
    private function getCachedExamQuestions($ujianId)
    {
        $cacheKey = "ujian_questions_{$ujianId}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($ujianId) {
            return Soal::with(['pilihan' => function ($query) {
                $query->orderBy('huruf_pilihan');
            }])
            ->where('id_ujian', $ujianId)
            ->get();
        });
    }

    /**
     * Clear cached exam questions (useful when questions are updated)
     */
    private function clearCachedExamQuestions($ujianId)
    {
        $cacheKey = "ujian_questions_{$ujianId}";
        Cache::forget($cacheKey);
    }

    /**
     * Store batch answers
     */
    public function submitBatchAnswers(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.id_soal' => 'required|exists:soal,id',
            'answers.*.id_pilihan' => 'required|exists:pilihan,id'
        ]);

        $nim = $this->nim;

        if (!$nim) {
            return response()->json([
                'success' => false,
                'message' => 'Session anda telah habis'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $answers = $request->answers;
            $batchData = [];

            // Get the first answer to determine the exam session
            $firstAnswer = $answers[0];
            $soal = Soal::findOrFail($firstAnswer['id_soal']);
            $sesiUjian = SesiUjian::where('id_ujian', $soal->id_ujian)
                ->where('nim', $nim)
                ->where('status', 'berlangsung')
                ->firstOrFail();

            // Prepare batch data
            foreach ($answers as $answer) {
                // Validate that all answers belong to the same exam
                $answerSoal = Soal::findOrFail($answer['id_soal']);
                if ($answerSoal->id_ujian !== $soal->id_ujian) {
                    throw new \Exception('Jawaban dari ujian yang berbeda tidak diperbolehkan');
                }

                $pilihan = Pilihan::where('id_soal', $answer['id_soal'])
                    ->where('id', $answer['id_pilihan'])
                    ->firstOrFail();

                $batchData[] = [
                    'id_sesi' => $sesiUjian->id,
                    'id_soal' => $answer['id_soal'],
                    'id_pilihan_dipilih' => $answer['id_pilihan'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Delete existing answers for these questions and insert new ones
            $soalIds = array_column($batchData, 'id_soal');
            JawabanMahasiswa::where('id_sesi', $sesiUjian->id)
                ->whereIn('id_soal', $soalIds)
                ->delete();

            // Insert batch answers
            JawabanMahasiswa::insert($batchData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($batchData) . ' jawaban berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch answer submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jawaban: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $nim = $this->nim;

        // Additional check to ensure nim exists
        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session anda telah habis, silakan login kembali');
        }

        // Get all active exams
        $allUjians = Ujian::where('is_active', true)->get();

        // Load cached questions for each exam
        $allUjians->each(function ($ujian) {
            $questions = $this->getCachedExamQuestions($ujian->id);
            $ujian->setRelation('soal', $questions);
        });

        // Pre-load user's exam sessions
        $userExamSessions = SesiUjian::where('nim', $nim)
            ->with(['ujian'])
            ->get()
            ->keyBy('id_ujian');

        // Process each exam to determine status
        $ujians = $allUjians->map(function ($ujian) use ($userExamSessions) {
            $session = $userExamSessions->get($ujian->id);

            // Determine status
            if ($session) {
                if ($session->status === 'selesai') {
                    $status = 'completed';
                    $statusText = 'Selesai';
                    $statusClass = 'success';
                } elseif ($session->status === 'berlangsung' && !$session->waktu_selesai) {
                    $status = 'ongoing';
                    $statusText = 'Sedang Berlangsung';
                    $statusClass = 'warning';
                } else {
                    $status = 'completed';
                    $statusText = 'Selesai';
                    $statusClass = 'success';
                }
            } else {
                $status = 'available';
                $statusText = 'Tersedia';
                $statusClass = 'primary';
            }

            // Check if there's an ongoing session for this exam
            $ongoingSession = SesiUjian::where('id_ujian', $ujian->id)
                ->where('nim', $this->nim)
                ->where('waktu_selesai', null)
                ->first();

            // Get answered question count for this exam session
            $answeredCount = 0;
            $correctCount = 0;
            $score = 0;

            if ($session) {
                $answeredCount = JawabanMahasiswa::where('id_sesi', $session->id)
                    ->count();

                // Calculate correct answers and score for completed exams
                if ($session->status === 'selesai') {
                    $correctCount = JawabanMahasiswa::where('id_sesi', $session->id)
                        ->with('pilihanDipilih')
                        ->get()
                        ->filter(function ($jawaban) {
                            return $jawaban->isBenar();
                        })
                        ->count();

                    $totalQuestions = $ujian->soal ? $ujian->soal->count() : 0;
                    $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;
                }
            }

            return (object) [
                'id' => $ujian->id,
                'encrypted_id' => UrlEncryptionService::encryptId($ujian->id),
                'nama_ujian' => $ujian->nama_ujian,
                'durasi_menit' => $ujian->durasi_menit,
                'deskripsi' => $ujian->deskripsi,
                'is_active' => $ujian->is_active,
                'status' => $status,
                'statusText' => $statusText,
                'statusClass' => $statusClass,
                'sesiUjian' => $session,
                'ongoingSession' => $ongoingSession,
                'ongoingSession_encrypted' => $ongoingSession ? UrlEncryptionService::encryptId($ongoingSession->id) : null,
                'sesiUjian_encrypted' => $session ? UrlEncryptionService::encryptId($session->id) : null,
                'total_soal' => $ujian->soal ? $ujian->soal->count() : 0,
                'answered_count' => $answeredCount,
                'correct_count' => $correctCount,
                'score' => $score,
                'created_at' => $ujian->created_at
            ];
        });

        return view('pages.mahasiswa.ujian.index', compact('ujians'));
    }

    public function startExam($id_ujian)
    {
        // This method is deprecated - use startExamEncrypted instead
        return redirect()->route('ujian.index')
            ->with('error', 'Akses tidak valid. Silakan gunakan tombol yang tersedia.');
    }

    /**
     * Start exam with encrypted ID parameter
     */
    public function startExamEncrypted($encryptedId)
    {
        $nim = $this->nim;

        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session anda telah habis, silakan login kembali');
        }

        // Decrypt the exam ID
        $id_ujian = UrlEncryptionService::decryptId($encryptedId);

        if (!$id_ujian) {
            return redirect()->route('ujian.index')
                ->with('error', 'Parameter ujian tidak valid');
        }

        $ujian = Ujian::findOrFail($id_ujian);

        if (!$ujian->is_active) {
            return redirect()->route('ujian.index')
                ->with('error', 'Ujian tidak aktif');
        }

        // Check for existing ongoing session
        $existingSession = SesiUjian::where('id_ujian', $id_ujian)
            ->where('nim', $nim)
            ->where('status', 'berlangsung')
            ->first();

        if ($existingSession) {
            return redirect()->route('ujian.show', UrlEncryptionService::encryptId($existingSession->id));
        }

        // Create new session
        $sesiUjian = SesiUjian::create([
            'id_ujian' => $id_ujian,
            'nim' => $nim,
            'waktu_mulai' => now(),
            'status' => 'berlangsung'
        ]);

        return redirect()->route('ujian.show', UrlEncryptionService::encryptId($sesiUjian->id));
    }

    public function show($id_sesi)
    {
        // This method is deprecated - use showEncrypted instead
        return redirect()->route('ujian.index')
            ->with('error', 'Akses tidak valid. Silakan gunakan tombol yang tersedia.');
    }

    /**
     * Show exam page with encrypted session ID parameter
     */
    public function showEncrypted($encryptedId)
    {
        $nim = $this->nim;

        // Additional check to ensure nim exists
        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session anda telah habis, silakan login kembali');
        }

        // Decrypt the session ID
        $id_sesi = UrlEncryptionService::decryptId($encryptedId);

        if (!$id_sesi) {
            return redirect()->route('ujian.index')
                ->with('error', 'Parameter sesi ujian tidak valid');
        }

        // Find the session and validate user ownership
        $sesiUjian = SesiUjian::with(['ujian'])
            ->where('id', $id_sesi)
            ->where('nim', $nim)
            ->firstOrFail();

        $ujian = $sesiUjian->ujian;

        // Get cached questions with options
        $questions = $this->getCachedExamQuestions($ujian->id);

        if (!$ujian->is_active) {
            return redirect()->route('ujian.index')
                ->with('error', 'Ujian tidak aktif');
        }

        if ($questions->count() === 0) {
            return redirect()->route('ujian.index')
                ->with('error', 'Ujian belum memiliki soal');
        }

        // Check if exam is already completed
        if ($sesiUjian->status !== 'berlangsung') {
            return redirect()->route('ujian.index')
                ->with('error', 'Ujian telah selesai atau kadaluarsa');
        }

        // Calculate remaining time manually
        $now = now();
        $endTime = $sesiUjian->waktu_mulai->copy()->addMinutes($sesiUjian->ujian->durasi_menit);
        $remainingTime = max(0, $now->diffInSeconds($endTime, false));

        // Check if exam has expired
        if ($remainingTime <= 0) {
            $this->completeExamSilently($sesiUjian);
            return redirect()->route('ujian.index')
                ->with('info', 'Waktu ujian telah habis. Ujian telah disimpan secara otomatis.');
        }

        $jawabanMahasiswa = JawabanMahasiswa::where('id_sesi', $sesiUjian->id)
            ->pluck('id_pilihan_dipilih', 'id_soal');

        return view('pages.mahasiswa.ujian.show', compact(
            'ujian',
            'sesiUjian',
            'remainingTime',
            'jawabanMahasiswa',
            'questions'
        ));
    }

    public function submitAnswer(Request $request)
    {
        $request->validate([
            'id_soal' => 'required|exists:soal,id',
            'id_pilihan' => 'required|exists:pilihan,id'
        ]);

        $nim = $this->nim;

        // Check if nim exists
        if (!$nim) {
            return response()->json([
                'success' => false,
                'message' => 'Session anda telah habis'
            ], 401);
        }

        $soal = Soal::findOrFail($request->id_soal);
        $sesiUjian = SesiUjian::where('id_ujian', $soal->id_ujian)
            ->where('nim', $nim)
            ->where('status', 'berlangsung')
            ->firstOrFail();

        $pilihan = Pilihan::where('id_soal', $request->id_soal)
            ->where('id', $request->id_pilihan)
            ->firstOrFail();

        JawabanMahasiswa::updateOrCreate(
            [
                'id_sesi' => $sesiUjian->id,
                'id_soal' => $request->id_soal
            ],
            [
                'id_pilihan_dipilih' => $request->id_pilihan
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan'
        ]);
    }

    public function finishExam(Request $request)
    {
        $nim = $this->nim;

        // Check if nim exists
        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session anda telah habis, silakan login kembali');
        }

        $sesiUjian = SesiUjian::where('nim', $nim)
            ->where('status', 'berlangsung')
            ->firstOrFail();

        return $this->completeExam($sesiUjian);
    }

    public function timeoutSubmit(Request $request)
    {
        $nim = $this->nim;

        // Check if nim exists
        if (!$nim) {
            return response()->json([
                'success' => false,
                'message' => 'Session anda telah habis'
            ], 401);
        }

        $sesiUjian = SesiUjian::where('nim', $nim)
            ->where('status', 'berlangsung')
            ->first();

        if ($sesiUjian) {
            $this->completeExamSilently($sesiUjian);
        }

        return response()->json([
            'success' => true,
            'message' => 'Waktu habis! Ujian telah disimpan otomatis.',
            'redirect' => route('ujian.index')
        ]);
    }

    private function completeExamSilently(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->status !== 'berlangsung') {
            return;
        }

        // Set finish time immediately, but let the job handle scoring
        $sesiUjian->update([
            'waktu_selesai' => now()
        ]);

        // Dispatch scoring job to background queue
        ScoreExamJob::dispatch($sesiUjian)
            ->onQueue('ujian_scoring');
    }

    private function completeExam(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->status !== 'berlangsung') {
            return redirect()->route('ujian.result', UrlEncryptionService::encryptId($sesiUjian->id));
        }

        // Set finish time immediately, but let the job handle scoring
        $sesiUjian->update([
            'waktu_selesai' => now()
        ]);

        // Dispatch scoring job to background queue
        ScoreExamJob::dispatch($sesiUjian)
            ->onQueue('ujian_scoring');

        // Return JSON response for AJAX request
        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil diselesaikan! Nilai sedang diproses dan akan segera tersedia.',
            'scoring_queued' => true
        ]);
    }

    public function result($id_sesi)
    {
        // This method is deprecated - use resultEncrypted instead
        return redirect()->route('ujian.index')
            ->with('error', 'Akses tidak valid. Silakan gunakan tombol yang tersedia.');
    }

    /**
     * Show exam result with encrypted session ID parameter
     */
    public function resultEncrypted($encryptedId)
    {
        $nim = $this->nim;

        // Additional check to ensure nim exists
        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session anda telah habis, silakan login kembali');
        }

        // Decrypt the session ID
        $id_sesi = UrlEncryptionService::decryptId($encryptedId);

        if (!$id_sesi) {
            return redirect()->route('ujian.index')
                ->with('error', 'Parameter sesi ujian tidak valid');
        }

        $sesiUjian = SesiUjian::with([
            'ujian.soal.pilihan',
            'jawabanMahasiswa.soal.pilihan',
            'jawabanMahasiswa.pilihanDipilih'
        ])
            ->where('id', $id_sesi)
            ->where('nim', $nim)
            ->firstOrFail();

        $results = [];
        if ($sesiUjian->ujian) {
            foreach ($sesiUjian->ujian->soal->sortBy('nomor_soal') as $soal) {
                $jawaban = $sesiUjian->jawabanMahasiswa
                    ->where('id_soal', $soal->id)
                    ->first();

                $results[] = [
                    'soal' => $soal,
                    'jawaban' => $jawaban,
                    'benar' => $jawaban ? $jawaban->isBenar() : false
                ];
            }
        }

        return view('pages.mahasiswa.ujian.result', compact('sesiUjian', 'results'));
    }

    /**
     * Generate encrypted URL for AJAX requests
     */
    public function generateEncryptedUrl(Request $request)
    {
        $nim = $this->nim;

        if (!$nim) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak valid'
            ], 401);
        }

        $request->validate([
            'exam_id' => 'required|integer',
            'action' => 'required|string|in:start,result'
        ]);

        $examId = $request->exam_id;
        $action = $request->action;

        try {
            // Validate exam exists and user has access
            $ujian = Ujian::findOrFail($examId);

            if ($action === 'start') {
                // Generate encrypted URL for starting exam
                $encryptedId = UrlEncryptionService::encryptId($examId);
                $encryptedUrl = route('ujian.start', $encryptedId);
            } elseif ($action === 'result') {
                // Find session and generate encrypted URL for result
                $sesiUjian = SesiUjian::where('id_ujian', $examId)
                    ->where('nim', $nim)
                    ->where('status', 'selesai')
                    ->first();

                if (!$sesiUjian) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada sesi ujian yang selesai ditemukan'
                    ]);
                }

                $encryptedId = UrlEncryptionService::encryptId($sesiUjian->id);
                $encryptedUrl = route('ujian.result', $encryptedId);
            }

            return response()->json([
                'success' => true,
                'encrypted_url' => $encryptedUrl,
                'message' => 'URL terenkripsi berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat URL terenkripsi'
            ]);
        }
    }

    function getUjianApi()
    {
        // // Get all active exams with soal relationship
        // $allUjians = Ujian::where('is_active', true)
        //     ->with('soal')
        //     ->get();

        $hi = 'Hello World';

        return response()->json([
            'success' => true,
            'data' => $hi
        ]);
    }
}
