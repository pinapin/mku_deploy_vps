<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DosenP2K;
use App\Models\KelasDosenP2K;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Models\SuratPengantarMahasiswa;
use App\Models\LaporanAkhirMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PKS;
use App\Models\SuratPengantar;
use Illuminate\Support\Facades\DB;

class LaporanP2KController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil daftar tahun akademik
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();

        return view('pages.admin.laporan-p2k.index', compact('tahunAkademiks'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData()
    {
        $tahunAkademiks = TahunAkademik::select(
            'tahun_akademiks.id',
            'tahun_akademiks.tahun_ajaran',
            'tahun_akademiks.tipe_semester',
            'tahun_akademiks.is_aktif',
            DB::raw('COUNT(kelas_dosen_p2_k_s.id) as jumlah_kelas')
        )
            ->leftJoin('kelas_dosen_p2_k_s', 'tahun_akademiks.id', '=', 'kelas_dosen_p2_k_s.tahun_akademik_id')
            ->groupBy('tahun_akademiks.id', 'tahun_akademiks.tahun_ajaran', 'tahun_akademiks.tipe_semester', 'tahun_akademiks.is_aktif')
            ->orderBy('tahun_akademiks.tahun_ajaran', 'desc')
            ->get();

        // Tambahkan id terenkripsi untuk setiap tahun akademik
        $tahunAkademiks->map(function ($item) {
            $item->id_encrypted = encrypt($item->id);
            return $item;
        });

        return response()->json(['data' => $tahunAkademiks]);
    }

    /**
     * Show the form for displaying classes for a specific academic year.
     */
    public function showKelas($tahunAkademikId)
    {
        try {
            // Coba dekripsi ID tahun akademik
            try {
                $decryptedId = decrypt($tahunAkademikId);
            } catch (\Exception $decryptException) {
                // Jika gagal dekripsi, coba gunakan ID langsung (mungkin sudah tidak terenkripsi)
                $decryptedId = $tahunAkademikId;
            }

            $tahunAkademik = TahunAkademik::findOrFail($decryptedId);
            $dosens = DosenP2K::all();
        } catch (\Exception $e) {
            abort(404, 'Tahun Akademik tidak ditemukan: ' . $e->getMessage());
        }

        return view('pages.admin.laporan-p2k.kelas', compact('tahunAkademik', 'dosens'));
    }

    /**
     * Get class data for a specific academic year.
     */
    public function getKelasData(Request $request, $tahunAkademikId)
    {
        try {
            $decryptedId = $tahunAkademikId;
            $dosenId = $request->query('dosen_id');

            // Jika ada parameter dosen_id, ambil data kelas untuk dosen tersebut
            if ($dosenId) {
                $kelasData = KelasDosenP2K::select(
                    'kelas_dosen_p2_k_s.id',
                    'kelas_dosen_p2_k_s.kelas',
                    'kelas_dosen_p2_k_s.kode_dosen',
                    DB::raw('COUNT(DISTINCT surat_pengantar_mahasiswas.id) as jumlah_mahasiswa'),
                    DB::raw('COUNT(DISTINCT surat_pengantars.kelompok) as jumlah_kelompok'),
                    DB::raw('COUNT(DISTINCT CASE WHEN laporan_akhir_mahasiswas.is_validated = 1 THEN surat_pengantars.id END) as laporan_tervalidasi')
                )
                    ->leftJoin('surat_pengantars', function ($join) {
                        $join->on('kelas_dosen_p2_k_s.kelas', '=', 'surat_pengantars.kelas')
                            ->on('kelas_dosen_p2_k_s.tahun_akademik_id', '=', 'surat_pengantars.tahun_akademik_id');
                    })
                    ->leftJoin('surat_pengantar_mahasiswas', 'surat_pengantars.id', '=', 'surat_pengantar_mahasiswas.surat_pengantar_id')
                    ->leftJoin('laporan_akhir_mahasiswas', function ($join) {
                        $join->on('surat_pengantar_mahasiswas.nim', '=', 'laporan_akhir_mahasiswas.nim')
                            ->on('surat_pengantars.tahun_akademik_id', '=', 'laporan_akhir_mahasiswas.tahun_akademik_id');
                    })
                    ->where('kelas_dosen_p2_k_s.tahun_akademik_id', $decryptedId)
                    ->where('kelas_dosen_p2_k_s.kode_dosen', $dosenId)
                    ->groupBy('kelas_dosen_p2_k_s.id', 'kelas_dosen_p2_k_s.kelas', 'kelas_dosen_p2_k_s.kode_dosen')
                    ->get();

                // Tambahkan id terenkripsi untuk setiap kelas
                $kelasData->map(function ($item) {
                    $item->id_encrypted = encrypt($item->id);
                    return $item;
                });
            } else {
                // Jika tidak ada parameter dosen_id, ambil data dosen dengan jumlah kelas
                $kelasData = KelasDosenP2K::select(
                    'kelas_dosen_p2_k_s.kode_dosen',
                    'dosen_p2_k_s.nama_dosen as dosen_name',
                    DB::raw('COUNT(DISTINCT kelas_dosen_p2_k_s.kelas) as jumlah_kelas'),
                    DB::raw('COUNT(DISTINCT surat_pengantar_mahasiswas.id) as jumlah_mahasiswa'),
                    DB::raw('COUNT(DISTINCT CASE WHEN laporan_akhir_mahasiswas.is_validated = 1 THEN surat_pengantars.id END) as laporan_tervalidasi')
                )
                    ->join('dosen_p2_k_s', 'kelas_dosen_p2_k_s.kode_dosen', '=', 'dosen_p2_k_s.kode_dosen')
                    ->leftJoin('surat_pengantars', function ($join) {
                        $join->on('kelas_dosen_p2_k_s.kelas', '=', 'surat_pengantars.kelas')
                            ->on('kelas_dosen_p2_k_s.tahun_akademik_id', '=', 'surat_pengantars.tahun_akademik_id');
                    })
                    ->leftJoin('surat_pengantar_mahasiswas', 'surat_pengantars.id', '=', 'surat_pengantar_mahasiswas.surat_pengantar_id')
                    ->leftJoin('laporan_akhir_mahasiswas', function ($join) {
                        $join->on('surat_pengantar_mahasiswas.nim', '=', 'laporan_akhir_mahasiswas.nim')
                            ->on('surat_pengantars.tahun_akademik_id', '=', 'laporan_akhir_mahasiswas.tahun_akademik_id');
                    })
                    ->where('kelas_dosen_p2_k_s.tahun_akademik_id', $decryptedId)
                    ->groupBy('kelas_dosen_p2_k_s.kode_dosen', 'dosen_p2_k_s.nama_dosen')
                    ->get();

                // Tambahkan id terenkripsi untuk setiap dosen
                $kelasData->map(function ($item) {
                    $item->id_encrypted = encrypt($item->kode_dosen);
                    return $item;
                });
            }

            // Return data sebagai JSON response
            return response()->json(['data' => $kelasData]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tahun Akademik tidak ditemukan: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Display the detail of a specific class.
     */
    public function detailKelas($id)
    {
        $kelas_dosen_id = decrypt($id);

        $kelas = KelasDosenP2K::where('id', $kelas_dosen_id)
            ->with('tahunAkademik')
            ->firstOrFail();

        // Ambil data surat pengantar berdasarkan kelas
        $suratPengantars = SuratPengantar::where('kelas', $kelas->kelas)
            ->where('tahun_akademik_id', $kelas->tahun_akademik_id)
            ->with(['suratPengantarMahasiswas.programStudi', 'umkm'])
            ->get();

        // Kelompokkan data mahasiswa berdasarkan kelompok
        $kelompokMahasiswa = [];
        $no = 1;

        foreach ($suratPengantars as $suratPengantar) {
            $kelompok = $suratPengantar->kelompok;

            if (!isset($kelompokMahasiswa[$kelompok])) {
                $kelompokMahasiswa[$kelompok] = [
                    'no' => $no++,
                    'kelompok' => $kelompok,
                    'umkm' => $suratPengantar->umkm->nama_umkm,
                    'mahasiswas' => [],
                    'surat_pengantar' => [
                        'id' => $suratPengantar->id,
                        'file_path' => null, // Tambahkan path file jika ada
                    ],
                ];
            }


            // Ambil data PKS berdasarkan UMKM
            $pks = PKS::where('umkm_id', $suratPengantar->umkm_id)
                ->where('tahun_akademik_id', $kelas->tahun_akademik_id)
                ->first();

            $laporanAkhirWithPKS = LaporanAkhirMahasiswa::whereIn('nim', $suratPengantar->suratPengantarMahasiswas->pluck('nim'))
                ->where('tahun_akademik_id', $kelas->tahun_akademik_id)
                ->whereNotNull('file_pks')
                ->first();

            $kelompokMahasiswa[$kelompok]['pks'] = $pks ? [
                'id' => $pks->id,
                'file_path' => $laporanAkhirWithPKS ? $laporanAkhirWithPKS->file_pks : null, // Tambahkan path file jika ada
            ] : null;

             $kelompokMahasiswa[$kelompok]['ia'] = $pks ? [
                'id' => $pks->id,
                'file_path' => $laporanAkhirWithPKS ? $laporanAkhirWithPKS->file_ia : null, // Gunakan file IA dari laporan akhir jika ada
            ] : null;

            // Tambahkan mahasiswa ke kelompok
            foreach ($suratPengantar->suratPengantarMahasiswas as $mahasiswa) {
                // Cek apakah mahasiswa sudah upload laporan akhir
                $laporanAkhir = LaporanAkhirMahasiswa::where('nim', $mahasiswa->nim)
                    ->where('tahun_akademik_id', $kelas->tahun_akademik_id)
                    ->where('kelas_dosen_p2k_id', $kelas->id)
                    ->first();

                $kelompokMahasiswa[$kelompok]['mahasiswas'][] = [
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama_mahasiswa,
                    'program_studi' => $mahasiswa->programStudi ? $mahasiswa->programStudi->nama_prodi : null,
                    'laporan_akhir' => $laporanAkhir ? [
                        'id' => $laporanAkhir->id,
                        'file_path' => $laporanAkhir->file_path,
                        'is_validated' => $laporanAkhir->is_validated,
                        'validated_at' => $laporanAkhir->validated_at,
                        'catatan_validasi' => $laporanAkhir->catatan_validasi,
                    ] : null,
                ];
            }
        }

        return view('pages.admin.laporan-p2k.detail-kelas', compact('kelas', 'kelompokMahasiswa'));
    }

    /**
     * Validate laporan akhir.
     */
    // public function validateLaporanAkhir(Request $request)
    // {
    //     $user = Auth::user();

    //     $validator = Validator::make($request->all(), [
    //         'kelompok_id' => 'required|string',
    //         'catatan_validasi' => 'nullable|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validasi gagal',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         // Cari laporan akhir berdasarkan kelompok (kelompok_id adalah string nomor kelompok)
    //         $laporanAkhir = LaporanAkhirMahasiswa::where('kelompok', $request->kelompok_id)
    //             ->where('is_validated', false)
    //             ->first();

    //         if (!$laporanAkhir) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Laporan akhir tidak ditemukan atau sudah divalidasi'
    //             ], 404);
    //         }

    //         $laporanAkhir->is_validated = true;
    //         $laporanAkhir->catatan_validasi = $request->catatan_validasi;
    //         $laporanAkhir->validated_at = now();
    //         $laporanAkhir->validated_by = $user->id;
    //         $laporanAkhir->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Laporan berhasil divalidasi',
    //             'data' => $laporanAkhir
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}
