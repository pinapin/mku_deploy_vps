<?php

namespace App\Jobs;

use App\Models\SesiUjian;
use App\Models\JawabanMahasiswa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ScoreExamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sesiUjian;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param SesiUjian $sesiUjian
     * @return void
     */
    public function __construct(SesiUjian $sesiUjian)
    {
        $this->sesiUjian = $sesiUjian;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // Double check if the exam is still running before scoring
        if ($this->sesiUjian->status !== 'berlangsung') {
            // Check if already scored to prevent duplicate jobs
            if ($this->sesiUjian->status === 'selesai' && $this->sesiUjian->skor_akhir !== null) {
                return;
            }
            return;
        }

        // Use database transaction to prevent race conditions
        DB::transaction(function () {
            // Re-lock the session to prevent concurrent scoring
            $freshSesi = SesiUjian::where('id', $this->sesiUjian->id)
                ->lockForUpdate()
                ->first();

            // Double-check status after acquiring lock
            if ($freshSesi->status !== 'berlangsung') {
                return;
            }

            // Mark as processing to prevent duplicate jobs
            $freshSesi->update([
                'status' => 'scoring',
                'waktu_selesai' => now()
            ]);

            // Load relationships properly
            $freshSesi->load(['ujian.soal']);

            $totalQuestions = $freshSesi->ujian ? $freshSesi->ujian->soal->count() : 0;

            if ($totalQuestions === 0) {
                $freshSesi->update([
                    'skor_akhir' => 0,
                    'status' => 'selesai'
                ]);
                return;
            }

            // Get correct answers efficiently
            $correctAnswers = JawabanMahasiswa::where('id_sesi', $freshSesi->id)
                ->with('pilihanDipilih')
                ->get()
                ->filter(function ($jawaban) {
                    return $jawaban->isBenar();
                })
                ->count();

            $score = round(($correctAnswers / $totalQuestions) * 100);

            // Final update with score and completion status
            $freshSesi->update([
                'skor_akhir' => $score,
                'status' => 'selesai'
            ]);
        });
    }
}
