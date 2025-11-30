<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriUmkm;
use App\Models\SuratPengantar;
use App\Models\SuratPengantarMahasiswa;
use App\Models\Umkm;
use App\Models\ProgramStudi;
use App\Models\Mahasiswa;
use App\Models\SettingSuratPengantar;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SuratPengantarController extends Controller
{
    public function index()
    {
        $umkms = Umkm::orderBy('nama_umkm', 'asc')->get();
        $programStudis = ProgramStudi::orderBy('nama_prodi', 'asc')->get();

        //kategoriUmkms sort asc
        $kategoriUmkms = KategoriUmkm::orderBy('nama_kategori', 'asc')->get();
        return view('pages.admin.surat-pengantar.index', compact('umkms', 'programStudis', 'kategoriUmkms'));
    }

    public function getData()
    {
        // Admin dapat melihat semua surat pengantar
        $suratPengantars = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas'])
            ->get();

        $suratPengantars->transform(function ($item) {
            $item->encrypted_id = encrypt($item->id);
            return $item;
        });

        return response()->json(['data' => $suratPengantars]);
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

        // Admin membuat surat pengantar
        $user = Auth::user();

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

            $suratTerambil = SuratPengantar::where('umkm_id', $request->umkm_id)
                ->where('kelas', $request->kelas)
                ->first();

            if ($suratTerambil) {
                return response()->json([
                    'status' => 'error',
                    'tipe' => 'umkm',
                    'message' => 'UMKM ini sudah dipakai di kelas yang sama. Pilih UMKM yang lain.'
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
            $suratPengantar->input_by = $request->input_by;
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

        DB::beginTransaction();

        try {
            // Dapatkan tahun akademik yang aktif
            $tahunAkademik = TahunAkademik::getActive();

            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tahun akademik yang aktif. Silahkan aktifkan tahun akademik terlebih dahulu.'
                ], 422);
            }

            $suratTerambil = SuratPengantar::where('umkm_id', $request->umkm_id)
                ->where('kelas', $request->kelas)
                ->where('id', '!=', $id) // Mengecualikan record yang sedang diedit
                ->first();

            if ($suratTerambil) {
                return response()->json([
                    'status' => 'error',
                    'tipe' => 'umkm',
                    'message' => 'UMKM ini sudah dipakai di kelas yang sama. Pilih UMKM yang lain.'
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
        try {
            $id = decrypt($id);
            $suratPengantar = SuratPengantar::with(['umkm', 'suratPengantarMahasiswas.programStudi'])
                ->find($id);

            if (!$suratPengantar) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Surat pengantar tidak ditemukan'
                ], 404);
            }

            // Dapatkan tahun akademik yang aktif
            $tahunAkademik = TahunAkademik::getActive();
            
            // Cari setting surat pengantar berdasarkan tahun akademik yang aktif
            $setting_surat = SettingSuratPengantar::where('tahun_akademik_id', $tahunAkademik->id)->first();
            
            // Jika tidak ditemukan, gunakan setting surat pengantar terbaru
            if (!$setting_surat) {
                $setting_surat = SettingSuratPengantar::latest()->first();
            }
            $qr_image = $setting_surat->qr_surat_image;
            // Format tanggal surat
            $tanggalSurat = Carbon::parse($suratPengantar->tgl_surat)->locale('id')->isoFormat('D MMMM Y');

            $kopImage = base64_encode(file_get_contents(public_path('assets/image/kop-mku.png')));
            $qrImage = base64_encode(file_get_contents(public_path('storage/' . $qr_image)));

            // Siapkan data untuk template
            $data = [
                'qrImage' => $qrImage,
                'kopImage' => $kopImage,
                'no_surat' => $setting_surat->no_surat,
                'tanggal_surat' => $tanggalSurat,
                'nama_umkm' => $suratPengantar->umkm->nama_umkm,
                'alamat_umkm' => $suratPengantar->umkm->alamat,
                'mahasiswas' => $suratPengantar->suratPengantarMahasiswas,
                'kelas' => $suratPengantar->kelas,
                'kelompok' => $suratPengantar->kelompok
            ];

            // Generate PDF
            $pdf = PDF::loadView('pages.mahasiswa.surat-pengantar.cetak', $data);

            // Atur ukuran kertas dan orientasi
            $pdf->setPaper('a4', 'portrait');

            // Nama file PDF yang akan didownload
            $filename = 'Surat_Pengantar_' . date('YmdHis') . '.pdf';

            // Download PDF
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMahasiswaList($id)
    {
        try {
            $id = decrypt($id);
            $suratPengantar = SuratPengantar::with(['suratPengantarMahasiswas.programStudi'])->find($id);

            if (!$suratPengantar) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Surat Pengantar tidak ditemukan'
                ], 404);
            }

            $mahasiswas = $suratPengantar->suratPengantarMahasiswas->map(function ($item) {
                return [
                    'nim' => $item->nim,
                    'nama_mahasiswa' => $item->nama_mahasiswa,
                    'nama_prodi' => $item->programStudi->nama_prodi ?? '-',
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $mahasiswas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data mahasiswa: ' . $e->getMessage()
            ], 500);
        }
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
