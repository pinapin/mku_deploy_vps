<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Imports\MahasiswaImport;
use App\Exports\MahasiswaExportTemplate;
use App\Exports\MahasiswaUnmatchedDataExport;
use App\Models\Fakultas;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::all();
        $fakultas = Fakultas::all();
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')->orderBy('tipe_semester', 'asc')->get();
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', 1)->first();

        return view('pages.admin.p2k.mahasiswa.index', compact('programStudis', 'fakultas', 'tahunAkademiks', 'tahunAkademikAktif'));
    }

    public function getData(Request $request)
    {
        $query = Mahasiswa::with(['programStudi.fakultas', 'tahunAkademik']);

        // Filter berdasarkan tahun akademik
        if ($request->has('tahun_akademik_id') && $request->tahun_akademik_id != '') {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
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
            ->addColumn('nama_prodi', function ($mahasiswa) {
                return $mahasiswa->programStudi ? $mahasiswa->programStudi->nama_prodi : '<span class="badge badge-warning">Tidak Ada</span>';
            })
            ->addColumn('nama_fakultas', function ($mahasiswa) {
                return $mahasiswa->programStudi && $mahasiswa->programStudi->fakultas ? $mahasiswa->programStudi->fakultas->nama_fakultas : '<span class="badge badge-warning">Tidak Ada</span>';
            })
            ->addColumn('tahun_akademik', function ($mahasiswa) {
                return $mahasiswa->tahunAkademik ? $mahasiswa->tahunAkademik->tahun_ajaran . ' ' . $mahasiswa->tahunAkademik->tipe_semester : '<span class="badge badge-warning">Tidak Ada</span>';
            })
            ->addColumn('action', function ($mahasiswa) {
                $btn = '<div class="btn-group">';
                $btn .= '<button data-toggle="tooltip" title="Edit" type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$mahasiswa->nim.'"><i class="fas fa-edit"></i></button>';
                $btn .= '<button data-toggle="tooltip" title="Hapus" type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$mahasiswa->nim.'"><i class="fas fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['nama_prodi', 'nama_fakultas', 'tahun_akademik', 'action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $query->where(function($q) use ($searchValue) {
                        $q->where('nim', 'like', "%{$searchValue}%")
                          ->orWhere('nama', 'like', "%{$searchValue}%")
                          ->orWhereHas('programStudi', function($q) use ($searchValue) {
                              $q->where('nama_prodi', 'like', "%{$searchValue}%");
                          })
                          ->orWhereHas('programStudi.fakultas', function($q) use ($searchValue) {
                              $q->where('nama_fakultas', 'like', "%{$searchValue}%");
                          })
                          ->orWhereHas('tahunAkademik', function($q) use ($searchValue) {
                              $q->where('tahun_ajaran', 'like', "%{$searchValue}%")
                                ->orWhere('tipe_semester', 'like', "%{$searchValue}%");
                          });
                    });
                }
            })
            ->orderColumn('nama_prodi', function ($query, $direction) {
                $query->leftJoin('program_studis', 'mahasiswas.prodi_id', '=', 'program_studis.id')
                      ->orderBy('program_studis.nama_prodi', $direction)
                      ->select('mahasiswas.*');
            })
            ->orderColumn('nama_fakultas', function ($query, $direction) {
                $query->leftJoin('program_studis', 'mahasiswas.prodi_id', '=', 'program_studis.id')
                      ->leftJoin('fakultas', 'program_studis.fakultas_id', '=', 'fakultas.id')
                      ->orderBy('fakultas.nama_fakultas', $direction)
                      ->select('mahasiswas.*');
            })
            ->orderColumn('tahun_akademik', function ($query, $direction) {
                $query->leftJoin('tahun_akademiks', 'mahasiswas.tahun_akademik_id', '=', 'tahun_akademiks.id')
                      ->orderBy('tahun_akademiks.tahun_ajaran', $direction)
                      ->orderBy('tahun_akademiks.tipe_semester', $direction)
                      ->select('mahasiswas.*');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:9',
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:program_studis,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $mahasiswa = new Mahasiswa();
        $mahasiswa->nim = $request->nim;
        $mahasiswa->nama = $request->nama;
        $mahasiswa->prodi_id = $request->prodi_id;
        $mahasiswa->tahun_akademik_id = $request->tahun_akademik_id;
        $mahasiswa->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data' => $mahasiswa
        ]);
    }

    public function show($nim)
    {
        $mahasiswa = Mahasiswa::find($nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mahasiswa
        ]);
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::find($nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:9',
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:program_studis,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $mahasiswa->nim = $request->nim;
        $mahasiswa->nama = $request->nama;
        $mahasiswa->prodi_id = $request->prodi_id;
        $mahasiswa->tahun_akademik_id = $request->tahun_akademik_id;
        $mahasiswa->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Mahasiswa berhasil diperbarui',
            'data' => $mahasiswa
        ]);
    }

    public function destroy($nim)
    {
        $mahasiswa = Mahasiswa::find($nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        $mahasiswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Mahasiswa berhasil dihapus'
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

            $filename = 'template_import_mahasiswa.xlsx';

            // Gunakan MahasiswaExportTemplate untuk membuat template
            return Excel::download(new MahasiswaExportTemplate(), $filename);
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
            'file' => 'required|file|mimes:xlsx,xls',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Hitung jumlah data sebelum import
            $countBefore = Mahasiswa::count();

            // Buat instance importer dengan tahun akademik ID
            $import = new MahasiswaImport($request->tahun_akademik_id);

            // Impor data menggunakan Laravel Excel
            Excel::import($import, $request->file('file'));

            // Hitung jumlah data setelah import
            $countAfter = Mahasiswa::count();

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

            // Inisialisasi array untuk menyimpan data yang cocok dan tidak cocok
            $matchedData = [];
            $unmatchedData = [];

            // Ambil semua data mahasiswa yang ada di database
            $existingData = Mahasiswa::select('nim', 'nama')->get();

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

                if ($found) {
                    $matchedData[] = [
                        'nim' => $nim,
                        'nama' => $nama
                    ];
                } else {
                    $unmatchedData[] = [
                        'nim' => $nim,
                        'nama' => $nama
                    ];
                }
            }

            // Hitung jumlah data yang cocok dan tidak cocok
            $matchedCount = count($matchedData);
            $unmatchedCount = count($unmatchedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Validasi data berhasil',
                'data' => [
                    'total_data' => $totalData,
                    'matched_count' => $matchedCount,
                    'unmatched_count' => $unmatchedCount,
                    'matched_data' => $matchedData,
                    'unmatched_data' => $unmatchedData
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
     * Export unmatched data to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUnmatchedData(Request $request)
    {
        try {
            $unmatchedData = json_decode($request->input('unmatched_data'), true);

            if (empty($unmatchedData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang tidak cocok untuk diekspor'
                ], 422);
            }

            return Excel::download(new MahasiswaUnmatchedDataExport($unmatchedData), 'data-tidak-cocok-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
           
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }
}