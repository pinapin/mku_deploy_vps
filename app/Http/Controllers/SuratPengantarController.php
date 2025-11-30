<?php

namespace App\Http\Controllers;

use App\Models\KategoriUmkm;
use App\Models\LaporanAkhirMahasiswa;
use App\Models\SuratPengantar;
use App\Models\SuratPengantarMahasiswa;
use App\Models\Umkm;
use App\Models\ProgramStudi;
use App\Models\Mahasiswa;
use App\Models\SettingSuratPengantar;
use App\Models\TahunAkademik;
use App\Models\PKS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class SuratPengantarController extends Controller
{
    protected $nim;
    protected $role;

    public function __construct()
    {
        $this->nim = Session::get('kode');
        $this->role = Session::get('role');
    }
    public function index()
    {
        // Ambil UMKM yang dimiliki oleh user yang login
        // $userUmkms = Umkm::orderBy('nama_umkm', 'asc')
        //     ->where('input_by', $this->nim)
        //     ->get();

        // Ambil tanggal sekarang
        $today = Carbon::now()->format('Y-m-d');

        // Ambil UMKM dari PKS yang masih berlaku
        // $pksUmkms = PKS::with('umkm')
        //     ->whereRaw('DATE_ADD(tgl_pks, INTERVAL lama_perjanjian YEAR) >= ?', [$today])
        //     ->whereHas('umkm')
        //     ->selectRaw('p_k_s.*, (SELECT COUNT(*) FROM surat_pengantars WHERE input_by != ? AND umkm_id = p_k_s.umkm_id) as jumlah_umkm_digunakan', [$this->nim])
        //     ->get();

        // Ambil semua UMKM
        // $pksUmkms = PKS::with('umkm')
        //     ->whereHas('umkm')
        //     ->selectRaw('p_k_s.*, (SELECT COUNT(*) FROM surat_pengantars WHERE input_by != ? AND umkm_id = p_k_s.umkm_id) as jumlah_umkm_digunakan', [$this->nim])
        //     ->get();

        // $filteredPksUmkms = $pksUmkms->filter(function ($item) {
        //     return $item->umkm !== null && $item->jumlah_umkm_digunakan <= 1;
        // })->pluck('umkm')->unique('id');

        // Gabungkan kedua koleksi UMKM
        // $umkms = $userUmkms->merge($filteredPksUmkms)->unique('id')->sortBy('nama_umkm');

        $umkms = Umkm::orderBy('nama_umkm', 'asc')
            ->get();

        $programStudis = ProgramStudi::orderBy('nama_prodi', 'asc')->get();

        $cekLaporanAkhirExist = LaporanAkhirMahasiswa::where('nim', $this->nim)->exists();

        //kategoriUmkms sort asc
        $kategoriUmkms = KategoriUmkm::orderBy('nama_kategori', 'asc')->get();
        return view('pages.mahasiswa.surat-pengantar.index', compact('umkms', 'programStudis', 'kategoriUmkms', 'cekLaporanAkhirExist'));
    }

    public function getData()
    {
        // Ambil data surat pengantar yang dibuat oleh mahasiswa yang login
        $nim = $this->nim;

        if (!$nim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        // Dapatkan tahun akademik yang aktif
        $tahunAkademik = TahunAkademik::getActive();

        $suratPengantars = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas'])
            ->where('input_by', $nim)
            ->get();

        $suratPengantars->transform(function ($item) {
            $item->encrypted_id = encrypt($item->id);
            return $item;
        });

        // Hitung jumlah surat pengantar yang sudah dibuat oleh mahasiswa pada tahun akademik aktif
        $countSuratPengantar = 0;
        if ($tahunAkademik) {
            $countSuratPengantar = SuratPengantar::where('input_by', $nim)
                ->count();
        }

        return response()->json([
            'data' => $suratPengantars,
            'has_active_letter' => $countSuratPengantar > 0
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,id',
            'kelas' => 'required|string|max:2',
            'tgl_surat' => 'required|date',
            'mahasiswas' => 'required|array|min:1',
            'mahasiswas.*.nim' => 'required|string',
            'mahasiswas.*.nama_mahasiswa' => 'required|string',
            'mahasiswas.*.prodi_id' => 'required|exists:program_studis,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Ambil data mahasiswa yang login
        $nim = $this->nim;

        if (!$nim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Dapatkan tahun akademik yang aktif
            $tahunAkademik = TahunAkademik::getActive();

            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tahun akademik yang aktif. Silahkan hubungi admin untuk mengaktifkan tahun akademik.'
                ], 422);
            }

            // Cek apakah mahasiswa sudah pernah membuat surat pengantar pada tahun akademik yang aktif
            $existingSurat = SuratPengantar::where('input_by', $nim)
                ->first();

            if ($existingSurat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah membuat surat pengantar. Anda hanya diperbolehkan membuat satu surat pengantar.'
                ], 422);
            }

            //cek apakah umkm sudah dipakai 2 kali pada tahun akademik yang aktif
            $umkmDigunakan = SuratPengantar::where('umkm_id', $request->umkm_id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->count();

            if ($umkmDigunakan >= 2) {
                return response()->json([
                    'status' => 'error',
                    'tipe' => 'umkm',
                    'message' => 'UMKM ini sudah dipakai 2 kali pada tahun akademik ini. Pilih UMKM yang lain.'
                ], 422);
            }

            //cek apakah umkm sudah dipakai di kelas yang sama pada tahun akademik yang aktif
            $suratTerambil = SuratPengantar::where('umkm_id', $request->umkm_id)
                ->where('kelas', $request->kelas)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->first();

            if ($suratTerambil) {
                return response()->json([
                    'status' => 'error',
                    'tipe' => 'umkm',
                    'message' => 'UMKM ini sudah dipakai di kelas ' . $request->kelas . ' pada tahun akademik ini. Pilih UMKM yang lain.'
                ], 422);
            }

            $kelas = $request->kelas;
            if (strlen($kelas) < 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'kelas harus di inputkan minimal 2 digit. Contoh: 01 atau 02 atau 11 atau 12'
                ], 422);
            }

            // Hitung kelompok berdasarkan kelas dan tahun akademik
            $lastKelompok = SuratPengantar::where('kelas', $request->kelas)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->max('kelompok');

            $kelompok = $lastKelompok ? $lastKelompok + 1 : 1;

            // Buat surat pengantar
            $suratPengantar = new SuratPengantar();
            $suratPengantar->tahun_akademik_id = $tahunAkademik->id;
            $suratPengantar->input_by = $nim;
            $suratPengantar->kelas = $request->kelas;
            $suratPengantar->kelompok = $kelompok;
            $suratPengantar->umkm_id = $request->umkm_id;
            $suratPengantar->tgl_surat = $request->tgl_surat;
            $suratPengantar->save();

            // Simpan data mahasiswa yang terlibat
            foreach ($request->mahasiswas as $mhs) {
                $suratPengantarMahasiswa = new SuratPengantarMahasiswa();
                $suratPengantarMahasiswa->surat_pengantar_id = $suratPengantar->id;
                $suratPengantarMahasiswa->nim = $mhs['nim'];
                $suratPengantarMahasiswa->nama_mahasiswa = $mhs['nama_mahasiswa'];
                $suratPengantarMahasiswa->prodi_id = $mhs['prodi_id'];
                $suratPengantarMahasiswa->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Surat pengantar berhasil dibuat',
                'data' => $suratPengantar->load(['umkm', 'suratPengantarMahasiswas'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $suratPengantar = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas.programStudi'])
            ->find($id);

        if (!$suratPengantar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Surat pengantar tidak ditemukan'
            ], 404);
        }

        // Cek apakah surat pengantar milik mahasiswa yang login
        $nim = $this->nim;
        $role = $this->role;

        if ($nim !== $suratPengantar->input_by && $role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat surat pengantar ini'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $suratPengantar
        ]);
    }

    public function update(Request $request, $id)
    {
        $suratPengantar = SuratPengantar::find($id);

        if (!$suratPengantar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Surat pengantar tidak ditemukan'
            ], 404);
        }

        // Cek apakah surat pengantar milik mahasiswa yang login
        $nim = $this->nim;

        if ($nim !== $suratPengantar->input_by) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengubah surat pengantar ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,id',
            'tgl_surat' => 'required|date',
            'mahasiswas' => 'required|array|min:1',
            'mahasiswas.*.nim' => 'required|string',
            'mahasiswas.*.nama_mahasiswa' => 'required|string',
            'mahasiswas.*.prodi_id' => 'required|exists:program_studis,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Dapatkan tahun akademik yang aktif
            $tahunAkademik = TahunAkademik::getActive();

            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tahun akademik yang aktif. Silahkan hubungi admin untuk mengaktifkan tahun akademik.'
                ], 422);
            }

             //cek apakah umkm sudah dipakai 2 kali pada tahun akademik yang aktif
             $umkmDigunakan = SuratPengantar::where('umkm_id', $request->umkm_id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->where('id', '!=', $id)
                ->count();

             if ($umkmDigunakan >= 2) {
                return response()->json([
                    'status' => 'error',
                    'tipe' => 'umkm',
                    'message' => 'UMKM ini sudah dipakai 2 kali pada tahun akademik ini. Pilih UMKM yang lain.'
                ], 422);
             }

            $suratTerambil = SuratPengantar::where('umkm_id', $request->umkm_id)
                ->where('kelas', $request->kelas)
                ->where('id', '!=', $id)
                ->first();

            if ($suratTerambil) {
                return response()->json([
                    'status' => 'error',
                    'tipe' => 'umkm',
                    'message' => 'UMKM ini sudah dipakai di kelas ' . $request->kelas . ' pada tahun akademik ini. Pilih UMKM yang lain.'
                ], 422);
            }

            $kelas = $request->kelas;
            if (strlen($kelas) < 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'kelas harus di inputkan minimal 2 digit. Contoh: 01 atau 02 atau 11 atau 12'
                ], 422);
            }

            // Jika kelas berubah, perlu menghitung ulang kelompok
            if ($suratPengantar->kelas != $request->kelas) {
                // Hitung kelompok berdasarkan kelas dan tahun akademik
                $lastKelompok = SuratPengantar::where('kelas', $request->kelas)
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->max('kelompok');

                $kelompok = $lastKelompok ? $lastKelompok + 1 : 1;
                $suratPengantar->kelompok = $kelompok;
            }

            // Update surat pengantar
            $suratPengantar->tahun_akademik_id = $tahunAkademik->id;
            $suratPengantar->umkm_id = $request->umkm_id;
            $suratPengantar->kelas = $request->kelas;
            $suratPengantar->tgl_surat = $request->tgl_surat;
            $suratPengantar->save();

            // Hapus data mahasiswa yang lama
            SuratPengantarMahasiswa::where('surat_pengantar_id', $suratPengantar->id)->delete();

            // Simpan data mahasiswa yang baru
            foreach ($request->mahasiswas as $mhs) {
                $suratPengantarMahasiswa = new SuratPengantarMahasiswa();
                $suratPengantarMahasiswa->surat_pengantar_id = $suratPengantar->id;
                $suratPengantarMahasiswa->nim = $mhs['nim'];
                $suratPengantarMahasiswa->nama_mahasiswa = $mhs['nama_mahasiswa'];
                $suratPengantarMahasiswa->prodi_id = $mhs['prodi_id'];
                $suratPengantarMahasiswa->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Surat pengantar berhasil diperbarui',
                'data' => $suratPengantar->load(['umkm', 'suratPengantarMahasiswas'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $suratPengantar = SuratPengantar::find($id);

        if (!$suratPengantar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Surat pengantar tidak ditemukan'
            ], 404);
        }

        // Cek apakah surat pengantar milik mahasiswa yang login
        $nim = $this->nim;

        if ($nim !== $suratPengantar->input_by) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus surat pengantar ini'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Hapus data mahasiswa terlebih dahulu
            SuratPengantarMahasiswa::where('surat_pengantar_id', $suratPengantar->id)->delete();

            // Hapus surat pengantar
            $suratPengantar->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Surat pengantar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePdf($id)
    {

        $id = decrypt($id);
        $suratPengantar = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas.programStudi'])
            ->find($id);

        if (!$suratPengantar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Surat pengantar tidak ditemukan'
            ], 404);
        }

        // Cek apakah surat pengantar milik mahasiswa yang login atau user adalah admin/dosen
        $nim = $this->nim;
        $role = $this->role;

        if ($nim !== $suratPengantar->input_by && $role !== 'admin' && $role !== 'dosen') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mencetak surat pengantar ini'
            ], 403);
        }

        // Dapatkan tahun akademik dari surat pengantar
        $tahun_akademik_id = $suratPengantar->tahun_akademik_id;

        // Cari setting surat pengantar berdasarkan tahun akademik yang aktif
        $setting_surat = SettingSuratPengantar::where('tahun_akademik_id', $tahun_akademik_id)->first();

        // Jika tidak ditemukan, gunakan setting surat pengantar terbaru
        // if (!$setting_surat) {
        //     $setting_surat = SettingSuratPengantar::latest()->first();
        // }
        $qr_image = $setting_surat->qr_surat_image ?? '';
        // Format tanggal surat
        $tanggalSurat = Carbon::parse($suratPengantar->tgl_surat)->locale('id')->isoFormat('D MMMM Y');

        // Generate nomor surat (contoh format: 001/SP/MKU/VII/2023)
        // $bulan = Carbon::parse($suratPengantar->tgl_surat)->format('m');
        // $tahun = Carbon::parse($suratPengantar->tgl_surat)->format('Y');
        // $romawi = $this->convertToRoman($bulan);
        // $nomorUrut = str_pad($suratPengantar->id, 3, '0', STR_PAD_LEFT);
        // $nomorSurat = "{$nomorUrut}/SP/MKU/{$romawi}/{$tahun}";

        $kopImage = base64_encode(file_get_contents(public_path('assets/image/kop-mku.png')));
        $qrImage = !empty($qr_image) ? base64_encode(file_get_contents(public_path('storage/' . $qr_image))) : '';

        // dd($suratPengantar);
        // Siapkan data untuk template
        $data = [
            'qrImage' => $qrImage,
            'kopImage' => $kopImage,
            'no_surat' => $setting_surat->no_surat ?? '',
            'tanggal_surat' => $tanggalSurat,
            'nama_umkm' => $suratPengantar->umkm->nama_umkm,
            'alamat_umkm' => $suratPengantar->umkm->alamat,
            'mahasiswas' => $suratPengantar->suratPengantarMahasiswas,
            'kelas' => $suratPengantar->kelas,
            'kelompok' => $suratPengantar->kelompok
        ];
        // return view('pages.mahasiswa.surat-pengantar.cetak', $data);
        // die;
        // Generate PDF
        $pdf = PDF::loadView('pages.mahasiswa.surat-pengantar.cetak', $data);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'portrait');

        // Nama file PDF yang akan didownload
        $filename = 'Surat_Pengantar_' . date('YmdHis') . '.pdf';

        // Download PDF
        return $pdf->stream($filename);
    }

    private function convertToRoman($number)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romans[$number] ?? $number;
    }
}
