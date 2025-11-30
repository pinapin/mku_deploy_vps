<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\SuratPengantar;
use App\Models\SuratPengantarMahasiswa;
use App\Models\PKS;
use App\Models\SertifikatKwu;
use App\Models\KelasDosenP2K;
use App\Models\LaporanAkhirMahasiswa;
use App\Models\TahunAkademik;
use App\Models\SesiUjian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Session::get('role');

        if ($user === 'admin') {
            return $this->adminDashboard();
        } else if ($user === 'mahasiswa') {
            return $this->mahasiswaDashboard();
        } else if ($user === 'dosen') {
            return $this->dosenDashboard();
        } else if ($user === 'tamu') {
            return $this->tamuDashboard();
        }

        // Default dashboard jika role tidak dikenali
        return view('pages.dashboard');
    }

    private function adminDashboard()
    {
        // Statistik jumlah Surat Pengantar
        $totalSuratPengantar = SuratPengantar::count();

        // Statistik jumlah PKS
        $totalPKS = PKS::count();

        // Statistik jumlah UMKM
        $totalUMKM = Umkm::count();

        // Statistik jumlah Sertifikat KWU
        $totalSertifikatKwu = SertifikatKwu::count();

        // Statistik Surat Pengantar per bulan dalam setahun terakhir
        $suratPengantarPerBulan = SuratPengantar::select(
            DB::raw('MONTH(tgl_surat) as bulan'),
            DB::raw('YEAR(tgl_surat) as tahun'),
            DB::raw('count(*) as total')
        )
            ->whereRaw('tgl_surat >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // Statistik PKS per bulan dalam setahun terakhir
        $pksPerBulan = PKS::select(
            DB::raw('MONTH(tgl_pks) as bulan'),
            DB::raw('YEAR(tgl_pks) as tahun'),
            DB::raw('count(*) as total')
        )
            ->whereRaw('tgl_pks >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // Data untuk tabel Surat Pengantar terbaru
        $suratPengantarTerbaru = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk tabel PKS terbaru
        $pksTerbaru = PKS::with('umkm')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik Sertifikat KWU per fakultas
        $sertifikatKwuPerFakultas = SertifikatKwu::select(
            'fakultas.nama_fakultas',
            DB::raw('count(*) as total')
        )
            ->join('program_studis', 'sertifikat_kwus.prodi_id', '=', 'program_studis.id')
            ->join('fakultas', 'program_studis.fakultas_id', '=', 'fakultas.id')
            ->groupBy('fakultas.nama_fakultas')
            ->get();

        // Statistik Sertifikat KWU per tahun
        $sertifikatKwuPerTahun = SertifikatKwu::select(
            'tahun',
            DB::raw('count(*) as total')
        )
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        // Statistik jumlah sertifikat KWU per program studi
        $sertifikatKwuPerProdi = SertifikatKwu::select(
            'program_studis.nama_prodi',
            DB::raw('count(*) as total')
        )
            ->join('program_studis', 'sertifikat_kwus.prodi_id', '=', 'program_studis.id')
            ->groupBy('program_studis.nama_prodi')
            ->orderBy('total', 'desc')
            ->get();

        // Data untuk tabel Sertifikat KWU terbaru
        $sertifikatKwuTerbaru = SertifikatKwu::with('programStudi.fakultas')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalSuratPengantar',
            'totalPKS',
            'totalUMKM',
            'totalSertifikatKwu',
            'suratPengantarPerBulan',
            'pksPerBulan',
            'suratPengantarTerbaru',
            'pksTerbaru',
            'sertifikatKwuPerFakultas',
            'sertifikatKwuPerTahun',
            'sertifikatKwuPerProdi',
            'sertifikatKwuTerbaru'
        ));
    }

    private function mahasiswaDashboard()
    {
        $nim = Session::get('kode');
        $tahunAkademikAktif = TahunAkademik::getActive();

        // Get tahun akademik aktif ID
        $tahunAkademikId = $tahunAkademikAktif ? $tahunAkademikAktif->id : null;

        // Statistik jumlah UMKM yang diinput mahasiswa (tahun akademik aktif)
        $totalUMKM = Umkm::where('input_by', $nim)
            ->count();

        // Statistik jumlah Surat Pengantar yang diinput mahasiswa (tahun akademik aktif)
        $totalSuratPengantar = SuratPengantar::where('input_by', $nim)
            ->when($tahunAkademikId, function($query, $tahunAkademikId) {
                return $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->count();

        // Statistik jumlah PKS yang diinput mahasiswa (tahun akademik aktif)
        $totalPKS = PKS::where('created_by', $nim)
            ->when($tahunAkademikId, function($query, $tahunAkademikId) {
                return $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->count();

        // Statistik jumlah Laporan Akhir mahasiswa (tahun akademik aktif)
        $totalLaporanAkhir = LaporanAkhirMahasiswa::where('nim', $nim)
            ->when($tahunAkademikId, function($query, $tahunAkademikId) {
                return $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->count();

        // Data UMKM yang diinput mahasiswa (tahun akademik aktif) - hanya 1 data
        $umkms = Umkm::where('input_by', $nim)
            ->with('kategoriUmkm')
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // Data Surat Pengantar yang diinput mahasiswa (tahun akademik aktif) - hanya 1 data
        $suratPengantars = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas'])
            ->where('input_by', $nim)
            ->when($tahunAkademikId, function($query, $tahunAkademikId) {
                return $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // Data PKS yang diinput mahasiswa (tahun akademik aktif) - hanya 1 data
        $pksList = PKS::with('umkm')
            ->where('created_by', $nim)
            ->when($tahunAkademikId, function($query, $tahunAkademikId) {
                return $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // Data Laporan Akhir mahasiswa (tahun akademik aktif) - hanya 1 data
        $laporanAkhirList = LaporanAkhirMahasiswa::where('nim', $nim)
            ->when($tahunAkademikId, function($query, $tahunAkademikId) {
                return $query->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->with('tahunAkademik')
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // Statistik UMKM per kategori (tahun akademik aktif)
        $umkmPerKategori = Umkm::select('kategori_umkms.nama_kategori', DB::raw('count(*) as total'))
            ->join('kategori_umkms', 'umkms.kategori_umkm_id', '=', 'kategori_umkms.id')
            ->where('umkms.input_by', $nim)
            ->groupBy('kategori_umkms.nama_kategori')
            ->get();

        // Check if there's an ongoing exam session
        $activeExamSession = SesiUjian::where('nim', $nim)
            ->where('waktu_selesai', null)
            ->with('ujian')
            ->first();

        // Add encrypted URL for active exam session
        if ($activeExamSession) {
            $activeExamSession->encrypted_id = \App\Services\UrlEncryptionService::encryptId($activeExamSession->id);
        }

        // Check if there are any available active exams
        $availableExams = \App\Models\Ujian::where('is_active', true)
            ->whereHas('soal')
            ->get();

        $hasAvailableExams = $availableExams->count() > 0;

        return view('pages.dashboard', compact(
            'totalUMKM',
            'totalSuratPengantar',
            'totalPKS',
            'totalLaporanAkhir',
            'umkms',
            'suratPengantars',
            'pksList',
            'laporanAkhirList',
            'umkmPerKategori',
            'tahunAkademikAktif',
            'activeExamSession',
            'hasAvailableExams'
        ));
    }
    
    private function dosenDashboard()
    {
        $kode = Session::get('kode');
        
        // Statistik jumlah kelas yang diampu dosen
        $totalKelas = KelasDosenP2K::where('kode_dosen', $kode)->count();
        
        // Statistik jumlah laporan akhir mahasiswa yang sudah divalidasi
        $totalLaporanValidated = LaporanAkhirMahasiswa::whereHas('kelasDosenP2K', function($query) use ($kode) {
                $query->where('kode_dosen', $kode);
            })
            ->where('is_validated', true)
            ->count();
        
        // Statistik jumlah laporan akhir mahasiswa yang belum divalidasi
        $totalLaporanPending = LaporanAkhirMahasiswa::whereHas('kelasDosenP2K', function($query) use ($kode) {
                $query->where('kode_dosen', $kode);
            })
            ->where('is_validated', false)
            ->count();
            
        // Statistik jumlah total mahasiswa di semua kelas yang diampu
        $totalMahasiswa = SuratPengantarMahasiswa::whereHas('suratPengantar', function($query) use ($kode) {
                // Get kelas and tahun_akademik_id pairs from kelas_dosen_p2k_s
                $kelasDosenP2K = KelasDosenP2K::where('kode_dosen', $kode)
                    ->select('kelas', 'tahun_akademik_id')
                    ->get();
                
                // Build query with OR conditions for each kelas and tahun_akademik_id pair
                $query->where(function($q) use ($kelasDosenP2K) {
                    foreach ($kelasDosenP2K as $index => $kelas) {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $q->$method(function($subQ) use ($kelas) {
                            $subQ->where('kelas', $kelas->kelas)
                                 ->where('tahun_akademik_id', $kelas->tahun_akademik_id);
                        });
                    }
                });
            })
            ->count();
        
        // Data kelas yang diampu dosen berdasarkan tahun akademik
        $kelasByTahunAkademikData = KelasDosenP2K::where('kode_dosen', $kode)
            ->with('tahunAkademik')
            ->get()
            ->groupBy(function($item) {
                return $item->tahunAkademik->tahun_ajaran . ' ' . $item->tahunAkademik->tipe_semester;
            })
            ->map(function($items) {
                return $items->count();
            });
            
        // Format data untuk chart kelas per tahun akademik
        $kelasByTahunAkademik = collect();
        foreach ($kelasByTahunAkademikData as $tahunAkademik => $jumlah) {
            $kelasByTahunAkademik->push([
                'tahun_akademik' => $tahunAkademik,
                'jumlah' => $jumlah
            ]);
        }
        
        // Data laporan akhir terbaru yang belum divalidasi
        $laporanPending = LaporanAkhirMahasiswa::whereHas('kelasDosenP2K', function($query) use ($kode) {
                $query->where('kode_dosen', $kode);
            })
            ->with(['mahasiswa', 'kelasDosenP2K', 'tahunAkademik'])
            ->where('is_validated', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Data kelas terbaru yang diampu dosen (hanya tahun akademik aktif)
        $kelasTerbaru = KelasDosenP2K::where('kode_dosen', $kode)
            ->with(['tahunAkademik', 'laporanAkhirMahasiswas'])
            ->whereHas('tahunAkademik', function($query) {
                $query->where('is_aktif', 1);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($kelas) {
                // Hitung jumlah mahasiswa unik per kelas berdasarkan NIM
                $jumlahMahasiswa = $kelas->laporanAkhirMahasiswas()
                    ->distinct('nim')
                    ->count('nim');

                $kelas->jumlah_mahasiswa = $jumlahMahasiswa;
                return $kelas;
            });
        
        // Statistik validasi laporan per kelas (untuk chart)
        $validasiPerKelas = KelasDosenP2K::where('kode_dosen', $kode)
            ->with(['laporanAkhirMahasiswas', 'tahunAkademik'])
            ->get()
            ->map(function($kelas) {
                $validated = $kelas->laporanAkhirMahasiswas->where('is_validated', true)->count();
                $pending = $kelas->laporanAkhirMahasiswas->where('is_validated', false)->count();
                $total = $validated + $pending;
                
                return [
                    'kelas' => $kelas->kelas . ' (' . $kelas->tahunAkademik->tahun_ajaran . ' ' . $kelas->tahunAkademik->tipe_semester . ')',
                    'validated_count' => $validated,
                    'pending_count' => $pending,
                    'total_count' => $total,
                    'validated_percent' => $total > 0 ? round(($validated / $total) * 100) : 0,
                    'pending_percent' => $total > 0 ? round(($pending / $total) * 100) : 0
                ];
            })
            ->sortByDesc('total_count')
            ->take(5)
            ->values();
        
        return view('pages.dashboard', compact(
            'totalKelas',
            'totalLaporanValidated',
            'totalLaporanPending',
            'totalMahasiswa',
            'kelasByTahunAkademik',
            'laporanPending',
            'kelasTerbaru',
            'validasiPerKelas'
        ));
    }

    private function tamuDashboard()
    {
        // Statistik jumlah Surat Pengantar
        $totalSuratPengantar = SuratPengantar::count();

        // Statistik jumlah PKS
        $totalPKS = PKS::count();

        // Statistik jumlah UMKM
        $totalUMKM = Umkm::count();

        // Statistik jumlah Sertifikat KWU
        $totalSertifikatKwu = SertifikatKwu::count();

        // Statistik Surat Pengantar per bulan dalam setahun terakhir
        $suratPengantarPerBulan = SuratPengantar::select(
            DB::raw('MONTH(tgl_surat) as bulan'),
            DB::raw('YEAR(tgl_surat) as tahun'),
            DB::raw('count(*) as total')
        )
            ->whereRaw('tgl_surat >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // Statistik PKS per bulan dalam setahun terakhir
        $pksPerBulan = PKS::select(
            DB::raw('MONTH(tgl_pks) as bulan'),
            DB::raw('YEAR(tgl_pks) as tahun'),
            DB::raw('count(*) as total')
        )
            ->whereRaw('tgl_pks >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // Data untuk tabel Surat Pengantar terbaru
        $suratPengantarTerbaru = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk tabel PKS terbaru
        $pksTerbaru = PKS::with('umkm')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik Sertifikat KWU per fakultas
        $sertifikatKwuPerFakultas = SertifikatKwu::select(
            'fakultas.nama_fakultas',
            DB::raw('count(*) as total')
        )
            ->join('program_studis', 'sertifikat_kwus.prodi_id', '=', 'program_studis.id')
            ->join('fakultas', 'program_studis.fakultas_id', '=', 'fakultas.id')
            ->groupBy('fakultas.nama_fakultas')
            ->get();

        // Statistik Sertifikat KWU per tahun
        $sertifikatKwuPerTahun = SertifikatKwu::select(
            'tahun',
            DB::raw('count(*) as total')
        )
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        // Statistik jumlah sertifikat KWU per program studi
        $sertifikatKwuPerProdi = SertifikatKwu::select(
            'program_studis.nama_prodi',
            DB::raw('count(*) as total')
        )
            ->join('program_studis', 'sertifikat_kwus.prodi_id', '=', 'program_studis.id')
            ->groupBy('program_studis.nama_prodi')
            ->orderBy('total', 'desc')
            ->get();

        // Data untuk tabel Sertifikat KWU terbaru
        $sertifikatKwuTerbaru = SertifikatKwu::with('programStudi.fakultas')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalSuratPengantar',
            'totalPKS',
            'totalUMKM',
            'totalSertifikatKwu',
            'suratPengantarPerBulan',
            'pksPerBulan',
            'suratPengantarTerbaru',
            'pksTerbaru',
            'sertifikatKwuPerFakultas',
            'sertifikatKwuPerTahun',
            'sertifikatKwuPerProdi',
            'sertifikatKwuTerbaru'
        ));
    }
}
