<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\KategoriUmkm;
use App\Models\PKS;
use App\Models\Mahasiswa;
use App\Models\SuratPengantar;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DataUmkmController extends Controller
{
    /**
     * Menampilkan halaman data UMKM
     */
    protected $nim;

    public function __construct()
    {
        $this->nim = Session::get('kode');
    }

    public function index()
    {
        $kategoriUmkm = KategoriUmkm::all();
        return view('pages.mahasiswa.data-umkm.index', compact('kategoriUmkm'));
    }

    /**
     * Mengambil data UMKM untuk DataTable
     */
    public function getData()
    {
        $tahunAkademik = TahunAkademik::getActive();

        // Single query untuk mengambil UMKM dengan relasi
        $umkm = Umkm::with('kategoriUmkm')
            ->where('input_by', $this->nim)
            ->get();

        // Gunakan collection hasil query pertama (tidak perlu query lagi)
        $hasUmkm = $umkm->isNotEmpty();

        // Query optimal dengan whereIn untuk mendapatkan surat pengantar
        $hasSuratPengantar = false;
        if ($hasUmkm) {
            $umkmIds = $umkm->pluck('id');

            $hasSuratPengantar = SuratPengantar::where('input_by', $this->nim)
                ->whereIn('umkm_id', $umkmIds)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->exists();
        }

        return response()->json([
            'data' => $umkm,
            'has_umkm' => $hasUmkm,
            'has_surat_pengantar' => $hasSuratPengantar
        ]);
    }

    /**
     * Mengambil data PKS yang masih berlaku untuk DataTable Arsip UMKM
     */
    public function getArsip(Request $request)
    {
        // Ambil tanggal sekarang
        $today = Carbon::now()->format('Y-m-d');
        $nim = Session::get('kode');

        // Query untuk mengambil PKS yang masih berlaku di tahun akademik sekarang
        $tahun_akademik_sekarang = TahunAkademik::getActive();
        $query = PKS::with(['umkm', 'umkm.kategoriUmkm'])
            ->leftJoin('umkms', 'p_k_s.umkm_id', '=', 'umkms.id')
            ->leftJoin('kategori_umkms', 'umkms.kategori_umkm_id', '=', 'kategori_umkms.id')
            ->select('p_k_s.*')
            ->selectRaw('DATE_ADD(tgl_pks, INTERVAL lama_perjanjian YEAR) as tanggal_berakhir')
            ->selectRaw('(SELECT COUNT(*) FROM surat_pengantars WHERE umkm_id = p_k_s.umkm_id AND tahun_akademik_id = ?) as jumlah_umkm_digunakan', [$tahun_akademik_sekarang->id])
            ->selectRaw('(SELECT COUNT(*) FROM surat_pengantars WHERE umkm_id = p_k_s.umkm_id AND input_by = ?) as is_used_by_current_user', [$nim])
            ->selectRaw('umkms.nama_umkm as nama_umkm')
            ->selectRaw('kategori_umkms.nama_kategori as nama_kategori')
            ->whereRaw('DATE_ADD(tgl_pks, INTERVAL lama_perjanjian YEAR) >= ?', [$today])
            ->whereNotNull('p_k_s.umkm_id');

        return DataTables::of($query)
            ->addIndexColumn()
            // Custom global search
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->get('search')['value'])) {
                    $search = $request->get('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('p_k_s.no_pks', 'like', "%{$search}%")
                            ->orWhere('p_k_s.no_pks_umkm', 'like', "%{$search}%")
                            ->orWhere('umkms.nama_umkm', 'like', "%{$search}%")
                            ->orWhere('kategori_umkms.nama_kategori', 'like', "%{$search}%")
                            ->orWhere('p_k_s.tgl_pks', 'like', "%{$search}%");
                    });
                }
            })
            // Filter columns untuk mencegah error pada kolom computed
            ->filterColumn('tanggal_berakhir', function ($query, $keyword) {
                // Empty - prevent default search
            })
            ->filterColumn('jumlah_umkm_digunakan', function ($query, $keyword) {
                // Empty - prevent default search
            })
            ->filterColumn('nama_umkm', function ($query, $keyword) {
                $query->where('umkms.nama_umkm', 'like', "%{$keyword}%");
            })
            ->filterColumn('nama_kategori', function ($query, $keyword) {
                $query->where('kategori_umkms.nama_kategori', 'like', "%{$keyword}%");
            })
            // Order columns
            ->orderColumn('nama_umkm', 'umkms.nama_umkm $1')
            ->orderColumn('nama_kategori', 'kategori_umkms.nama_kategori $1')
            ->orderColumn('no_pks', 'p_k_s.no_pks $1')
            ->orderColumn('no_pks_umkm', 'p_k_s.no_pks_umkm $1')
            ->orderColumn('tgl_pks', 'p_k_s.tgl_pks $1')
            ->orderColumn('tanggal_berakhir', function ($query, $order) {
                $query->orderByRaw("DATE_ADD(p_k_s.tgl_pks, INTERVAL p_k_s.lama_perjanjian YEAR) {$order}");
            })
            ->orderColumn('jumlah_umkm_digunakan', function ($query, $order) {
                $query->orderByRaw("(SELECT COUNT(*) FROM surat_pengantars WHERE umkm_id = p_k_s.umkm_id) {$order}");
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_umkm_id' => 'required|exists:kategori_umkms,id',
            'nama_umkm' => 'required|string|max:255|unique:umkms,nama_umkm',
            'nama_pemilik_umkm' => 'required|string|max:255',
            'jabatan_umkm' => 'required|string|max:255',
            'no_hp_umkm' => 'required|string|max:20',
            'email_umkm' => 'required|string|max:255',
            'alamat_umkm' => 'required|string',
            'logo_umkm' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nama_umkm.unique' => 'Nama UMKM sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Cek apakah user sudah memiliki UMKM
        $existingUmkm = Umkm::where('input_by', $this->nim)->first();

        if ($existingUmkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memiliki UMKM. Anda hanya diperbolehkan membuat satu UMKM.'
            ], 422);
        }

        $umkm = new Umkm();
        $umkm->kategori_umkm_id = $request->kategori_umkm_id;
        $umkm->input_by = $this->nim; // Simpan ID user yang login
        $umkm->nama_umkm = $request->nama_umkm;
        $umkm->nama_pemilik_umkm = $request->nama_pemilik_umkm;
        $umkm->jabatan_umkm = $request->jabatan_umkm;
        $umkm->no_hp_umkm = $request->no_hp_umkm;
        $umkm->email_umkm = $request->email_umkm;
        $umkm->alamat_umkm = $request->alamat_umkm;

        // Handle logo upload
        if ($request->hasFile('logo_umkm')) {
            $file = $request->file('logo_umkm');
            $fileName =  str_replace(' ', '_', $request->nama_umkm) . '_' . date('YmdHis')  . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('logo-umkm', $fileName);
            $umkm->logo_umkm = $path;
        }

        $umkm->save();

        return response()->json([
            'status' => 'success',
            'message' => 'UMKM berhasil ditambahkan',
            'data' => $umkm
        ]);
    }

    /**
     * Mengambil data UMKM tanpa PKS yang masih berlaku untuk DataTable
     */
    public function getUmkmTanpaPks(Request $request)
    {
        // Ambil tanggal sekarang
        $today = Carbon::now()->format('Y-m-d');
        $nim = Session::get('kode');

        // Query untuk mengambil UMKM milik user yang tidak memiliki PKS atau PKS sudah kadaluarsa
        $query = Umkm::with('KategoriUmkm')
            ->leftJoin('p_k_s', 'umkms.id', '=', 'p_k_s.umkm_id')
            ->leftJoin('kategori_umkms', 'umkms.kategori_umkm_id', '=', 'kategori_umkms.id')
            ->select('umkms.*', 'kategori_umkms.nama_kategori')
            ->selectRaw('(SELECT COUNT(*) FROM p_k_s WHERE umkm_id = umkms.id AND DATE_ADD(tgl_pks, INTERVAL lama_perjanjian YEAR) >= ?) as has_valid_pks', [$today])
            ->selectRaw('(SELECT COUNT(*) FROM p_k_s WHERE umkm_id = umkms.id) as has_any_pks')
            ->selectRaw('CASE WHEN (SELECT COUNT(*) FROM p_k_s WHERE umkm_id = umkms.id) = 0 THEN "Tidak Ada PKS" ELSE "PKS Kedaluwarsa" END as status_pks')
            ->where(function ($q) use ($today) {
                $q->whereNull('p_k_s.id') // Tidak ada PKS sama sekali
                    ->orWhereRaw('DATE_ADD(p_k_s.tgl_pks, INTERVAL p_k_s.lama_perjanjian YEAR) < ?', [$today]); // PKS sudah kadaluarsa
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('has_valid_pks', function ($query, $keyword) {
                // Empty - prevent default search
            })
            ->filterColumn('has_any_pks', function ($query, $keyword) {
                // Empty - prevent default search
            })
            ->filterColumn('nama_kategori', function ($query, $keyword) {
                $query->whereHas('KategoriUmkm', function ($q) use ($keyword) {
                    $q->where('nama_kategori', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('nama_umkm', function ($query, $keyword) {
                $query->where('umkms.nama_umkm', 'like', "%{$keyword}%");
            })
            ->filterColumn('nama_pemilik_umkm', function ($query, $keyword) {
                $query->where('umkms.nama_pemilik_umkm', 'like', "%{$keyword}%");
            })
            ->filterColumn('no_hp_umkm', function ($query, $keyword) {
                $query->where('umkms.no_hp_umkm', 'like', "%{$keyword}%");
            })
            ->filterColumn('email_umkm', function ($query, $keyword) {
                $query->where('umkms.email_umkm', 'like', "%{$keyword}%");
            })
            ->filterColumn('status_pks', function ($query, $keyword) {
                if ($keyword === 'Tidak Ada PKS') {
                    $query->whereRaw('(SELECT COUNT(*) FROM p_k_s WHERE umkm_id = umkms.id) = 0');
                } else if ($keyword === 'PKS Kedaluwarsa') {
                    $query->whereRaw('(SELECT COUNT(*) FROM p_k_s WHERE umkm_id = umkms.id) > 0');
                }
            })
            ->make(true);
    }

    public function show($id)
    {
        $umkm = Umkm::find($id);

        if (!$umkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'UMKM tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $umkm
        ]);
    }

    public function update(Request $request, $id)
    {
        $umkm = Umkm::find($id);

        if (!$umkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'UMKM tidak ditemukan'
            ], 404);
        }

        // Cek apakah UMKM ini milik user yang login
        if ($umkm->input_by != $this->nim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengedit UMKM ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'kategori_umkm_id' => 'required|exists:kategori_umkms,id',
            'nama_umkm' => 'required|string|max:255|unique:umkms,nama_umkm,' . $umkm->id,
            'nama_pemilik_umkm' => 'required|string|max:255',
            'jabatan_umkm' => 'required|string|max:255',
            'no_hp_umkm' => 'required|string|max:20',
            'email_umkm' => 'required|string|max:255',
            'alamat_umkm' => 'required|string',
            'logo_umkm' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nama_umkm.unique' => 'Nama UMKM sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $umkm->kategori_umkm_id = $request->kategori_umkm_id;
        $umkm->nama_umkm = $request->nama_umkm;
        $umkm->nama_pemilik_umkm = $request->nama_pemilik_umkm;
        $umkm->jabatan_umkm = $request->jabatan_umkm;
        $umkm->no_hp_umkm = $request->no_hp_umkm;
        $umkm->email_umkm = $request->email_umkm;
        $umkm->alamat_umkm = $request->alamat_umkm;

        // Handle logo upload
        if ($request->hasFile('logo_umkm')) {
            // Delete old logo if exists
            if ($umkm->logo_umkm) {
                Storage::delete($umkm->logo_umkm);
            }

            $file = $request->file('logo_umkm');
            $fileName =  str_replace(' ', '_', $request->nama_umkm) . '_' . date('YmdHis')  . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('logo-umkm', $fileName);
            $umkm->logo_umkm = $path;
        }

        $umkm->save();

        return response()->json([
            'status' => 'success',
            'message' => 'UMKM berhasil diperbarui',
            'data' => $umkm
        ]);
    }

    public function destroy($id)
    {
        $umkm = Umkm::find($id);

        if (!$umkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'UMKM tidak ditemukan'
            ], 404);
        }

        // Cek apakah UMKM ini milik user yang login
        if ($umkm->input_by != $this->nim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus UMKM ini'
            ], 403);
        }

        // Delete logo if exists
        if ($umkm->logo_umkm) {
            Storage::delete($umkm->logo_umkm);
        }

        $umkm->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'UMKM berhasil dihapus'
        ]);
    }
}
