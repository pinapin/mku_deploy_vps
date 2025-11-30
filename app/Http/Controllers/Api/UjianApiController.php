<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Pilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UjianApiController extends Controller
{
    /**
     * Get all active exams with basic info
     * GET /api/ujian
     */
    public function index()
    {
        try {
            $ujians = Ujian::where('is_active', true)
                ->select('id', 'nama_ujian', 'deskripsi', 'durasi_menit', 'created_at')
                ->withCount(['soal'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ujians,
                'message' => 'Data ujian berhasil diambil'
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Ujian Index Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data ujian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam questions by exam ID
     * GET /api/ujian/{id}/questions
     */
    public function getQuestions($id)
    {
        try {
            // Validate exam exists and is active
            $ujian = Ujian::where('id', $id)
                ->where('is_active', true)
                ->first();

            if (!$ujian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian tidak ditemukan atau tidak aktif'
                ], 404);
            }

            // Use caching for performance
            $cacheKey = "api_ujian_questions_{$id}";
            $questions = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Soal::where('id_ujian', $id)
                    ->with(['pilihan' => function ($query) {
                        $query->orderBy('huruf_pilihan');
                    }])
                    ->orderBy('nomor_soal')
                    ->get();
            });

            if ($questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian belum memiliki soal'
                ], 404);
            }

            // Format response untuk k6 testing
            $formattedQuestions = $questions->map(function ($soal) {
                return [
                    'id' => $soal->id,
                    'nomor_soal' => $soal->nomor_soal,
                    'pertanyaan' => $soal->pertanyaan,
                    'tipe_soal' => $soal->tipe_soal,
                    'pilihan' => $soal->pilihan->map(function ($pilihan) {
                        return [
                            'id' => $pilihan->id,
                            'huruf_pilihan' => $pilihan->huruf_pilihan,
                            'teks_pilihan' => $pilihan->teks_pilihan,
                            'is_benar' => $pilihan->is_benar // Include untuk testing
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'ujian' => [
                        'id' => $ujian->id,
                        'nama_ujian' => $ujian->nama_ujian,
                        'deskripsi' => $ujian->deskripsi,
                        'durasi_menit' => $ujian->durasi_menit,
                        'total_soal' => $questions->count()
                    ],
                    'questions' => $formattedQuestions
                ],
                'message' => 'Data soal ujian berhasil diambil'
            ], 200);

        } catch (\Exception $e) {
            Log::error("API Ujian Questions Error (ID: {$id}): " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data soal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get random question for load testing
     * GET /api/ujian/random/question
     */
    public function getRandomQuestion()
    {
        try {
            // Get random active exam
            $randomExam = Ujian::with('soal.pilihan')
                ->where('id', 2)
                ->get();

            if ($randomExam->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian belum memiliki soal'
                ], 404);
            }

          

            return response()->json([
                'success' => true,
                'data' => $randomExam->toArray(),
                'message' => 'Random question berhasil diambil'
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Random Question Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil random question',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam statistics
     * GET /api/ujian/stats
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_aktif' => Ujian::where('is_active', true)->count(),
                'total_soal' => Soal::count(),
                'total_pilihan' => Pilihan::count(),
                'avg_soal_per_ujian' => Ujian::where('is_active', true)
                    ->withCount('soal')
                    ->avg('soal_count') ?? 0
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistik ujian berhasil diambil'
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Ujian Stats Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Health check endpoint
     * GET /api/ujian/health
     */
    public function health()
    {
        try {
            $dbConnection = DB::connection()->getPdo() ? true : false;
            $cacheConnection = Cache::store('redis')->get('test_key') !== null ? true : false;

            return response()->json([
                'success' => true,
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'services' => [
                    'database' => $dbConnection ? 'connected' : 'disconnected',
                    'cache' => $cacheConnection ? 'connected' : 'disconnected'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Bulk get questions for multiple exams
     * GET /api/ujian/bulk/questions?exam_ids=1,2,3
     */
    public function getBulkQuestions(Request $request)
    {
        try {
            $examIds = $request->get('exam_ids');

            if (!$examIds) {
                return response()->json([
                    'success' => false,
                    'message' => 'exam_ids parameter is required'
                ], 400);
            }

            $examIdArray = explode(',', $examIds);
            $examIdArray = array_map('intval', $examIdArray);

            // Validate all exams exist and are active
            $ujians = Ujian::whereIn('id', $examIdArray)
                ->where('is_active', true)
                ->get();

            if ($ujians->count() !== count($examIdArray)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some exams not found or not active'
                ], 404);
            }

            $allQuestions = [];
            foreach ($ujians as $ujian) {
                $cacheKey = "api_ujian_questions_{$ujian->id}";
                $questions = Cache::remember($cacheKey, 3600, function () use ($ujian) {
                    return Soal::where('id_ujian', $ujian->id)
                        ->with(['pilihan' => function ($query) {
                            $query->orderBy('huruf_pilihan');
                        }])
                        ->orderBy('nomor_soal')
                        ->get();
                });

                $allQuestions[$ujian->id] = [
                    'ujian' => [
                        'id' => $ujian->id,
                        'nama_ujian' => $ujian->nama_ujian,
                        'total_soal' => $questions->count()
                    ],
                    'questions' => $questions->map(function ($soal) {
                        return [
                            'id' => $soal->id,
                            'nomor_soal' => $soal->nomor_soal,
                            'pertanyaan' => $soal->pertanyaan,
                            'tipe_soal' => $soal->tipe_soal,
                            'pilihan' => $soal->pilihan->map(function ($pilihan) {
                                return [
                                    'id' => $pilihan->id,
                                    'huruf_pilihan' => $pilihan->huruf_pilihan,
                                    'teks_pilihan' => $pilihan->teks_pilihan,
                                    'is_benar' => $pilihan->is_benar
                                ];
                            })
                        ];
                    })
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $allQuestions,
                'message' => 'Bulk questions retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Bulk Questions Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bulk questions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}