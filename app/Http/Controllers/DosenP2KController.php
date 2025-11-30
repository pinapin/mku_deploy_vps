<?php

namespace App\Http\Controllers;

use App\Models\KelasDosenP2K;
use App\Models\LaporanAkhirMahasiswa;
use App\Models\PKS;
use App\Models\SuratPengantar;
use App\Models\SuratPengantarMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DosenP2KController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $kode;
    public function __construct()
    {
        $this->kode = Session::get('kode');
    }
    public function index()
    {
        // Ambil tahun akademik berdasarkan dosen yang login
        $tahunAkademiks = KelasDosenP2K::where('kode_dosen', $this->kode)
            ->with('tahunAkademik')
            ->select('tahun_akademik_id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->tahunAkademik;
            });
        
        return view('pages.dosen.p2k.index', compact('tahunAkademiks'));
    }
    
    /**
     * Get kelas by tahun akademik.
     */
    public function getKelasByTahunAkademik(Request $request)
    {
        $tahunAkademikId = $request->tahun_akademik_id;
        
        // Get classes with student count using subquery
        $kelas = KelasDosenP2K::where('kode_dosen', $this->kode)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->select('kelas_dosen_p2_k_s.*')
            ->selectSub(function($query) use ($tahunAkademikId) {
                $query->from('surat_pengantar_mahasiswas')
                    ->join('surat_pengantars', 'surat_pengantar_mahasiswas.surat_pengantar_id', '=', 'surat_pengantars.id')
                    ->where('surat_pengantars.tahun_akademik_id', $tahunAkademikId)
                    ->whereColumn('surat_pengantars.kelas', 'kelas_dosen_p2_k_s.kelas')
                    ->selectRaw('COUNT(*)');
            }, 'jumlah_mahasiswa')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }
    
    /**
     * Display detail kelas.
     */
    public function detailKelas($id)
    {

        $kelas = KelasDosenP2K::where('id', $id)
            ->where('kode_dosen', $this->kode)
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
            
            // Cek apakah ada mahasiswa yang sudah upload laporan akhir dengan file PKS
            $laporanAkhirWithPKS = LaporanAkhirMahasiswa::whereIn('nim', $suratPengantar->suratPengantarMahasiswas->pluck('nim'))
                ->where('tahun_akademik_id', $kelas->tahun_akademik_id)
                ->whereNotNull('file_pks')
                ->first();
            
            $kelompokMahasiswa[$kelompok]['pks'] = $pks ? [
                'id' => $pks->id,
                'file_path' => $laporanAkhirWithPKS ? $laporanAkhirWithPKS->file_pks : null, // Gunakan file PKS dari laporan akhir jika ada
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
        
        return view('pages.dosen.p2k.detail-kelas', compact('kelas', 'kelompokMahasiswa'));
    }
    
    /**
     * Validate laporan akhir.
     */
    public function validateLaporanAkhir(Request $request, $id)
    {
        $laporanAkhir = LaporanAkhirMahasiswa::findOrFail($id);
        
        // Cek apakah kelas dimiliki oleh dosen yang login
        $kelas = KelasDosenP2K::where('id', $laporanAkhir->kelas_dosen_p2k_id)
            ->where('kode_dosen', $this->kode)
            ->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'catatan_validasi' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $laporanAkhir->is_validated = true;
        $laporanAkhir->catatan_validasi = $request->catatan_validasi;
        $laporanAkhir->validated_at = now();
        $laporanAkhir->validated_by = $this->kode;
        $laporanAkhir->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil divalidasi',
            'data' => $laporanAkhir
        ]);
    }
}