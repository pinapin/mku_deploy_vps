<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JawabanMahasiswa;
use App\Models\SesiUjian;
use App\Models\Soal;
use App\Models\Pilihan;
use App\Services\UrlEncryptionService;
use Illuminate\Support\Facades\Validator;

class JawabanMahasiswaController extends Controller
{
    public function index($id_sesi = null)
    {
        if ($id_sesi) {
            $sesiUjian = SesiUjian::with(['ujian', 'mahasiswa'])->findOrFail($id_sesi);
            $jawabanMahasiswas = JawabanMahasiswa::with(['soal.pilihan', 'pilihanDipilih'])
                ->where('id_sesi', $id_sesi)
                ->get();

            return view('pages.admin.jawaban_mahasiswa.index', compact('sesiUjian', 'jawabanMahasiswas'));
        } else {
            $jawabanMahasiswas = JawabanMahasiswa::with([
                'soal.pilihan',
                'pilihanDipilih',
                'sesiUjian.ujian',
                'sesiUjian.mahasiswa'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            return view('pages.admin.jawaban_mahasiswa.all', compact('jawabanMahasiswas'));
        }
    }

    public function show($id)
    {
        $jawabanMahasiswa = JawabanMahasiswa::with([
            'soal.pilihan' => function($query) {
                $query->orderBy('huruf_pilihan');
            },
            'pilihanDipilih',
            'sesiUjian.ujian',
            'sesiUjian.mahasiswa'
        ])->findOrFail($id);

        // Get all options for this question
        $allOptions = $jawabanMahasiswa->soal->pilihan;
        $correctOption = $allOptions->where('is_benar', true)->first();

        return view('pages.admin.jawaban_mahasiswa.show', compact(
            'jawabanMahasiswa',
            'allOptions',
            'correctOption'
        ));
    }

    public function edit($id)
    {
        $jawabanMahasiswa = JawabanMahasiswa::with([
            'soal.pilihan' => function($query) {
                $query->orderBy('huruf_pilihan');
            },
            'pilihanDipilih',
            'sesiUjian.ujian'
        ])->findOrFail($id);

        // Check if the exam session is still ongoing
        if ($jawabanMahasiswa->sesiUjian->status !== 'berlangsung') {
            return redirect()->route('admin.jawaban_mahasiswa.show', $id)
                ->with('error', 'Hanya dapat mengedit jawaban dari sesi ujian yang sedang berlangsung');
        }

        $allOptions = $jawabanMahasiswa->soal->pilihan;

        return view('pages.admin.jawaban_mahasiswa.edit', compact(
            'jawabanMahasiswa',
            'allOptions'
        ));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_pilihan_dipilih' => 'required|exists:pilihan,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $jawabanMahasiswa = JawabanMahasiswa::with(['sesiUjian', 'soal'])->findOrFail($id);

            // Check if the exam session is still ongoing
            if ($jawabanMahasiswa->sesiUjian->status !== 'berlangsung') {
                return redirect()->route('admin.jawaban_mahasiswa.show', $id)
                    ->with('error', 'Hanya dapat mengedit jawaban dari sesi ujian yang sedang berlangsung');
            }

            // Verify that the selected option belongs to the same question
            $pilihan = Pilihan::where('id', $request->id_pilihan_dipilih)
                ->where('id_soal', $jawabanMahasiswa->id_soal)
                ->firstOrFail();

            $jawabanMahasiswa->update([
                'id_pilihan_dipilih' => $request->id_pilihan_dipilih
            ]);

            return redirect()->route('admin.jawaban_mahasiswa.index', $jawabanMahasiswa->id_sesi)
                ->with('success', 'Jawaban berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui jawaban: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $jawabanMahasiswa = JawabanMahasiswa::with('sesiUjian')->findOrFail($id);
            $id_sesi = $jawabanMahasiswa->id_sesi;

            // Check if the exam session is still ongoing
            if ($jawabanMahasiswa->sesiUjian->status !== 'berlangsung') {
                return redirect()->route('admin.jawaban_mahasiswa.show', $id)
                    ->with('error', 'Hanya dapat menghapus jawaban dari sesi ujian yang sedang berlangsung');
            }

            $jawabanMahasiswa->delete();

            return redirect()->route('admin.jawaban_mahasiswa.index', $id_sesi)
                ->with('success', 'Jawaban berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus jawaban: ' . $e->getMessage());
        }
    }

    public function bulkAnswer(Request $request, $id_sesi)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.id_soal' => 'required|exists:soal,id',
            'answers.*.id_pilihan' => 'required|exists:pilihan,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sesiUjian = SesiUjian::findOrFail($id_sesi);

            // Check if the exam session is still ongoing
            if ($sesiUjian->status !== 'berlangsung') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya dapat mengedit jawaban dari sesi ujian yang sedang berlangsung'
                ], 400);
            }

            foreach ($request->answers as $answerData) {
                // Verify that the selected option belongs to the same question
                $pilihan = Pilihan::where('id', $answerData['id_pilihan'])
                    ->where('id_soal', $answerData['id_soal'])
                    ->first();

                if ($pilihan) {
                    JawabanMahasiswa::updateOrCreate(
                        [
                            'id_sesi' => $id_sesi,
                            'id_soal' => $answerData['id_soal']
                        ],
                        [
                            'id_pilihan_dipilih' => $answerData['id_pilihan']
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jawaban: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reviewMode($id_sesi)
    {
        try {
            $sesiUjian = SesiUjian::with([
                'ujian.soal.pilihan',
                'mahasiswa',
                'jawabanMahasiswa.soal.pilihan',
                'jawabanMahasiswa.pilihanDipilih'
            ])->findOrFail($id_sesi);

            // Group answers by question for review
            $reviewData = [];
            if ($sesiUjian->ujian) {
                foreach ($sesiUjian->ujian->soal->sortBy('nomor_soal') as $soal) {
                    $jawaban = $sesiUjian->jawabanMahasiswa
                        ->where('id_soal', $soal->id)
                        ->first();

                    $allOptions = $soal->pilihan->sortBy('huruf_pilihan');
                    $correctOption = $allOptions->where('is_benar', true)->first();

                    $reviewData[] = [
                        'soal' => $soal,
                        'jawaban' => $jawaban,
                        'allOptions' => $allOptions,
                        'correctOption' => $correctOption,
                        'isCorrect' => $jawaban ? $jawaban->isBenar() : false,
                        'hasAnswered' => $jawaban !== null
                    ];
                }
            }

            return view('pages.admin.jawaban_mahasiswa.review', compact(
                'sesiUjian',
                'reviewData'
            ));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memuat mode review: ' . $e->getMessage());
        }
    }

    public function statistics($id_ujian = null)
    {
        if ($id_ujian) {
            // Get question statistics for an exam
            $soals = Soal::with(['pilihan', 'jawabanMahasiswa.sesiUjian'])
                ->where('id_ujian', $id_ujian)
                ->get();

            $statistics = [];
            foreach ($soals as $soal) {
                $totalAnswers = $soal->jawabanMahasiswa
                    ->filter(function ($jawaban) {
                        return $jawaban->sesiUjian->status === 'selesai';
                    })
                    ->count();

                $correctAnswers = $soal->jawabanMahasiswa
                    ->filter(function ($jawaban) {
                        return $jawaban->sesiUjian->status === 'selesai' && $jawaban->isBenar();
                    })
                    ->count();

                $correctPercentage = $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0;

                // Option statistics
                $optionStats = [];
                foreach ($soal->pilihan as $option) {
                    $optionCount = $soal->jawabanMahasiswa
                        ->where('id_pilihan_dipilih', $option->id)
                        ->filter(function ($jawaban) {
                            return $jawaban->sesiUjian->status === 'selesai';
                        })
                        ->count();

                    $optionPercentage = $totalAnswers > 0 ? round(($optionCount / $totalAnswers) * 100, 2) : 0;

                    $optionStats[] = [
                        'option' => $option,
                        'count' => $optionCount,
                        'percentage' => $optionPercentage
                    ];
                }

                $statistics[] = [
                    'soal' => $soal,
                    'totalAnswers' => $totalAnswers,
                    'correctAnswers' => $correctAnswers,
                    'correctPercentage' => $correctPercentage,
                    'optionStats' => $optionStats
                ];
            }

            return view('pages.admin.jawaban_mahasiswa.statistics', compact('statistics'));
        } else {
            return redirect()->route('master.ujian.index')
                ->with('info', 'Pilih ujian terlebih dahulu untuk melihat statistik jawaban');
        }
    }

    /**
     * Review mode dengan encrypted ID
     */
    public function reviewModeEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $sesiUjian = SesiUjian::with([
                'ujian.soal.pilihan',
                'mahasiswa',
                'jawabanMahasiswa.soal.pilihan',
                'jawabanMahasiswa.pilihanDipilih'
            ])->findOrFail($id);

            // Group answers by question for review
            $reviewData = [];
            if ($sesiUjian->ujian) {
                foreach ($sesiUjian->ujian->soal->sortBy('nomor_soal') as $soal) {
                    $jawaban = $sesiUjian->jawabanMahasiswa
                        ->where('id_soal', $soal->id)
                        ->first();

                    $allOptions = $soal->pilihan->sortBy('huruf_pilihan');
                    $correctOption = $allOptions->where('is_benar', true)->first();

                    $reviewData[] = [
                        'soal' => $soal,
                        'jawaban' => $jawaban,
                        'allOptions' => $allOptions,
                        'correctOption' => $correctOption,
                        'isCorrect' => $jawaban ? $jawaban->isBenar() : false,
                        'hasAnswered' => $jawaban !== null
                    ];
                }
            }

            return view('pages.admin.jawaban_mahasiswa.review', compact(
                'sesiUjian',
                'reviewData'
            ));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat mode review: ' . $e->getMessage());
        }
    }
}