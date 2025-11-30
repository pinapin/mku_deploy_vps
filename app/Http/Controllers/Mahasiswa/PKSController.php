<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Formatter;
use App\Models\PKS;
use App\Models\Umkm;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PKSController extends Controller
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
        $umkms = Umkm::join('surat_pengantars', 'umkms.id', '=', 'surat_pengantars.umkm_id')
            ->where('surat_pengantars.input_by', $this->nim)
            ->select('umkms.*')
            ->get();
        // $umkms = Umkm::where('input_by', $this->nim)
        //     ->orderBy('nama_umkm', 'asc')->get();

        $cekPKSExist = PKS::where('created_by', $this->nim)
            ->whereNull('file_arsip_pks')
            ->exists();

        $tahun_ini = Carbon::now()->year;
        $bulan_romawi = Formatter::convertToRoman(date('m'));
        return view('pages.mahasiswa.pks.index', compact('umkms', 'bulan_romawi', 'tahun_ini', 'cekPKSExist'));
    }

    public function getData(Request $request)
    {
        // Ambil data PKS yang dibuat oleh mahasiswa yang login
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        $pks = PKS::with(['umkm'])
            ->where('created_by', $mahasiswa->nim)
            ->orderBy('created_at', 'desc')
            ->get();

        // Tambahkan encrypted_id untuk setiap PKS
        $pks->each(function ($item) {
            $item->encrypted_id = encrypt($item->id);
        });

        // Dapatkan tahun akademik yang aktif
        $tahunAkademik = TahunAkademik::getActive();

        // Hitung jumlah PKS yang sudah dibuat oleh mahasiswa pada tahun akademik aktif
        $countPKS = 0;
        if ($tahunAkademik) {
            $countPKS = PKS::where('created_by', $mahasiswa->nim)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->count();
        }

        return response()->json([
            'data' => $pks,
            'has_active_pks' => $countPKS > 0
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,id',
            'tgl_pks' => 'required|date',
            'no_pks_umkm' => 'nullable|string|max:255',
            'lama_perjanjian' => 'required|integer|min:1',
            'pic_pks' => 'required|string|max:255',
            'email_pks' => 'required|email|max:255',
            'alamat_pks' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Ambil data mahasiswa yang login
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Dapatkan tahun akademik yang aktif
            $today = Carbon::now()->format('Y-m-d');
            $tahunAkademik = TahunAkademik::getActive();

            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tahun akademik yang aktif. Silahkan hubungi admin untuk mengaktifkan tahun akademik.'
                ], 422);
            }

            // Cek apakah mahasiswa sudah pernah membuat PKS pada tahun akademik yang aktif
            $existingPKS = PKS::where('created_by', $mahasiswa->nim)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->first();

            if ($existingPKS) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah membuat PKS. Anda hanya diperbolehkan membuat satu PKS per tahun akademik.'
                ], 422);
            }

            $existingNoPKSUMKM = PKS::whereRaw('DATE_ADD(tgl_pks, INTERVAL lama_perjanjian MONTH) >= ?', [$today])
                ->where('umkm_id', $request->umkm_id)
                ->first();

            if ($existingNoPKSUMKM) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'UMKM ini sudah memiliki PKS yang masih berlaku. Silahkan unduh PKS pada menu Data UMKM > Arsip UMKM'
                ], 422);
            }

            // Hitung jumlah PKS untuk mendapatkan nomor urut
            $countPKS = PKS::whereYear('tgl_pks', Carbon::parse($request->tgl_pks)->year)->count() + 1;
            $no_urut = str_pad($countPKS, 3, '0', STR_PAD_LEFT);
            $no_urut2 = str_pad($countPKS, 2, '0', STR_PAD_LEFT);

            // Dapatkan bulan romawi dan tahun
            $bulan_romawi = Formatter::convertToRoman(Carbon::parse($request->tgl_pks)->format('m'));
            $tahun = Carbon::parse($request->tgl_pks)->format('Y');

            // Format nomor PKS
            $no_pks = "{$no_urut}/UPT MKU-Ketramp.UMK/PKS/C.06.{$no_urut2}/{$bulan_romawi}/{$tahun}";

            // Buat PKS
            $pks = new PKS();
            $pks->tahun_akademik_id = $tahunAkademik->id;
            $pks->tgl_pks = $request->tgl_pks;
            $pks->no_pks = $no_pks;
            $pks->no_pks_umkm = $request->no_pks_umkm;
            $pks->umkm_id = $request->umkm_id;
            $pks->lama_perjanjian = $request->lama_perjanjian;
            $pks->pic_pks = $request->pic_pks;
            $pks->email_pks = $request->email_pks;
            $pks->alamat_pks = $request->alamat_pks;
            $pks->created_by = $mahasiswa->nim;
            $pks->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'PKS berhasil dibuat',
                'data' => $pks->load(['umkm'])
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
        $pks = PKS::with(['umkm'])
            ->find($id);

        if (!$pks) {
            return response()->json([
                'status' => 'error',
                'message' => 'PKS tidak ditemukan'
            ], 404);
        }

        // Cek apakah PKS milik mahasiswa yang login
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if ($mahasiswa->nim !== $pks->created_by && $this->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat PKS ini'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pks
        ]);
    }

    public function update(Request $request, $id)
    {
        $pks = PKS::find($id);

        if (!$pks) {
            return response()->json([
                'status' => 'error',
                'message' => 'PKS tidak ditemukan'
            ], 404);
        }

        // Cek apakah PKS milik mahasiswa yang login
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if ($mahasiswa->nim !== $pks->created_by) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengubah PKS ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,id',
            'tgl_pks' => 'required|date',
            'no_pks_umkm' => 'nullable|string|max:255',
            'lama_perjanjian' => 'required|integer|min:1',
            'pic_pks' => 'required|string|max:255',
            'email_pks' => 'required|email|max:255',
            'alamat_pks' => 'required|string',
            'file_arsip_pks' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
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

            // Handle file upload if exists
            if ($request->hasFile('file_arsip_pks')) {
                $file = $request->file('file_arsip_pks');

                // Create directory if it doesn't exist
                $directory = storage_path('app/public/pks');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate filename: PKS_{nim}_{no_pks}.pdf
                $extension = $file->getClientOriginalExtension();
                $fileName = 'PKS_' . $mahasiswa->nim . '_' . str_replace('/', '_', $pks->no_pks) . '.' . $extension;

                // Move file to storage
                $filePksPath = $file->storeAs('pks', $fileName);

                // Update PKS with file path
                $pks->file_arsip_pks = $filePksPath;
            }

            // Update PKS - only update if value is provided
            if ($request->has('umkm_id')) {
                $pks->umkm_id = $request->umkm_id;
            }
            if ($request->has('tgl_pks')) {
                $pks->tgl_pks = $request->tgl_pks;
            }
            if ($request->has('no_pks_umkm')) {
                $pks->no_pks_umkm = $request->no_pks_umkm;
            }
            if ($request->has('lama_perjanjian')) {
                $pks->lama_perjanjian = $request->lama_perjanjian;
            }
            if ($request->has('pic_pks')) {
                $pks->pic_pks = $request->pic_pks;
            }
            if ($request->has('email_pks')) {
                $pks->email_pks = $request->email_pks;
            }
            if ($request->has('alamat_pks')) {
                $pks->alamat_pks = $request->alamat_pks;
            }

            $pks->tahun_akademik_id = $tahunAkademik->id;
            $pks->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'PKS berhasil diperbarui',
                'data' => $pks->load(['umkm'])
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
        $pks = PKS::find($id);

        if (!$pks) {
            return response()->json([
                'status' => 'error',
                'message' => 'PKS tidak ditemukan'
            ], 404);
        }

        // Cek apakah PKS milik mahasiswa yang login
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();

        if ($mahasiswa->nim !== $pks->created_by) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus PKS ini'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Hapus PKS
            $pks->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'PKS berhasil dihapus'
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
            $pks = PKS::with(['umkm', 'mahasiswa'])
                ->find($id);

            if (!$pks) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PKS tidak ditemukan'
                ], 404);
            }

            // Cek apakah PKS milik mahasiswa yang login atau user adalah admin/dosen
            $role = $this->role;

            // Jika user adalah admin atau dosen, izinkan akses
            if ($role === 'admin' || $role === 'dosen') {
                // Lanjutkan proses
            } else {
                // Jika mahasiswa, cek apakah PKS miliknya
                $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();
                if (!$mahasiswa || $mahasiswa->nim !== $pks->created_by) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk mencetak PKS ini'
                    ], 403);
                }
            }

            // Format tanggal surat
            $tanggalPks = Carbon::parse($pks->tgl_pks)->locale('id');
            $tanggalPksFormatted = $tanggalPks->isoFormat('DD-MM-Y');
            $bulanRomawi = Formatter::convertToRoman($tanggalPks->format('m'));
            $tahun = $tanggalPks->format('Y');

            $hari_indonesia = $tanggalPks->isoFormat('dddd'); // Senin, Selasa, Rabu, dst.
            $tgl_latin = Formatter::convertNumberToWords((int)$tanggalPks->format('d')); // satu, dua, tiga, dst.
            $bulan_latin = $tanggalPks->isoFormat('MMMM'); // April, Mei, dst.
            $tahun_latin = Formatter::convertNumberToWords($tahun); // dua ribu dua puluh empat

            // Siapkan data untuk template
            $data = [
                'pks' => $pks,
                'tanggal_pks' => $tanggalPksFormatted,
                'bulan_romawi' => $bulanRomawi,
                'tahun' => $tahun,
                'hari_indonesia' => $hari_indonesia,
                'tgl_latin' => $tgl_latin,
                'bulan_latin' => $bulan_latin,
                'tahun_latin' => $tahun_latin,
            ];

            return view('pages.mahasiswa.pks.cetak', $data);

            // // Generate PDF
            // $pdf = PDF::loadView('pages.mahasiswa.pks.cetak', $data);

            // // Atur ukuran kertas dan orientasi
            // $pdf->setPaper('a4', 'portrait');

            // // Nama file PDF yang akan didownload
            // $filename = 'Perjanjian_Kerja_Sama_' . date('YmdHis') . '.pdf';

            // // Download PDF
            // return $pdf->stream($filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencetak PKS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request, $id)
    {
        $pks = PKS::find($id);

        if (!$pks) {
            return response()->json([
                'status' => 'error',
                'message' => 'PKS tidak ditemukan'
            ], 404);
        }

        // Cek ownership
        if ($this->nim !== $pks->created_by) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengupload file pada PKS ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file_arsip_pks' => 'required|file|mimes:pdf|max:5120',
        ], [
            'file_arsip_pks.required' => 'File PKS wajib diupload',
            'file_arsip_pks.file' => 'File yang diupload tidak valid',
            'file_arsip_pks.mimes' => 'File harus berformat PDF',
            'file_arsip_pks.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $file = $request->file('file_arsip_pks');

            // Generate filename
            $fileName = sprintf(
                'PKS_%s_%s.pdf',
                $this->nim,
                str_replace(['/', '\\'], '_', $pks->no_pks)
            );

            // Delete old file if exists
            if ($pks->file_arsip_pks && Storage::disk('public')->exists($pks->file_arsip_pks)) {
                Storage::disk('public')->delete($pks->file_arsip_pks);
            }

            // Store new file
            $filePath = $file->storeAs('pks', $fileName, 'public');

            // Update PKS
            $pks->update(['file_arsip_pks' => $filePath]);

            return response()->json([
                'status' => 'success',
                'message' => 'File PKS berhasil diupload',
                'data' => $pks->load('umkm')
            ]);
        } catch (\Exception $e) {
            Log::error('Upload PKS file error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupload file'
            ], 500);
        }
    }
}
