<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SertifikatKwu;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Imports\SertifikatKwuImport;
use App\Exports\SertifikatKwuExportTemplate;
use App\Exports\SertifikatKwuValidationExport;
use App\Models\Fakultas;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SertifikatKwuController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::all();
        $fakultas = Fakultas::all();
        $tahunList = SertifikatKwu::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $semesterList = SertifikatKwu::select('semester')->distinct()->orderBy('semester')->pluck('semester');

        return view('pages.admin.sertifikat-kwu.index', compact('programStudis', 'fakultas', 'tahunList', 'semesterList'));
    }

    public function getData(Request $request)
    {
        $query = SertifikatKwu::with(['programStudi.fakultas']);

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }

        // Filter berdasarkan semester
        if ($request->has('semester') && $request->semester != '') {
            $query->where('semester', $request->semester);
        }

        // Filter berdasarkan program studi
        if ($request->has('prodi_id') && $request->prodi_id != '') {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter berdasarkan fakultas
        if ($request->has('fakultas_id') && $request->fakultas_id != '') {
            $query->whereHas('programStudi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_prodi', function ($sertifikat) {
                return $sertifikat->programStudi ? $sertifikat->programStudi->nama_prodi : '<span class="badge badge-warning">Tidak Ada</span>';
            })
            ->addColumn('nama_fakultas', function ($sertifikat) {
                return $sertifikat->programStudi && $sertifikat->programStudi->fakultas ? $sertifikat->programStudi->fakultas->nama_fakultas : '<span class="badge badge-warning">Tidak Ada</span>';
            })
            ->addColumn('keterangan', function ($sertifikat) {
                return $sertifikat->keterangan ?? '-';
            })
            ->addColumn('tgl_sertifikat', function ($sertifikat) {
                return $sertifikat->tgl_sertifikat ? date('d-m-Y', strtotime($sertifikat->tgl_sertifikat)) : '-';
            })
            ->addColumn('action', function ($sertifikat) {
                $btn = '<div class="btn-group">';
                $btn .= '<button data-toggle="tooltip" title="Edit" type="button" class="btn btn-sm btn-info btn-edit" data-id="' . $sertifikat->id . '"><i class="fas fa-edit"></i></button>';
                $btn .= '<button data-toggle="tooltip" title="Hapus" type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $sertifikat->id . '"><i class="fas fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['nama_prodi', 'nama_fakultas', 'action', 'keterangan', 'nilai'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('no_sertifikat', 'like', "%{$searchValue}%")
                            ->orWhere('nim', 'like', "%{$searchValue}%")
                            ->orWhere('nama', 'like', "%{$searchValue}%")
                            ->orWhere('tahun', 'like', "%{$searchValue}%")
                            ->orWhere('semester', 'like', "%{$searchValue}%")
                            ->orWhere('tgl_sertifikat', 'like', "%{$searchValue}%")
                            ->orWhere('keterangan', 'like', "%{$searchValue}%")
                            ->orWhereHas('programStudi', function ($q) use ($searchValue) {
                                $q->where('nama_prodi', 'like', "%{$searchValue}%");
                            })
                            ->orWhereHas('programStudi.fakultas', function ($q) use ($searchValue) {
                                $q->where('nama_fakultas', 'like', "%{$searchValue}%");
                            });
                    });
                }
            })
            ->orderColumn('nama_prodi', function ($query, $direction) {
                $query->leftJoin('program_studis', 'sertifikat_kwus.prodi_id', '=', 'program_studis.id')
                    ->orderBy('program_studis.nama_prodi', $direction)
                    ->select('sertifikat_kwus.*');
            })
            ->orderColumn('nama_fakultas', function ($query, $direction) {
                $query->leftJoin('program_studis', 'sertifikat_kwus.prodi_id', '=', 'program_studis.id')
                    ->leftJoin('fakultas', 'program_studis.fakultas_id', '=', 'fakultas.id')
                    ->orderBy('fakultas.nama_fakultas', $direction)
                    ->select('sertifikat_kwus.*');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_sertifikat' => 'required|string|max:20|unique:sertifikat_kwus,no_sertifikat',
            'tgl_sertifikat' => 'required|date',
            'nim' => 'required|string|max:9',
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:program_studis,id',
            'semester' => 'required|string|max:255',
            'tahun' => 'required|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $sertifikatKwu = new SertifikatKwu();
        $sertifikatKwu->no_sertifikat = $request->no_sertifikat;
        $sertifikatKwu->tgl_sertifikat = $request->tgl_sertifikat;
        $sertifikatKwu->nim = $request->nim;
        $sertifikatKwu->nama = $request->nama;
        $sertifikatKwu->prodi_id = $request->prodi_id;
        $sertifikatKwu->semester = $request->semester;
        $sertifikatKwu->tahun = $request->tahun;
        $sertifikatKwu->keterangan = $request->keterangan;
        $sertifikatKwu->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sertifikat KWU berhasil ditambahkan',
            'data' => $sertifikatKwu
        ]);
    }

    public function show($id)
    {
        $sertifikatKwu = SertifikatKwu::find($id);

        if (!$sertifikatKwu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sertifikat KWU tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $sertifikatKwu
        ]);
    }

    public function update(Request $request, $id)
    {
        $sertifikatKwu = SertifikatKwu::find($id);

        if (!$sertifikatKwu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sertifikat KWU tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'no_sertifikat' => 'required|string|max:20|unique:sertifikat_kwus,no_sertifikat,' . $id,
            'tgl_sertifikat' => 'required|date',
            'nim' => 'required|string|max:9',
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:program_studis,id',
            'semester' => 'required|string|max:255',
            'tahun' => 'required|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $sertifikatKwu->no_sertifikat = $request->no_sertifikat;
        $sertifikatKwu->tgl_sertifikat = $request->tgl_sertifikat;
        $sertifikatKwu->nim = $request->nim;
        $sertifikatKwu->nama = $request->nama;
        $sertifikatKwu->prodi_id = $request->prodi_id;
        $sertifikatKwu->semester = $request->semester;
        $sertifikatKwu->tahun = $request->tahun;
        $sertifikatKwu->keterangan = $request->keterangan;
        $sertifikatKwu->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sertifikat KWU berhasil diperbarui',
            'data' => $sertifikatKwu
        ]);
    }

    public function destroy($id)
    {
        $sertifikatKwu = SertifikatKwu::find($id);

        if (!$sertifikatKwu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sertifikat KWU tidak ditemukan'
            ], 404);
        }

        $sertifikatKwu->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sertifikat KWU berhasil dihapus'
        ]);
    }

    public function importTemplate()
    {
        try {
            // Pastikan direktori temp ada
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $filename = 'template_import_sertifikat_kwu.xlsx';

            // Gunakan SertifikatKwuExportTemplate untuk membuat template
            return Excel::download(new SertifikatKwuExportTemplate(), $filename);
        } catch (\Exception $e) {
           
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Hitung jumlah data sebelum import
            $countBefore = SertifikatKwu::count();

            // Buat instance importer
            $import = new SertifikatKwuImport();

            // Impor data menggunakan Laravel Excel
            Excel::import($import, $request->file('file'));

            // Hitung jumlah data setelah import
            $countAfter = SertifikatKwu::count();

            // Hitung jumlah data yang berhasil diimpor
            $imported = $import->getImportedCount();

            // Ambil error dari import jika ada
            $errors = $import->getErrors();

            // Jika ada error, kembalikan warning dengan detail error
            if (count($errors) > 0) {
                return response()->json([
                    'status' => 'warning',
                    'message' => "Berhasil mengimpor " . $imported . " data. Terdapat " . count($errors) . " error.",
                    'errors' => $errors
                ]);
            }

            // Jika tidak ada error, kembalikan success
            return response()->json([
                'status' => 'success',
                'message' => "Berhasil mengimpor " . $imported . " data."
            ]);
        } catch (\Exception $e) {
           
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Baca file Excel tanpa menyimpan ke database
            $rows = Excel::toCollection(null, $request->file('file'))->first();

            // Hapus baris header
            $rows = $rows->slice(1);

            // Hitung total data yang diupload
            $totalData = $rows->count();

            // Inisialisasi array untuk menyimpan data validasi
            $validationData = [];

            // Ambil semua data sertifikat KWU yang ada di database
            $existingData = SertifikatKwu::select('nim', 'nama', 'tahun', 'semester')->get();

            // Proses setiap baris data
            foreach ($rows as $row) {
                // Pastikan kolom NIM dan Nama ada
                if (!isset($row[0]) || !isset($row[1])) {
                    continue;
                }

                $nim = (string) $row[0]; // Kolom NIM (indeks 0)
                $nama = (string) $row[1]; // Kolom Nama (indeks 1)

                // Lewati baris kosong
                if (empty($nim) && empty($nama)) {
                    continue;
                }

                // Cek apakah data ada di database
                $found = $existingData->first(function ($item) use ($nim, $nama) {
                    return $item->nim === $nim && $item->nama === $nama;
                });

                // Tambahkan ke data validasi
                $validationData[] = [
                    'nim' => $nim,
                    'nama' => $nama,
                    'status' => $found ? true : false,
                    'tahun' => $found ? $found->tahun : null,
                    'semester' => $found ? $found->semester : null
                ];
            }

            // Hitung jumlah data yang cocok dan tidak cocok
            $matchedCount = collect($validationData)->where('status', true)->count();
            $unmatchedCount = collect($validationData)->where('status', false)->count();

            // Simpan data validasi dalam session untuk digunakan saat ekspor
            session(['validation_data' => $validationData]);

            return response()->json([
                'status' => 'success',
                'message' => 'Validasi data berhasil',
                'data' => [
                    'total_data' => $totalData,
                    'matched_count' => $matchedCount,
                    'unmatched_count' => $unmatchedCount,
                    'has_validation_data' => true
                ]
            ]);
        } catch (\Exception $e) {
           
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memvalidasi data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export validation results to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportValidationResults(Request $request)
    {
        try {
            $validationData = session('validation_data', []);

            if (empty($validationData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data validasi untuk diekspor'
                ], 422);
            }

            return Excel::download(new SertifikatKwuValidationExport($validationData), 'hasil-validasi-sertifikat-kwu-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }
}
