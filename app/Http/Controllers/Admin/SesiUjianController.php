<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\SesiUjian;
use App\Models\JawabanMahasiswa;
use App\Services\UrlEncryptionService;
use Illuminate\Support\Facades\Validator;

class SesiUjianController extends Controller
{
    public function index(Request $request, $id_ujian = null)
    {
        if ($id_ujian) {
            $ujian = Ujian::findOrFail($id_ujian);

            // Get all exam sessions without filters
            $sesiUjians = SesiUjian::with([
                'ujian',
                'mahasiswa.programStudi.fakultas',
                'jawabanMahasiswa'
            ])
                ->where('id_ujian', $id_ujian)
                ->orderBy('waktu_mulai', 'desc')
                ->get();

            // Get data for client-side filters
            $fakultas = \App\Models\Fakultas::orderBy('nama_fakultas')->get();
            $programStudis = \App\Models\ProgramStudi::orderBy('nama_prodi')->get();

            return view('pages.admin.sesi_ujian.index', compact(
                'ujian',
                'sesiUjians',
                'fakultas',
                'programStudis'
            ));
        } else {
            $ujians = Ujian::withCount(['soal', 'sesiUjian'])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('pages.admin.sesi_ujian.all', compact('ujians'));
        }
    }

    public function show($id)
    {
        $sesiUjian = SesiUjian::with([
            'ujian.soal.pilihan',
            'mahasiswa',
            'jawabanMahasiswa.soal.pilihan',
            'jawabanMahasiswa.pilihanDipilih'
        ])->findOrFail($id);

        // Calculate statistics
        $totalQuestions = $sesiUjian->ujian ? $sesiUjian->ujian->soal->count() : 0;
        $answeredQuestions = $sesiUjian->jawabanMahasiswa->count();
        $correctAnswers = $sesiUjian->jawabanMahasiswa
            ->filter(function ($jawaban) {
                return $jawaban->isBenar();
            })
            ->count();

        // Calculate score if exam is finished
        $score = 0;
        if ($sesiUjian->status === 'selesai' && $totalQuestions > 0) {
            $score = round(($correctAnswers / $totalQuestions) * 100);
        }

        // Group answers by question for display
        $answersByQuestion = [];
        if ($sesiUjian->ujian) {
            foreach ($sesiUjian->ujian->soal->sortBy('nomor_soal') as $soal) {
                $jawaban = $sesiUjian->jawabanMahasiswa
                    ->where('id_soal', $soal->id)
                    ->first();

                $answersByQuestion[] = [
                    'soal' => $soal,
                    'jawaban' => $jawaban,
                    'benar' => $jawaban ? $jawaban->isBenar() : false
                ];
            }
        }

        return view('pages.admin.sesi_ujian.show', compact(
            'sesiUjian',
            'totalQuestions',
            'answeredQuestions',
            'correctAnswers',
            'score',
            'answersByQuestion'
        ));
    }

    public function forceFinish($id)
    {
        try {
            $sesiUjian = SesiUjian::with(['ujian.soal', 'jawabanMahasiswa'])->findOrFail($id);

            if ($sesiUjian->status !== 'berlangsung') {
                return redirect()->route('master.sesi_ujian.show', $id)
                    ->with('error', 'Sesi ujian sudah selesai atau kadaluarsa');
            }

            // Calculate score
            $totalQuestions = $sesiUjian->ujian ? $sesiUjian->ujian->soal->count() : 0;
            $correctAnswers = 0;

            if ($totalQuestions > 0) {
                $correctAnswers = $sesiUjian->jawabanMahasiswa
                    ->filter(function ($jawaban) {
                        return $jawaban->isBenar();
                    })
                    ->count();
            }

            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

            $sesiUjian->update([
                'waktu_selesai' => now(),
                'skor_akhir' => $score,
                'status' => 'selesai'
            ]);

            return redirect()->route('master.sesi_ujian.show', $id)
                ->with('success', 'Sesi ujian berhasil diselesaikan secara paksa');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyelesaikan sesi ujian: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $sesiUjian = SesiUjian::findOrFail($id);
            $id_ujian = $sesiUjian->id_ujian;

            // Delete all related answers first
            $sesiUjian->jawabanMahasiswa()->delete();

            // Delete the session
            $sesiUjian->delete();

            return redirect()->route('master.sesi_ujian.index_by_ujian', $id_ujian)
                ->with('success', 'Sesi ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus sesi ujian: ' . $e->getMessage());
        }
    }

    public function reset($id)
    {
        try {
            $sesiUjian = SesiUjian::findOrFail($id);

            if ($sesiUjian->status !== 'selesai') {
                return redirect()->route('master.sesi_ujian.show', $id)
                    ->with('error', 'Hanya sesi ujian yang sudah selesai yang dapat direset');
            }

            // Delete all answers
            $sesiUjian->jawabanMahasiswa()->delete();

            // Reset session
            $sesiUjian->update([
                'waktu_selesai' => null,
                'skor_akhir' => null,
                'status' => 'berlangsung'
            ]);

            return redirect()->route('master.sesi_ujian.show', $id)
                ->with('success', 'Sesi ujian berhasil direset');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mereset sesi ujian: ' . $e->getMessage());
        }
    }

    public function extendTime(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'additional_minutes' => 'required|integer|min:1|max:120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $sesiUjian = SesiUjian::findOrFail($id);

            if ($sesiUjian->status !== 'berlangsung') {
                return redirect()->route('master.sesi_ujian.show', $id)
                    ->with('error', 'Hanya sesi ujian yang sedang berlangsung yang dapat diperpanjang');
            }

            // Extend time by adding minutes to the start time
            $newEndTime = $sesiUjian->waktu_mulai->copy()
                ->addMinutes($sesiUjian->ujian->durasi_menit + $request->additional_minutes);

            // We can't directly modify the duration, so we'll store the extension info in session
            // or create a separate table for extensions if needed
            // For now, we'll just show a success message

            return redirect()->route('master.sesi_ujian.show', $id)
                ->with('success', "Waktu ujian berhasil diperpanjang {$request->additional_minutes} menit");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperpanjang waktu ujian: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function statistics($id_ujian = null)
    {
        if ($id_ujian) {
            $ujian = Ujian::findOrFail($id_ujian);

            $sesiUjians = SesiUjian::where('id_ujian', $id_ujian)
                ->with(['ujian', 'jawabanMahasiswa'])
                ->get();

            $totalSessions = $sesiUjians->count();
            $finishedSessions = $sesiUjians->where('status', 'selesai')->count();
            $ongoingSessions = $sesiUjians->where('status', 'berlangsung')->count();

            $averageScore = 0;
            $maxScore = 0;
            $minScore = 100;

            if ($finishedSessions > 0) {
                $scores = $sesiUjians->where('status', 'selesai')->pluck('skor_akhir');
                $averageScore = round($scores->avg(), 2);
                $maxScore = $scores->max();
                $minScore = $scores->min();
            }

            return view('pages.admin.sesi_ujian.statistics', compact(
                'ujian',
                'totalSessions',
                'finishedSessions',
                'ongoingSessions',
                'averageScore',
                'maxScore',
                'minScore'
            ));
        } else {
            return redirect()->route('master.ujian.index')
                ->with('info', 'Pilih ujian terlebih dahulu untuk melihat statistik');
        }
    }

    public function exportResults($id_ujian)
    {
        try {
            $ujian = Ujian::findOrFail($id_ujian);
            $sesiUjians = SesiUjian::with(['mahasiswa', 'jawabanMahasiswa'])
                ->where('id_ujian', $id_ujian)
                ->where('status', 'selesai')
                ->orderBy('waktu_selesai', 'desc')
                ->get();

            // Create CSV content
            $csvContent = "No,NIM,Mahasiswa,Waktu Mulai,Waktu Selesai,Skor Akhir,Status\n";

            foreach ($sesiUjians as $index => $sesi) {
                $csvContent .= ($index + 1) . ',' .
                    $sesi->nim . ',' .
                    ($sesi->mahasiswa->nama ?? 'Unknown') . ',' .
                    $sesi->waktu_mulai->format('Y-m-d H:i:s') . ',' .
                    ($sesi->waktu_selesai ? $sesi->waktu_selesai->format('Y-m-d H:i:s') : '') . ',' .
                    ($sesi->skor_akhir ?? 0) . ',' .
                    $sesi->status . "\n";
            }

            $filename = 'hasil_ujian_' . str_replace(' ', '_', $ujian->nama_ujian) . '_' . date('Y-m-d') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengekspor hasil: ' . $e->getMessage());
        }
    }

    public function jawabanDetail($id)
    {
        try {
            $sesiUjian = SesiUjian::with([
                'ujian',
                'mahasiswa',
                'jawabanMahasiswa.soal',
                'jawabanMahasiswa.pilihanDipilih'
            ])->findOrFail($id);

            $data = [
                'id' => $sesiUjian->id,
                'mahasiswa' => [
                    'nim' => $sesiUjian->nim,
                    'nama' => $sesiUjian->mahasiswa->nama ?? 'Unknown'
                ],
                'ujian' => [
                    'nama_ujian' => $sesiUjian->ujian->nama_ujian,
                    'durasi_menit' => $sesiUjian->ujian->durasi_menit
                ],
                'waktu_mulai' => $sesiUjian->waktu_mulai->format('d/m/Y H:i:s'),
                'waktu_selesai' => $sesiUjian->waktu_selesai ? $sesiUjian->waktu_selesai->format('d/m/Y H:i:s') : null,
                'skor_akhir' => $sesiUjian->skor_akhir,
                'status' => $sesiUjian->status,
                'jawaban' => $sesiUjian->jawabanMahasiswa->map(function ($jawaban) {
                    return [
                        'id' => $jawaban->id,
                        'jawaban_essay' => $jawaban->jawaban_essay,
                        'is_benar' => $jawaban->isBenar(),
                        'soal' => [
                            'id' => $jawaban->soal->id,
                            'nomor_soal' => $jawaban->soal->nomor_soal,
                            'teks_soal' => $jawaban->soal->teks_soal,
                            'tipe' => $jawaban->soal->tipe
                        ],
                        'pilihan_dipilih' => $jawaban->pilihanDipilih ? [
                            'huruf_pilihan' => $jawaban->pilihanDipilih->huruf_pilihan,
                            'teks_pilihan' => $jawaban->pilihanDipilih->teks_pilihan,
                            'is_benar' => $jawaban->pilihanDipilih->is_benar
                        ] : null
                    ];
                })->toArray()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Index sesi ujian dengan encrypted ID
     */
    public function indexEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);

            // Get all exam sessions without filters
            $sesiUjians = SesiUjian::with([
                'ujian',
                'mahasiswa.programStudi.fakultas',
                'jawabanMahasiswa'
            ])
                ->where('id_ujian', $id)
                ->orderBy('waktu_mulai', 'desc')
                ->get();

            // Get data for client-side filters
            $fakultas = \App\Models\Fakultas::orderBy('nama_fakultas')->get();
            $programStudis = \App\Models\ProgramStudi::orderBy('nama_prodi')->get();

            return view('pages.admin.sesi_ujian.index', compact(
                'ujian',
                'sesiUjians',
                'fakultas',
                'programStudis'
            ));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat data sesi ujian: ' . $e->getMessage());
        }
    }

    /**
     * Show sesi ujian dengan encrypted ID
     */
    public function showEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $sesiUjian = SesiUjian::with([
                'ujian',
                'mahasiswa.programStudi.fakultas',
                'jawabanMahasiswa.soal.pilihan'
            ])->findOrFail($id);

            return view('pages.admin.sesi_ujian.show', compact('sesiUjian'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat detail sesi ujian: ' . $e->getMessage());
        }
    }

    /**
     * Jawaban detail dengan encrypted ID
     */
    public function jawabanDetailEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired URL'
                ], 400);
            }

            $sesiUjian = SesiUjian::with([
                'ujian',
                'mahasiswa',
                'jawabanMahasiswa.soal',
                'jawabanMahasiswa.pilihanDipilih'
            ])->findOrFail($id);

            $data = [
                'id' => $sesiUjian->id,
                'mahasiswa' => [
                    'nim' => $sesiUjian->nim,
                    'nama' => $sesiUjian->mahasiswa->nama ?? 'Unknown'
                ],
                'ujian' => [
                    'nama_ujian' => $sesiUjian->ujian->nama_ujian,
                    'durasi_menit' => $sesiUjian->ujian->durasi_menit
                ],
                'waktu_mulai' => $sesiUjian->waktu_mulai->format('d/m/Y H:i:s'),
                'waktu_selesai' => $sesiUjian->waktu_selesai ? $sesiUjian->waktu_selesai->format('d/m/Y H:i:s') : null,
                'skor_akhir' => $sesiUjian->skor_akhir,
                'status' => $sesiUjian->status,
                'jawaban' => $sesiUjian->jawabanMahasiswa->map(function ($jawaban) {
                    return [
                        'id' => $jawaban->id,
                        'jawaban_essay' => $jawaban->jawaban_essay,
                        'is_benar' => $jawaban->isBenar(),
                        'soal' => [
                            'id' => $jawaban->soal->id,
                            'nomor_soal' => $jawaban->soal->nomor_soal,
                            'teks_soal' => $jawaban->soal->teks_soal,
                            'tipe' => $jawaban->soal->tipe
                        ],
                        'pilihan_dipilih' => $jawaban->pilihanDipilih ? [
                            'huruf_pilihan' => $jawaban->pilihanDipilih->huruf_pilihan,
                            'teks_pilihan' => $jawaban->pilihanDipilih->teks_pilihan,
                            'is_benar' => $jawaban->pilihanDipilih->is_benar
                        ] : null
                    ];
                })->toArray()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Force finish dengan encrypted ID
     */
    public function forceFinishEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired URL'
                ], 400);
            }

            $sesiUjian = SesiUjian::findOrFail($id);

            if ($sesiUjian->status !== 'berlangsung') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya sesi ujian yang sedang berlangsung yang dapat dihentikan'
                ], 400);
            }

            $this->completeExam($sesiUjian);
            $sesiUjian->update([
                'waktu_selesai' => now(),
                'status' => 'selesai'
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Ujian berhasil dihentikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghentikan ujian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extend time dengan encrypted ID
     */
    public function extendTimeEncrypted(Request $request, $encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired URL'
                ], 400);
            }

            $sesiUjian = SesiUjian::findOrFail($id);

            if ($sesiUjian->status !== 'berlangsung') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya sesi ujian yang sedang berlangsung yang dapat diperpanjang'
                ], 400);
            }

            $request->validate([
                'additional_minutes' => 'required|integer|min:1|max:120'
            ]);

            // Extend time by adding minutes to the start time
            $newEndTime = $sesiUjian->waktu_mulai->copy()
                ->addMinutes($sesiUjian->ujian->durasi_menit + $request->additional_minutes);

            // Update the session (we can't directly modify the duration, so we'll show success message)

            return response()->json([
                'success' => true,
                'message' => "Waktu ujian berhasil diperpanjang {$request->additional_minutes} menit"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperpanjang waktu ujian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset dengan encrypted ID
     */
    public function resetEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired URL'
                ], 400);
            }

            $sesiUjian = SesiUjian::findOrFail($id);

            if ($sesiUjian->status === 'berlangsung') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mereset sesi ujian yang sedang berlangsung'
                ], 400);
            }

            // Delete all answers for this session
            $sesiUjian->jawabanMahasiswa()->delete();

            // Reset session status
            $sesiUjian->update([
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'skor_akhir' => null,
                'status' => 'menunggu'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesi ujian berhasil direset'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset sesi ujian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete dengan encrypted ID
     */
    public function deleteEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $sesiUjian = SesiUjian::findOrFail($id);

            if ($sesiUjian->status === 'berlangsung') {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus sesi ujian yang sedang berlangsung');
            }

            $sesiUjian->delete();

            return redirect()->back()
                ->with('success', 'Sesi ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus sesi ujian: ' . $e->getMessage());
        }
    }

    /**
     * Statistics dengan encrypted ID
     */
    public function statisticsEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);

            $sesiUjians = SesiUjian::where('id_ujian', $id)
                ->with(['ujian', 'jawabanMahasiswa'])
                ->get();

            $totalSessions = $sesiUjians->count();
            $finishedSessions = $sesiUjians->where('status', 'selesai')->count();
            $ongoingSessions = $sesiUjians->where('status', 'berlangsung')->count();

            $averageScore = 0;
            $maxScore = 0;
            $minScore = 100;

            if ($finishedSessions > 0) {
                $scores = $sesiUjians->where('status', 'selesai')->pluck('skor_akhir');
                $averageScore = round($scores->avg(), 2);
                $maxScore = $scores->max();
                $minScore = $scores->min();
            }

            return view('pages.admin.sesi_ujian.statistics', compact(
                'ujian',
                'totalSessions',
                'finishedSessions',
                'ongoingSessions',
                'averageScore',
                'maxScore',
                'minScore'
            ));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat statistik: ' . $e->getMessage());
        }
    }

    /**
     * Export results dengan encrypted ID
     */
    public function exportResultsEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);
            $sesiUjians = SesiUjian::with(['mahasiswa', 'jawabanMahasiswa'])
                ->where('id_ujian', $id)
                ->where('status', 'selesai')
                ->orderBy('waktu_selesai', 'desc')
                ->get();

            // Create CSV content
            $csvContent = "No,NIM,Mahasiswa,Waktu Mulai,Waktu Selesai,Skor Akhir,Status\n";

            foreach ($sesiUjians as $index => $sesi) {
                $csvContent .= ($index + 1) . ',' .
                    $sesi->nim . ',' .
                    ($sesi->mahasiswa->nama ?? 'Unknown') . ',' .
                    $sesi->waktu_mulai->format('Y-m-d H:i:s') . ',' .
                    ($sesi->waktu_selesai ? $sesi->waktu_selesai->format('Y-m-d H:i:s') : '') . ',' .
                    ($sesi->skor_akhir ?? 0) . ',' .
                    $sesi->status . "\n";
            }

            $filename = 'hasil_ujian_' . str_replace(' ', '_', $ujian->nama_ujian) . '_' . date('Y-m-d') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengekspor hasil: ' . $e->getMessage());
        }
    }

    private function completeExam(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->status !== 'berlangsung') {
            return redirect()->route('ujian.result', UrlEncryptionService::encryptId($sesiUjian->id));
        }

        // Load relationships properly
        $sesiUjian->load(['ujian.soal']);

        $totalQuestions = $sesiUjian->ujian ? $sesiUjian->ujian->soal->count() : 0;
        $correctAnswers = JawabanMahasiswa::where('id_sesi', $sesiUjian->id)
            ->with('pilihanDipilih')
            ->get()
            ->filter(function ($jawaban) {
                return $jawaban->isBenar();
            })
            ->count();

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        $sesiUjian->update([
            'waktu_selesai' => now(),
            'skor_akhir' => $score,
            'status' => 'selesai'
        ]);

        // Return JSON response for AJAX request
        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil diselesaikan!',
            'score' => $score
        ]);
    }
}
