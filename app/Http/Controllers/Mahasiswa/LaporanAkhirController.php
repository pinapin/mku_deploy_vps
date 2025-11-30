<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KelasDosenP2K;
use App\Models\LaporanAkhirMahasiswa;
use App\Models\Mahasiswa;
use App\Models\PKS;
use App\Models\SuratPengantar;
use App\Models\SuratPengantarMahasiswa;
use App\Models\TahunAkademik;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LaporanAkhirController extends Controller
{
    protected $nim;
    protected $role;

    public function __construct()
    {
        $this->nim = session('kode');
        $this->role = session('role');
    }

    public function index()
    {
        $nim = $this->nim;
        if (!$nim) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $tahunAkademikAktif = TahunAkademik::getActive();

        // Ambil data surat pengantar yang dimiliki mahasiswa
        $suratPengantars = SuratPengantar::with('tahunAkademik')
            ->where('input_by', $nim)
            ->get();
            
        $uploadPks = '';
        $cekpks = PKS::where('tahun_akademik_id', $tahunAkademikAktif->id)
            ->where('created_by', $nim)
            ->first();
        if ($cekpks) {
            if (!$cekpks->file_arsip_pks) {
                $uploadPks = false;
            } else {
                $uploadPks = true;
            }
        }

        // Ambil data laporan akhir yang sudah diupload
        $laporanAkhirs = LaporanAkhirMahasiswa::where('nim', $nim)
            ->with(['tahunAkademik', 'kelasDosenP2K', 'validator'])
            ->get();

        // Ambil data tahun akademik aktif
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();

        return view('pages.mahasiswa.laporan-akhir.index', compact('suratPengantars', 'laporanAkhirs', 'tahunAkademikAktif', 'uploadPks', 'cekpks'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData()
    {
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $laporanAkhirs = LaporanAkhirMahasiswa::where('nim', $mahasiswa->nim)
            ->with(['tahunAkademik', 'kelasDosenP2K', 'validator'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tahun_akademik' => $item->tahunAkademik ? $item->tahunAkademik->tahun_ajaran . ' ' . $item->tahunAkademik->tipe_semester : '-',
                    'kelas' => $item->kelas,
                    'kelompok' => $item->kelompok,
                    'file_path' => $item->file_path,
                    'is_validated' => $item->is_validated,
                    'validated_at' => $item->validated_at ? $item->validated_at->format('d-m-Y H:i:s') : null,
                    'validator' => $item->validator ? $item->validator->name : null,
                    'catatan_validasi' => $item->catatan_validasi
                ];
            });

            $tahunAkademik = TahunAkademik::getActive();

            // Hitung jumlah laporan akhir yang sudah dibuat oleh mahasiswa pada tahun akademik aktif
            $totalLaporanAkhir = LaporanAkhirMahasiswa::where('nim', $mahasiswa->nim)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->count();

        return response()->json([
            'success' => true,
            'data' => $laporanAkhirs,
            'hasLaporanAkhir' => $totalLaporanAkhir > 0
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();
        $today = date('Y-m-d');
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'surat_pengantar_id' => 'required|exists:surat_pengantars,id',
            'file_laporan' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'file_pks' => 'nullable', // Max 10MB untuk file PKS
            'file_ia' => 'required|file|mimes:pdf|max:10240', // Max 10MB untuk file IA
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah surat pengantar milik mahasiswa ini
        $suratPengantar = SuratPengantar::findOrFail($request->surat_pengantar_id);
        $suratPengantarMahasiswa = SuratPengantarMahasiswa::where('surat_pengantar_id', $suratPengantar->id)
            ->where('nim', $mahasiswa->nim)
            ->first();

        if (!$suratPengantarMahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak terdaftar dalam surat pengantar ini.'
            ], 403);
        }

        // Cek apakah tahun akademik surat pengantar sesuai dengan yang dipilih
        if ($suratPengantar->tahun_akademik_id != $request->tahun_akademik_id) {
            return response()->json([
                'success' => false,
                'message' => 'Surat pengantar tidak sesuai dengan tahun akademik yang dipilih.'
            ], 422);
        }

        // Cek apakah sudah ada laporan akhir untuk tahun akademik ini
        $existingLaporan = LaporanAkhirMahasiswa::where('nim', $mahasiswa->nim)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->first();

        if ($existingLaporan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengupload laporan akhir untuk tahun akademik ini.'
            ], 403);
        }

        // Cari kelas dosen P2K berdasarkan kelas dan tahun akademik
        $kelasDosenP2K = KelasDosenP2K::where('kelas', $suratPengantar->kelas)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->first();

        if (!$kelasDosenP2K) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas dosen P2K tidak ditemukan.'
            ], 404);
        }



        // Upload file PKS
        $cekpks = PKS::where('umkm_id', $suratPengantar->umkm_id)
            ->whereRaw('DATE_ADD(tgl_pks, INTERVAL lama_perjanjian YEAR) >= ?', [$today])
            ->first();

        if (!$cekpks) {
            $umkm = Umkm::where('id', $suratPengantar->umkm_id)->first();
            return response()->json([
                'success' => false,
                'message' => 'PKS UMKM ' . $umkm->nama_umkm . ' tidak ditemukan atau sudah kadaluarsa.'
            ], 404);
        } else {
            $filePathPks = $cekpks->file_arsip_pks;
        }

        // $filePks = $request->file('file_pks');
        // $fileNamePks = 'pks_' . $mahasiswa->nim . '_' . time() . '.' . $filePks->getClientOriginalExtension();
        // $filePathPks = $filePks->storeAs('pks', $fileNamePks, 'public');

        // Upload file IA
        $fileIa = $request->file('file_ia');
        $fileNameIa = 'ia_' . $mahasiswa->nim . '_' . time() . '.' . $fileIa->getClientOriginalExtension();
        $filePathIa = $fileIa->storeAs('ia', $fileNameIa, 'public');

        // Upload file laporan akhir
        $file = $request->file('file_laporan');
        $fileName = 'laporan_akhir_' . $mahasiswa->nim . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('laporan_akhir', $fileName, 'public');


        // Simpan data laporan akhir
        $laporanAkhir = new LaporanAkhirMahasiswa();
        $laporanAkhir->tahun_akademik_id = $request->tahun_akademik_id;
        $laporanAkhir->nim = $mahasiswa->nim;
        $laporanAkhir->kelompok = $suratPengantar->kelompok;
        $laporanAkhir->kelas = $suratPengantar->kelas;
        $laporanAkhir->kelas_dosen_p2k_id = $kelasDosenP2K->id;
        $laporanAkhir->file_path = $filePath;
        $laporanAkhir->file_pks = $filePathPks; // Simpan path file PKS
        $laporanAkhir->file_ia = $filePathIa; // Simpan path file IA
        $laporanAkhir->is_validated = false;
        $laporanAkhir->save();


        // //update file_arsip_pks di tabel p_k_s
        // $pks = PKS::where('umkm_id', $suratPengantar->umkm_id)
        //     ->where('tahun_akademik_id', $request->tahun_akademik_id)
        //     ->first();

        // //jika file_arsip_pks kosong atau null lakukan update
        // if (empty($pks->file_arsip_pks) || $pks->file_arsip_pks == null || $pks->file_arsip_pks == '') {
        //     $pks->file_arsip_pks = $filePathPks;
        //     $pks->save();
        // }

        return response()->json([
            'success' => true,
            'message' => 'Laporan akhir dan file PKS berhasil diupload.',
            'data' => $laporanAkhir
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $laporanAkhir = LaporanAkhirMahasiswa::where('id', $id)
            ->where('nim', $mahasiswa->nim)
            ->with(['tahunAkademik', 'kelasDosenP2K', 'validator'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $laporanAkhir
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $laporanAkhir = LaporanAkhirMahasiswa::where('id', $id)
            ->where('nim', $mahasiswa->nim)
            ->firstOrFail();

        // Jika laporan sudah divalidasi, tidak boleh diupdate
        if ($laporanAkhir->is_validated) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan akhir sudah divalidasi, tidak dapat diupdate.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file_laporan' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
            'file_pks' => 'nullable', // Max 10MB untuk file PKS
            'file_ia' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB untuk file IA
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update file laporan jika ada
        if ($request->hasFile('file_laporan')) {
            // Hapus file laporan lama
            if ($laporanAkhir->file_path) {
                Storage::disk('public')->delete($laporanAkhir->file_path);
            }

            // Upload file laporan baru
            $file = $request->file('file_laporan');
            $fileName = 'laporan_akhir_' . $mahasiswa->nim . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan_akhir', $fileName, 'public');

            // Update path file laporan
            $laporanAkhir->file_path = $filePath;
        }

        // Update file PKS jika ada
        // if ($request->hasFile('file_pks')) {
        //     // Hapus file PKS lama
        //     if ($laporanAkhir->file_pks) {
        //         Storage::disk('public')->delete($laporanAkhir->file_pks);
        //     }

        //     // Upload file PKS baru
        //     $filePks = $request->file('file_pks');
        //     $fileNamePks = 'pks_' . $mahasiswa->nim . '_' . time() . '.' . $filePks->getClientOriginalExtension();
        //     $filePathPks = $filePks->storeAs('pks', $fileNamePks, 'public');

        //     // Update path file PKS
        //     $laporanAkhir->file_pks = $filePathPks;
        // }

        // Update file IA jika ada
        if ($request->hasFile('file_ia')) {
            // Hapus file IA lama
            if ($laporanAkhir->file_ia) {
                Storage::disk('public')->delete($laporanAkhir->file_ia);
            }

            // Upload file IA baru
            $fileIa = $request->file('file_ia');
            $fileNameIa = 'ia_' . $mahasiswa->nim . '_' . time() . '.' . $fileIa->getClientOriginalExtension();
            $filePathIa = $fileIa->storeAs('ia', $fileNameIa, 'public');

            // Update path file IA
            $laporanAkhir->file_ia = $filePathIa;
        }

        // Simpan perubahan
        $laporanAkhir->save();

        return response()->json([
            'success' => true,
            'message' => 'Laporan akhir berhasil diupdate.',
            'data' => $laporanAkhir
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $laporanAkhir = LaporanAkhirMahasiswa::where('id', $id)
            ->where('nim', $mahasiswa->nim)
            ->firstOrFail();

        // dd($laporanAkhir);

        // Jika laporan sudah divalidasi, tidak boleh dihapus
        if ($laporanAkhir->is_validated) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan akhir sudah divalidasi, tidak dapat dihapus.'
            ], 403);
        }

        $cekSuratPengantar = SuratPengantar::where('kelas', $laporanAkhir->kelas)
            ->where('tahun_akademik_id', $laporanAkhir->tahun_akademik_id)
            ->first();

        $cekPks = PKS::where('umkm_id', $cekSuratPengantar->umkm_id)
            ->where('tahun_akademik_id', $laporanAkhir->tahun_akademik_id)
            ->first();

        if ($cekPks->created_by == $this->nim) {
            $cekPks->file_arsip_pks = null;
            $cekPks->save();
        }

        // Hapus file IA jika ada
        if ($laporanAkhir->file_ia) {
            Storage::disk('public')->delete($laporanAkhir->file_ia);
        }

        // Hapus file PKS jika ada
        if ($laporanAkhir->file_pks) {
            Storage::disk('public')->delete($laporanAkhir->file_pks);
        }

        // Hapus file laporan akhir jika ada
        if ($laporanAkhir->file_path) {
            Storage::disk('public')->delete($laporanAkhir->file_path);
        }

        // Hapus data laporan akhir
        $laporanAkhir->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan akhir berhasil dihapus.'
        ]);
    }

    /**
     * Get surat pengantar by tahun akademik
     */
    public function getSuratPengantar(Request $request)
    {
        $nim = $this->nim;

        if (!$nim) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $tahunAkademikId = $request->input('tahun_akademik_id');

        if (!$tahunAkademikId) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun akademik tidak valid.'
            ], 400);
        }

        // Ambil surat pengantar berdasarkan tahun akademik dan mahasiswa
        $suratPengantars = SuratPengantar::where('tahun_akademik_id', $tahunAkademikId)
            ->where('input_by', $nim)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $suratPengantars
        ]);
    }
}
