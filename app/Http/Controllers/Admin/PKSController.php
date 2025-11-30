<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Formatter;
use App\Models\PKS;
use App\Models\Umkm;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\KategoriUmkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class PKSController extends Controller
{
    public function index()
    {
        $umkms = Umkm::orderBy('nama_umkm', 'asc')->get();
        $kategori_umkms = KategoriUmkm::orderBy('nama_kategori', 'asc')->get();

        $tahun_ini = Carbon::now()->year;
        $bulan_romawi = Formatter::convertToRoman(date('m'));
        return view('pages.admin.pks.index', compact('umkms', 'kategori_umkms', 'bulan_romawi', 'tahun_ini'));
    }

    public function getData(Request $request)
    {
        $pks = PKS::with(['umkm'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Tambahkan encrypted_id untuk setiap PKS
        $pks->each(function ($item) {
            $item->encrypted_id = encrypt($item->id);
            // Jika created_by bernilai NULL, tampilkan 'Admin'
            $item->created_by_name = $item->created_by ? $item->created_by : 'Admin';
        });

        return response()->json(['data' => $pks]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,id',
            'tgl_pks' => 'required|date',
            'no_pks' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Cek apakah nomor PKS sudah ada di database
                    $exists = PKS::where('no_pks', $value)->exists();
                    if ($exists) {
                        $fail('Nomor PKS sudah digunakan.');
                    }
                },
            ],
            'no_pks_umkm' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    // Cek apakah nomor PKS UMKM sudah ada di database (jika diisi)
                    if ($value) {
                        $exists = PKS::where('no_pks_umkm', $value)->exists();
                        if ($exists) {
                            $fail('Nomor PKS UMKM sudah digunakan.');
                        }
                    }
                },
            ],
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

            // Gunakan nomor PKS yang diinput oleh admin

            // Buat PKS
            $pks = new PKS();
            $pks->tahun_akademik_id = $tahunAkademik->id;
            $pks->tgl_pks = $request->tgl_pks;
            $pks->no_pks = $request->no_pks; // Gunakan nomor PKS yang diinput oleh admin
            $pks->no_pks_umkm = $request->no_pks_umkm;
            $pks->umkm_id = $request->umkm_id;
            $pks->lama_perjanjian = $request->lama_perjanjian;
            $pks->pic_pks = $request->pic_pks;
            $pks->email_pks = $request->email_pks;
            $pks->alamat_pks = $request->alamat_pks;
            $pks->created_by = null; // Set created_by menjadi null karena input dari admin
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

        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,id',
            'tgl_pks' => 'required|date',
            'no_pks' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    // Cek apakah nomor PKS sudah ada di database, kecuali untuk PKS yang sedang diedit
                    $exists = PKS::where('no_pks', $value)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Nomor PKS sudah digunakan.');
                    }
                },
            ],
            'no_pks_umkm' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    // Cek apakah nomor PKS UMKM sudah ada di database (jika diisi), kecuali untuk PKS yang sedang diedit
                    if ($value) {
                        $exists = PKS::where('no_pks_umkm', $value)
                            ->where('id', '!=', $id)
                            ->exists();
                        if ($exists) {
                            $fail('Nomor PKS UMKM sudah digunakan.');
                        }
                    }
                },
            ],
            'lama_perjanjian' => 'required|integer|min:1',
            'pic_pks' => 'required|string|max:255',
            'email_pks' => 'required|email|max:255',
            'alamat_pks' => 'required|string',
            'file_arsip_pks' => 'nullable|file|mimes:pdf|max:10240', // Validasi file arsip PKS (PDF, max 10MB)
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

            // Upload file arsip PKS jika ada
            if ($request->hasFile('file_arsip_pks')) {
                // Hapus file lama jika ada
                if ($pks->file_arsip_pks) {
                    Storage::delete($pks->file_arsip_pks);
                }

                $file = $request->file('file_arsip_pks');
                //get nama umkm
                $fileName = 'PKS_' . $request->nama_umkm . '.' . $file->getClientOriginalExtension();
                $filePksPath = $file->storeAs('pks', $fileName);
                $pks->file_arsip_pks = $filePksPath;
            }

            // Update PKS
            $pks->tahun_akademik_id = $tahunAkademik->id;
            $pks->umkm_id = $request->umkm_id;
            $pks->tgl_pks = $request->tgl_pks;
            $pks->no_pks = $request->no_pks; // Gunakan nomor PKS yang diinput oleh admin
            $pks->no_pks_umkm = $request->no_pks_umkm;
            $pks->lama_perjanjian = $request->lama_perjanjian;
            $pks->pic_pks = $request->pic_pks;
            $pks->email_pks = $request->email_pks;
            $pks->alamat_pks = $request->alamat_pks;
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

        DB::beginTransaction();

        try {
            // Hapus file arsip PKS jika ada
            if ($pks->file_arsip_pks) {
                Storage::delete($pks->file_arsip_pks);
            }

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
            $pks = PKS::with(['umkm'])
                ->find($id);

            if (!$pks) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PKS tidak ditemukan'
                ], 404);
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencetak PKS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data UMKM baru dari admin
     */
    public function storeUmkm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_umkm_id' => 'required|exists:kategori_umkms,id',
            'nama_umkm' => 'required|string|max:255',
            'nama_pemilik_umkm' => 'required|string|max:255',
            'jabatan_umkm' => 'required|string|max:255',
            'no_hp_umkm' => 'required|string|max:20',
            'email_umkm' => 'required|email|max:255',
            'alamat_umkm' => 'required|string',
            'logo_umkm' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $logoPath = null;
            if ($request->hasFile('logo_umkm')) {
                $file = $request->file('logo_umkm');
                $fileName = 'Logo_' . $request->nama_umkm . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('logo-umkm', $fileName);
            }

            // Buat UMKM baru
            $umkm = new Umkm();
            $umkm->kategori_umkm_id = $request->kategori_umkm_id;
            $umkm->input_by = null; // Set input_by menjadi null karena input dari admin
            $umkm->nama_umkm = $request->nama_umkm;
            $umkm->nama_pemilik_umkm = $request->nama_pemilik_umkm;
            $umkm->jabatan_umkm = $request->jabatan_umkm;
            $umkm->no_hp_umkm = $request->no_hp_umkm;
            $umkm->email_umkm = $request->email_umkm;
            $umkm->alamat_umkm = $request->alamat_umkm;
            $umkm->logo_umkm = $logoPath;
            $umkm->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'UMKM berhasil dibuat',
                'data' => $umkm->load(['kategoriUmkm'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data PKS dan UMKM secara bersamaan
     */
    public function storePksUmkm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Validasi data UMKM
            'kategori_umkm_id' => 'required|exists:kategori_umkms,id',
            'nama_umkm' => 'required|string|max:255',
            'nama_pemilik_umkm' => 'required|string|max:255',
            'jabatan_umkm' => 'required|string|max:255',
            'no_hp_umkm' => 'required|string|max:20',
            'email_umkm' => 'required|email|max:255',
            'alamat_umkm' => 'required|string',
            'logo_umkm' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Validasi data PKS
            'tgl_pks' => 'required|date',
            'no_pks' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Cek apakah nomor PKS sudah ada di database
                    $exists = PKS::where('no_pks', $value)->exists();
                    if ($exists) {
                        $fail('Nomor PKS sudah digunakan.');
                    }
                },
            ],
            'no_pks_umkm' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Cek apakah nomor PKS UMKM sudah ada di database (jika diisi)
                    if ($value) {
                        $exists = PKS::where('no_pks_umkm', $value)->exists();
                        if ($exists) {
                            $fail('Nomor PKS UMKM sudah digunakan.');
                        }
                    }
                },
            ],
            'lama_perjanjian' => 'required|integer|min:1',
            // 'pic_pks' => 'required|string|max:255',
            // 'email_pks' => 'required|email|max:255',
            // 'alamat_pks' => 'required|string',
            'file_arsip_pks' => 'nullable|file|mimes:pdf|max:10240', // Validasi file arsip PKS (PDF, max 10MB)
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

            // Upload logo UMKM jika ada
            $logoPath = null;
            if ($request->hasFile('logo_umkm')) {
                $file = $request->file('logo_umkm');
                $fileName = 'Logo_' . $request->nama_umkm . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('logo-umkm', $fileName);
            }

            // Upload file arsip PKS jika ada
            $filePksPath = null;
            if ($request->hasFile('file_arsip_pks')) {
                $file = $request->file('file_arsip_pks');
                $fileName = 'PKS_' . $request->nama_umkm . '.' . $file->getClientOriginalExtension();
                $filePksPath = $file->storeAs('pks', $fileName);
            }

            // Buat UMKM baru
            $umkm = new Umkm();
            $umkm->kategori_umkm_id = $request->kategori_umkm_id;
            $umkm->input_by = null; // Set input_by menjadi null karena input dari admin
            $umkm->nama_umkm = $request->nama_umkm;
            $umkm->nama_pemilik_umkm = $request->nama_pemilik_umkm;
            $umkm->jabatan_umkm = $request->jabatan_umkm;
            $umkm->no_hp_umkm = $request->no_hp_umkm;
            $umkm->email_umkm = $request->email_umkm;
            $umkm->alamat_umkm = $request->alamat_umkm;
            $umkm->logo_umkm = $logoPath;
            $umkm->save();

            // Gunakan nomor PKS yang diinput manual

            // Buat PKS
            $pks = new PKS();
            $pks->tahun_akademik_id = $tahunAkademik->id;
            $pks->tgl_pks = $request->tgl_pks;
            $pks->no_pks = $request->no_pks; // Gunakan nomor PKS yang diinput manual
            $pks->no_pks_umkm = $request->no_pks_umkm;
            $pks->umkm_id = $umkm->id; // Gunakan ID UMKM yang baru dibuat
            $pks->lama_perjanjian = $request->lama_perjanjian;
            $pks->pic_pks = $request->nama_pemilik_umkm;
            $pks->email_pks = $request->email_umkm;
            $pks->alamat_pks = $request->alamat_umkm;
            $pks->file_arsip_pks = $filePksPath; // Simpan path file arsip PKS
            $pks->created_by = null; // Set created_by menjadi null karena input dari admin
            $pks->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'PKS dan UMKM berhasil dibuat',
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

    /**
     * Mengambil data PKS yang masih berlaku untuk DataTable Arsip UMKM
     */
    public function getArsip(Request $request)
    {
        try {
            // Ambil tanggal sekarang
            $today = Carbon::now()->format('Y-m-d');

        // Query untuk mengambil PKS yang masih berlaku
            $tahun_akademik_sekarang = TahunAkademik::getActive();
            $tahun_akademik_id = $tahun_akademik_sekarang ? $tahun_akademik_sekarang->id : null;

            $query = PKS::with(['umkm', 'umkm.kategoriUmkm'])
            ->leftJoin('umkms', 'p_k_s.umkm_id', '=', 'umkms.id')
            ->leftJoin('kategori_umkms', 'umkms.kategori_umkm_id', '=', 'kategori_umkms.id')
            ->select('p_k_s.*')
            ->selectRaw('DATE_ADD(tgl_pks, INTERVAL lama_perjanjian YEAR) as tanggal_berakhir')
            ->selectRaw('(SELECT COUNT(*) FROM surat_pengantars WHERE umkm_id = p_k_s.umkm_id ' .
                        ($tahun_akademik_id ? 'AND tahun_akademik_id = ' . $tahun_akademik_id : '') . ') as jumlah_umkm_digunakan')
            ->selectRaw('0 as is_used_by_current_user')
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil data UMKM tanpa PKS yang masih berlaku untuk DataTable
     */
    public function getUmkmTanpaPks(Request $request)
    {
        try {
            // Ambil tanggal sekarang
            $today = Carbon::now()->format('Y-m-d');

        // Query untuk mengambil UMKM yang tidak memiliki PKS atau PKS sudah kadaluarsa
            $query = Umkm::with('kategoriUmkm')
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
                $query->whereHas('kategoriUmkm', function ($q) use ($keyword) {
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data UMKM tanpa PKS: ' . $e->getMessage()
            ], 500);
        }
    }
}
