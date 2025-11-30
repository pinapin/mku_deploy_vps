<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Soal;
use App\Models\Pilihan;
use App\Services\UrlEncryptionService;
use Illuminate\Support\Facades\Validator;

class PilihanController extends Controller
{
    public function index($id_soal)
    {
        $soal = Soal::with('ujian')->findOrFail($id_soal);
        $pilihans = Pilihan::where('id_soal', $id_soal)
            ->orderBy('huruf_pilihan')
            ->get();

        return view('pages.admin.pilihan.index', compact('soal', 'pilihans'));
    }

    public function create($id_soal)
    {
        $soal = Soal::with('ujian')->findOrFail($id_soal);

        // Check if this is a multiple choice question
        if ($soal->tipe != 'pilihan_ganda') {
            return redirect()->route('master.soal.show', $id_soal)
                ->with('error', 'Hanya soal pilihan ganda yang dapat memiliki pilihan jawaban');
        }

        $lastHuruf = Pilihan::where('id_soal', $id_soal)
            ->orderBy('huruf_pilihan', 'desc')
            ->value('huruf_pilihan');

        $nextHuruf = $lastHuruf ? chr(ord($lastHuruf) + 1) : 'A';

        return view('pages.admin.pilihan.create', compact('soal', 'nextHuruf'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_soal' => 'required|exists:soal,id',
            'huruf_pilihan' => 'required|string|max:1',
            'teks_pilihan' => 'required|string',
            'is_benar' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $soal = Soal::findOrFail($request->id_soal);

            // Check if this is a multiple choice question
            if ($soal->tipe != 'pilihan_ganda') {
                return redirect()->back()
                    ->with('error', 'Hanya soal pilihan ganda yang dapat memiliki pilihan jawaban')
                    ->withInput();
            }

            // Check if option letter already exists for this question
            if (Pilihan::where('id_soal', $request->id_soal)
                ->where('huruf_pilihan', $request->huruf_pilihan)
                ->exists()) {
                return redirect()->back()
                    ->with('error', 'Huruf pilihan sudah ada untuk soal ini')
                    ->withInput();
            }

            // If this option is marked as correct, unmark all other options
            if ($request->has('is_benar') && $request->is_benar) {
                Pilihan::where('id_soal', $request->id_soal)
                    ->update(['is_benar' => false]);
            }

            $pilihan = Pilihan::create([
                'id_soal' => $request->id_soal,
                'huruf_pilihan' => strtoupper($request->huruf_pilihan),
                'teks_pilihan' => $request->teks_pilihan,
                'is_benar' => $request->has('is_benar') ? $request->is_benar : false
            ]);

            return redirect()->route('admin.pilihan.index', $request->id_soal)
                ->with('success', 'Pilihan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pilihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $pilihan = Pilihan::with(['soal.ujian'])->findOrFail($id);
        return view('pages.admin.pilihan.show', compact('pilihan'));
    }

    public function edit($id)
    {
        $pilihan = Pilihan::with(['soal.ujian'])->findOrFail($id);

        // Check if this is a multiple choice question
        if ($pilihan->soal->tipe != 'pilihan_ganda') {
            return redirect()->route('master.soal.show', $pilihan->id_soal)
                ->with('error', 'Hanya soal pilihan ganda yang dapat memiliki pilihan jawaban');
        }

        return view('pages.admin.pilihan.edit', compact('pilihan'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'huruf_pilihan' => 'required|string|max:1',
            'teks_pilihan' => 'required|string',
            'is_benar' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pilihan = Pilihan::with('soal')->findOrFail($id);

            // Check if this is a multiple choice question
            if ($pilihan->soal->tipe != 'pilihan_ganda') {
                return redirect()->back()
                    ->with('error', 'Hanya soal pilihan ganda yang dapat memiliki pilihan jawaban')
                    ->withInput();
            }

            // Check if option letter already exists for this question (excluding current option)
            if (Pilihan::where('id_soal', $pilihan->id_soal)
                ->where('huruf_pilihan', $request->huruf_pilihan)
                ->where('id', '!=', $id)
                ->exists()) {
                return redirect()->back()
                    ->with('error', 'Huruf pilihan sudah ada untuk soal ini')
                    ->withInput();
            }

            // If this option is marked as correct, unmark all other options
            if ($request->has('is_benar') && $request->is_benar) {
                Pilihan::where('id_soal', $pilihan->id_soal)
                    ->where('id', '!=', $id)
                    ->update(['is_benar' => false]);
            }

            $pilihan->update([
                'huruf_pilihan' => strtoupper($request->huruf_pilihan),
                'teks_pilihan' => $request->teks_pilihan,
                'is_benar' => $request->has('is_benar') ? $request->is_benar : false
            ]);

            return redirect()->route('admin.pilihan.index', $pilihan->id_soal)
                ->with('success', 'Pilihan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pilihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $pilihan = Pilihan::findOrFail($id);
            $id_soal = $pilihan->id_soal;

            // Check if there are any student answers for this option
            if ($pilihan->jawabanMahasiswa()->exists()) {
                return redirect()->route('admin.pilihan.index', $id_soal)
                    ->with('error', 'Tidak dapat menghapus pilihan yang sudah dipilih oleh mahasiswa');
            }

            // Check if this is the only option left for a multiple choice question
            $soal = $pilihan->soal;
            if ($soal->tipe == 'pilihan_ganda' && $soal->pilihan()->count() <= 1) {
                return redirect()->route('admin.pilihan.index', $id_soal)
                    ->with('error', 'Tidak dapat menghapus pilihan terakhir untuk soal pilihan ganda');
            }

            $pilihan->delete();

            return redirect()->route('admin.pilihan.index', $id_soal)
                ->with('success', 'Pilihan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pilihan: ' . $e->getMessage());
        }
    }

    public function setCorrectAnswer($id)
    {
        try {
            $pilihan = Pilihan::with('soal')->findOrFail($id);

            // Check if this is a multiple choice question
            if ($pilihan->soal->tipe != 'pilihan_ganda') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya soal pilihan ganda yang dapat memiliki jawaban benar'
                ], 400);
            }

            // Unmark all other options and mark this one as correct
            Pilihan::where('id_soal', $pilihan->id_soal)
                ->update(['is_benar' => false]);

            $pilihan->update(['is_benar' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Jawaban benar berhasil ditetapkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan jawaban benar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkCreate(Request $request, $id_soal)
    {
        $validator = Validator::make($request->all(), [
            'pilihans' => 'required|array|min:2',
            'pilihans.*.text' => 'required|string',
            'correct_answer' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $soal = Soal::findOrFail($id_soal);

            // Check if this is a multiple choice question
            if ($soal->tipe != 'pilihan_ganda') {
                return redirect()->back()
                    ->with('error', 'Hanya soal pilihan ganda yang dapat memiliki pilihan jawaban')
                    ->withInput();
            }

            // Delete existing options if any
            Pilihan::where('id_soal', $id_soal)->delete();

            // Create new options
            foreach ($request->pilihans as $index => $pilihanData) {
                $isCorrect = ($index == $request->correct_answer);

                Pilihan::create([
                    'id_soal' => $id_soal,
                    'huruf_pilihan' => chr(65 + $index), // A, B, C, D, E
                    'teks_pilihan' => $pilihanData['text'],
                    'is_benar' => $isCorrect
                ]);
            }

            return redirect()->route('admin.pilihan.index', $id_soal)
                ->with('success', 'Pilihan jawaban berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat pilihan jawaban: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Index pilihan dengan encrypted ID
     */
    public function indexEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $soal = Soal::with('ujian')->findOrFail($id);
            $pilihans = Pilihan::where('id_soal', $id)
                ->orderBy('huruf_pilihan')
                ->get();

            return view('pages.admin.pilihan.index', compact('soal', 'pilihans'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat data pilihan: ' . $e->getMessage());
        }
    }

    /**
     * Create pilihan dengan encrypted ID
     */
    public function createEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $soal = Soal::with('ujian')->findOrFail($id);
            return view('pages.admin.pilihan.create', compact('soal'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat form tambah pilihan: ' . $e->getMessage());
        }
    }

    /**
     * Show pilihan dengan encrypted ID
     */
    public function showEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired URL'
                ], 400);
            }

            $pilihan = Pilihan::with('soal.ujian')->findOrFail($id);
            return response()->json($pilihan);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pilihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit pilihan dengan encrypted ID
     */
    public function editEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $pilihan = Pilihan::with('soal.ujian')->findOrFail($id);
            return view('pages.admin.pilihan.edit', compact('pilihan'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat form edit pilihan: ' . $e->getMessage());
        }
    }

    /**
     * Update pilihan dengan encrypted ID
     */
    public function updateEncrypted(Request $request, $encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $pilihan = Pilihan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'teks_pilihan' => 'required|string',
                'is_benar' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $pilihan->update([
                'teks_pilihan' => $request->teks_pilihan,
                'is_benar' => $request->has('is_benar') ? 1 : 0
            ]);

            // If this is marked as correct, unmark all other options
            if ($request->has('is_benar')) {
                Pilihan::where('id_soal', $pilihan->id_soal)
                    ->where('id', '!=', $id)
                    ->update(['is_benar' => false]);
            }

            return redirect()->route('master.pilihan.index.encrypted', UrlEncryptionService::encryptId($pilihan->id_soal))
                ->with('success', 'Pilihan jawaban berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pilihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Destroy pilihan dengan encrypted ID
     */
    public function destroyEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $pilihan = Pilihan::findOrFail($id);
            $id_soal = $pilihan->id_soal;
            $pilihan->delete();

            return redirect()->route('master.pilihan.index.encrypted', UrlEncryptionService::encryptId($id_soal))
                ->with('success', 'Pilihan jawaban berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pilihan: ' . $e->getMessage());
        }
    }

    /**
     * Set correct answer dengan encrypted ID
     */
    public function setCorrectAnswerEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired URL'
                ], 400);
            }

            $pilihan = Pilihan::with('soal')->findOrFail($id);

            // Check if this is a multiple choice question
            if ($pilihan->soal->tipe != 'pilihan_ganda') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya soal pilihan ganda yang dapat memiliki jawaban benar'
                ], 400);
            }

            // Unmark all other options and mark this one as correct
            Pilihan::where('id_soal', $pilihan->id_soal)
                ->update(['is_benar' => false]);

            $pilihan->update(['is_benar' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Jawaban benar berhasil ditetapkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan jawaban benar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk create pilihan dengan encrypted ID
     */
    public function bulkCreateEncrypted(Request $request, $encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $validator = Validator::make($request->all(), [
                'pilihans' => 'required|array|min:2',
                'pilihans.*.text' => 'required|string',
                'correct_answer' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $soal = Soal::findOrFail($id);

            // Check if this is a multiple choice question
            if ($soal->tipe != 'pilihan_ganda') {
                return redirect()->back()
                    ->with('error', 'Hanya soal pilihan ganda yang dapat memiliki pilihan jawaban')
                    ->withInput();
            }

            // Delete existing options if any
            Pilihan::where('id_soal', $id)->delete();

            // Create new options
            foreach ($request->pilihans as $index => $pilihanData) {
                $isCorrect = ($index == $request->correct_answer);

                Pilihan::create([
                    'id_soal' => $id,
                    'huruf_pilihan' => chr(65 + $index), // A, B, C, D, E
                    'teks_pilihan' => $pilihanData['text'],
                    'is_benar' => $isCorrect
                ]);
            }

            return redirect()->route('master.pilihan.index.encrypted', $encryptedId)
                ->with('success', 'Pilihan jawaban berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat pilihan jawaban: ' . $e->getMessage())
                ->withInput();
        }
    }
}