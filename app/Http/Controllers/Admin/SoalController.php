<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Pilihan;
use App\Services\UrlEncryptionService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;
use App\Exports\SoalTemplateExport;

class SoalController extends Controller
{
    public function index($id_ujian)
    {
        $ujian = Ujian::findOrFail($id_ujian);
        $soals = Soal::with('pilihan')
            ->where('id_ujian', $id_ujian)
            ->orderBy('nomor_soal')
            ->get();

        return view('pages.admin.soal.index', compact('ujian', 'soals'));
    }

    public function create($id_ujian)
    {
        $ujian = Ujian::findOrFail($id_ujian);
        $lastNomor = Soal::where('id_ujian', $id_ujian)->max('nomor_soal') ?? 0;

        return view('pages.admin.soal.create', compact('ujian', 'lastNomor'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_ujian' => 'required|exists:ujian,id',
            'nomor_soal' => 'required|integer|min:1',
            'teks_soal' => 'required|string',
            'tipe' => 'required|in:pilihan_ganda,essay',
            'pilihan' => 'required_if:tipe,pilihan_ganda|array|min:2',
            'pilihan.*.text' => 'required_if:tipe,pilihan_ganda|string',
            'pilihan.*.is_benar' => 'nullable',
            'kunci_jawaban' => 'nullable|required_if:tipe,essay|string'
        ], [
            'pilihan.min' => 'Minimal harus ada 2 pilihan jawaban untuk soal pilihan ganda',
            'pilihan.*.text.required_if' => 'Teks pilihan jawaban tidak boleh kosong',
            'pilihan.*.text.min' => 'Teks pilihan jawaban minimal harus 1 karakter'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if question number already exists for this exam
        if (Soal::where('id_ujian', $request->id_ujian)
            ->where('nomor_soal', $request->nomor_soal)
            ->exists()) {
            return redirect()->back()
                ->with('error', 'Nomor soal sudah ada untuk ujian ini')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'id_ujian' => $request->id_ujian,
                'nomor_soal' => $request->nomor_soal,
                'teks_soal' => $request->teks_soal,
                'tipe' => $request->tipe == 'pilihan_ganda' ? 'pilihan_ganda' : 'essay' // Map tipe to tipe
            ]);

            // If multiple choice, create options
            if ($request->tipe == 'pilihan_ganda' && isset($request->pilihan)) {
                $correctAnswer = $request->correct_answer; // Get the selected correct answer index

                $index = 0;
                foreach ($request->pilihan as $pilihan) {
                    if (!empty($pilihan['text'])) {
                        Pilihan::create([
                            'id_soal' => $soal->id,
                            'huruf_pilihan' => chr(65 + $index), // A, B, C, D, E, F, etc.
                            'teks_pilihan' => $pilihan['text'],
                            'is_benar' => $index == $correctAnswer // Set true for correct answer
                        ]);
                        $index++;
                    }
                }
            }

            DB::commit();

            return redirect()->route('master.soal.index.encrypted', UrlEncryptionService::encryptId($request->id_ujian))
                ->with('success', 'Soal berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $soal = Soal::with(['ujian', 'pilihan' => function($query) {
            $query->orderBy('huruf_pilihan');
        }])->findOrFail($id);

        // If AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($soal);
        }

        return view('pages.admin.soal.show', compact('soal'));
    }

    public function edit($id)
    {
        $soal = Soal::with(['pilihan' => function($query) {
            $query->orderBy('huruf_pilihan');
        }])->findOrFail($id);

        return view('pages.admin.soal.edit', compact('soal'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor_soal' => 'required|integer|min:1',
            'teks_soal' => 'required|string',
            'tipe' => 'required|in:pilihan_ganda,essay',
            'pilihan' => 'required_if:tipe,pilihan_ganda|array|min:2',
            'pilihan.*.text' => 'required_if:tipe,pilihan_ganda|string',
            'pilihan.*.is_benar' => 'nullable',
            'pilihan.*.id' => 'nullable|exists:pilihan,id',
            'kunci_jawaban' => 'nullable|required_if:tipe,essay|string'
        ], [
            'pilihan.min' => 'Minimal harus ada 2 pilihan jawaban untuk soal pilihan ganda',
            'pilihan.*.text.required_if' => 'Teks pilihan jawaban tidak boleh kosong',
            'pilihan.*.text.min' => 'Teks pilihan jawaban minimal harus 1 karakter'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $soal = Soal::findOrFail($id);

            // Check if question number already exists for this exam (excluding current question)
            if (Soal::where('id_ujian', $soal->id_ujian)
                ->where('nomor_soal', $request->nomor_soal)
                ->where('id', '!=', $id)
                ->exists()) {
                return redirect()->back()
                    ->with('error', 'Nomor soal sudah ada untuk ujian ini')
                    ->withInput();
            }

            $soal->update([
                'nomor_soal' => $request->nomor_soal,
                'teks_soal' => $request->teks_soal,
                'tipe' => $request->tipe == 'pilihan_ganda' ? 'pilihan_ganda' : 'essay', // Map tipe to tipe
                'kunci_jawaban' => $request->tipe == 'essay' ? $request->kunci_jawaban : null
            ]);

            // Handle options for multiple choice questions
            if ($request->tipe == 'pilihan_ganda') {
                // Get existing options
                $existingOptions = $soal->pilihan->keyBy('id');

                // Update or create options
                if (isset($request->pilihan)) {
                    $correctAnswer = $request->correct_answer; // Get the selected correct answer index
                    $index = 0;

                    foreach ($request->pilihan as $pilihanData) {
                        if (!empty($pilihanData['text'])) {
                            $huruf = chr(65 + $index); // A, B, C, D, E, F, etc.
                            $isBenar = $index == $correctAnswer; // Set true for correct answer

                            if (isset($pilihanData['id']) && isset($existingOptions[$pilihanData['id']])) {
                                // Update existing option
                                $existingOptions[$pilihanData['id']]->update([
                                    'huruf_pilihan' => $huruf,
                                    'teks_pilihan' => $pilihanData['text'],
                                    'is_benar' => $isBenar
                                ]);
                                unset($existingOptions[$pilihanData['id']]);
                            } else {
                                // Create new option
                                Pilihan::create([
                                    'id_soal' => $soal->id,
                                    'huruf_pilihan' => $huruf,
                                    'teks_pilihan' => $pilihanData['text'],
                                    'is_benar' => $isBenar
                                ]);
                            }
                            $index++;
                        }
                    }
                }

                // Delete remaining options that were not in the form
                foreach ($existingOptions as $option) {
                    $option->delete();
                }
            } else {
                // If changed to essay, delete all options
                $soal->pilihan()->delete();
            }

            DB::commit();

            return redirect()->route('master.soal.index', $soal->id_ujian)
                ->with('success', 'Soal berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $soal = Soal::findOrFail($id);
            $id_ujian = $soal->id_ujian;

            // Check if there are any student answers for this question
            if ($soal->jawabanMahasiswa()->exists()) {
                return redirect()->route('master.soal.index', $id_ujian)
                    ->with('error', 'Tidak dapat menghapus soal yang sudah dijawab oleh mahasiswa');
            }

            $soal->delete();

            return redirect()->route('master.soal.index', $id_ujian)
                ->with('success', 'Soal berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }

    public function import(Request $request, $id_ujian)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // Max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $ujian = Ujian::findOrFail($id_ujian);

            Excel::import(new SoalImport($id_ujian), $request->file('file'));

            return redirect()->route('master.soal.index', $id_ujian)
                ->with('success', 'Soal dan pilihan berhasil diimpor');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function downloadTemplate($id_ujian)
    {
        try {
            $ujian = Ujian::findOrFail($id_ujian);
            return Excel::download(new SoalTemplateExport($ujian), 'template_soal_'.$ujian->nama_ujian.'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }

    public function reorderQuestions(Request $request, $id_ujian)
    {
        $validator = Validator::make($request->all(), [
            'soals' => 'required|array',
            'soals.*.id' => 'required|exists:soal,id',
            'soals.*.nomor_soal' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid'
            ], 422);
        }

        try {
            foreach ($request->soals as $soalData) {
                Soal::where('id_ujian', $id_ujian)
                    ->where('id', $soalData['id'])
                    ->update(['nomor_soal' => $soalData['nomor_soal']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan soal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan soal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Index soal dengan encrypted ID
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
            $soals = Soal::with('pilihan')
                ->where('id_ujian', $id)
                ->orderBy('nomor_soal')
                ->get();

            return view('pages.admin.soal.index', compact('ujian', 'soals'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat data soal: ' . $e->getMessage());
        }
    }

    /**
     * Create soal dengan encrypted ID
     */
    public function createEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);
            $lastNomor = Soal::where('id_ujian', $id)->max('nomor_soal') ?? 0;

            return view('pages.admin.soal.create', compact('ujian', 'lastNomor'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat form tambah soal: ' . $e->getMessage());
        }
    }

    /**
     * Import soal dengan encrypted ID
     */
    public function importEncrypted(Request $request, $encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:xlsx,xls,csv|max:10240'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $ujian = Ujian::findOrFail($id);

            Excel::import(new SoalImport($id), $request->file('file'));

            return redirect()->route('master.soal.index.encrypted', $encryptedId)
                ->with('success', 'Soal dan pilihan berhasil diimpor');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template soal dengan encrypted ID
     */
    public function downloadTemplateEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);
            return Excel::download(new SoalTemplateExport($ujian), 'template_soal_'.$ujian->nama_ujian.'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }

    /**
     * Show soal dengan encrypted ID
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

            $soal = Soal::with(['ujian', 'pilihan' => function($query) {
                $query->orderBy('huruf_pilihan');
            }])->findOrFail($id);

            return response()->json($soal);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data soal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit soal dengan encrypted ID
     */
    public function editEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $soal = Soal::with(['pilihan' => function($query) {
                $query->orderBy('huruf_pilihan');
            }])->findOrFail($id);

            return view('pages.admin.soal.edit', compact('soal'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat form edit: ' . $e->getMessage());
        }
    }

    /**
     * Update soal dengan encrypted ID
     */
    public function updateEncrypted(Request $request, $encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $validator = Validator::make($request->all(), [
                'nomor_soal' => 'required|integer|min:1',
                'teks_soal' => 'required|string',
                'tipe' => 'required|in:pilihan_ganda,essay',
                'pilihan' => 'required_if:tipe,pilihan_ganda|array|min:2',
                'pilihan.*.text' => 'required_if:tipe,pilihan_ganda|string',
                'pilihan.*.is_benar' => 'nullable',
                'pilihan.*.id' => 'nullable|exists:pilihan,id',
                'kunci_jawaban' => 'nullable|required_if:tipe,essay|string'
            ], [
                'pilihan.min' => 'Minimal harus ada 2 pilihan jawaban untuk soal pilihan ganda',
                'pilihan.*.text.required_if' => 'Teks pilihan jawaban tidak boleh kosong',
                'pilihan.*.text.min' => 'Teks pilihan jawaban minimal harus 1 karakter'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();
            try {
                $soal = Soal::findOrFail($id);

                // Check if question number already exists for this exam (excluding current question)
                if (Soal::where('id_ujian', $soal->id_ujian)
                    ->where('nomor_soal', $request->nomor_soal)
                    ->where('id', '!=', $id)
                    ->exists()) {
                    return redirect()->back()
                        ->with('error', 'Nomor soal sudah ada untuk ujian ini')
                        ->withInput();
                }

                $soal->update([
                    'nomor_soal' => $request->nomor_soal,
                    'teks_soal' => $request->teks_soal,
                    'tipe' => $request->tipe == 'pilihan_ganda' ? 'pilihan_ganda' : 'essay',
                    'kunci_jawaban' => $request->tipe == 'essay' ? $request->kunci_jawaban : null
                ]);

                // Handle options for multiple choice questions
                if ($request->tipe == 'pilihan_ganda') {
                    // Get existing options
                    $existingOptions = $soal->pilihan->keyBy('id');

                    // Update or create options
                    if (isset($request->pilihan)) {
                        $correctAnswer = $request->correct_answer;
                        $index = 0;

                        foreach ($request->pilihan as $pilihanData) {
                            if (!empty($pilihanData['text'])) {
                                $huruf = chr(65 + $index);
                                $isBenar = $index == $correctAnswer;

                                if (isset($pilihanData['id']) && isset($existingOptions[$pilihanData['id']])) {
                                    // Update existing option
                                    $existingOptions[$pilihanData['id']]->update([
                                        'huruf_pilihan' => $huruf,
                                        'teks_pilihan' => $pilihanData['text'],
                                        'is_benar' => $isBenar
                                    ]);
                                    unset($existingOptions[$pilihanData['id']]);
                                } else {
                                    // Create new option
                                    Pilihan::create([
                                        'id_soal' => $soal->id,
                                        'huruf_pilihan' => $huruf,
                                        'teks_pilihan' => $pilihanData['text'],
                                        'is_benar' => $isBenar
                                    ]);
                                }
                                $index++;
                            }
                        }
                    }

                    // Delete remaining options that were not in the form
                    foreach ($existingOptions as $option) {
                        $option->delete();
                    }
                } else {
                    // If changed to essay, delete all options
                    $soal->pilihan()->delete();
                }

                DB::commit();

                return redirect()->route('master.soal.index.encrypted', UrlEncryptionService::encryptId($soal->id_ujian))
                    ->with('success', 'Soal berhasil diperbarui');
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Destroy soal dengan encrypted ID
     */
    public function destroyEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $soal = Soal::findOrFail($id);
            $id_ujian = $soal->id_ujian;

            // Check if there are any student answers for this question
            if ($soal->jawabanMahasiswa()->exists()) {
                return redirect()->route('master.soal.index.encrypted', UrlEncryptionService::encryptId($id_ujian))
                    ->with('error', 'Tidak dapat menghapus soal yang sudah dijawab oleh mahasiswa');
            }

            $soal->delete();

            return redirect()->route('master.soal.index.encrypted', UrlEncryptionService::encryptId($id_ujian))
                ->with('success', 'Soal berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }
}