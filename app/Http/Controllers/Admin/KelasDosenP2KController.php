<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DosenP2K;
use App\Models\KelasDosenP2K;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class KelasDosenP2KController extends Controller
{
    public function index()
    {
        // Ambil daftar tahun akademik
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();

        return view('pages.admin.kelas.index', compact('tahunAkademiks'));
    }
    
    public function showKelas($tahunAkademikId)
    {
        // Ambil daftar dosen
        $dosens = DosenP2K::get();
        
        // Ambil tahun akademik yang dipilih
        $tahunAkademik = TahunAkademik::findOrFail($tahunAkademikId);
        
        return view('pages.admin.kelas.kelas', compact('dosens', 'tahunAkademik'));
    }

    public function getData()
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')
            ->orderBy('tipe_semester', 'asc')
            ->get();
            
        $data = $tahunAkademiks->map(function($item) {
            $kelasCount = KelasDosenP2K::where('tahun_akademik_id', $item->id)->count();
            
            return [
                'id' => $item->id,
                'tahun_ajaran' => $item->tahun_ajaran,
                'tipe_semester' => $item->tipe_semester,
                'is_aktif' => $item->is_aktif,
                'kelas_count' => $kelasCount
            ];
        });

        return response()->json(['data' => $data]);
    }
    
    public function getKelasData($tahunAkademikId)
    {
        $kelasDosenP2Ks = KelasDosenP2K::with(['tahunAkademik', 'dosen'])
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->get();

        return response()->json(['data' => $kelasDosenP2Ks]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_dosen' => 'required',
            'kelas' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        // Gunakan tahun akademik dari request jika ada, jika tidak gunakan yang aktif
        if (isset($request->tahun_akademik_id)) {
            $tahunAkademik = TahunAkademik::find($request->tahun_akademik_id);
            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tahun akademik tidak ditemukan'
                ], 422);
            }
        } else {
            // Ambil tahun akademik aktif
            $tahunAkademik = TahunAkademik::getActive();
            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tahun akademik yang aktif'
                ], 422);
            }
        }
        
        // Pisahkan kelas yang diinput (format: 01,02,03)
        $kelasList = array_map('trim', explode(',', $request->kelas));
        $createdClasses = [];
        $existingClasses = [];
        $invalidClasses = [];
        
        DB::beginTransaction();

        try {
            foreach ($kelasList as $kelas) {
                // Validasi format kelas
                if (strlen($kelas) != 2 || !is_numeric($kelas)) {
                    $invalidClasses[] = $kelas;
                    continue;
                }
                
                // Check if combination already exists
                $exists = KelasDosenP2K::where('tahun_akademik_id', $tahunAkademik->id)
                    // ->where('dosen_user_id', $request->dosen_user_id)
                    ->where('kelas', $kelas)
                    ->exists();

                if ($exists) {
                    $existingClasses[] = $kelas;
                    continue;
                }
                
                $kelasDosenP2K = new KelasDosenP2K();
                $kelasDosenP2K->tahun_akademik_id = $tahunAkademik->id;
                $kelasDosenP2K->kode_dosen = $request->kode_dosen;
                $kelasDosenP2K->kelas = $kelas;
                $kelasDosenP2K->save();
                
                $createdClasses[] = $kelas;
            }

            if (!empty($invalidClasses)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format kelas tidak valid. Gunakan format 2 digit (contoh: 01)',
                    'invalid_classes' => $invalidClasses
                ], 422);
            }
            
            if (empty($createdClasses) && !empty($existingClasses)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Semua kelas yang dimasukkan sudah ada',
                    'existing_classes' => $existingClasses
                ], 422);
            }

            DB::commit();
            
            $response = [
                'status' => 'success',
                'message' => count($createdClasses) . ' kelas berhasil ditambahkan',
                'created_classes' => $createdClasses
            ];
            
            if (!empty($existingClasses)) {
                $response['warning'] = 'Beberapa kelas sudah ada dan dilewati';
                $response['existing_classes'] = $existingClasses;
            }

            return response()->json($response);
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
        $kelasDosenP2K = KelasDosenP2K::with(['tahunAkademik', 'dosen'])
            ->find($id);

        if (!$kelasDosenP2K) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kelasDosenP2K
        ]);
    }

    public function update(Request $request, $id)
    {
        $kelasDosenP2K = KelasDosenP2K::find($id);

        if (!$kelasDosenP2K) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_dosen' => 'required',
            'kelas' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        // Gunakan tahun akademik dari request jika ada, jika tidak gunakan yang aktif
        if (isset($request->tahun_akademik_id)) {
            $tahunAkademik = TahunAkademik::find($request->tahun_akademik_id);
            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tahun akademik tidak ditemukan'
                ], 422);
            }
        } else {
            // Ambil tahun akademik aktif
            $tahunAkademik = TahunAkademik::getActive();
            if (!$tahunAkademik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tahun akademik yang aktif'
                ], 422);
            }
        }

        // Validasi format kelas
        if (strlen($request->kelas) > 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format kelas tidak valid. Gunakan format 2 digit (contoh: 01)'
            ], 422);
        }

        // Check if combination already exists (excluding current record)
        $exists = KelasDosenP2K::where('tahun_akademik_id', $tahunAkademik->id)
            // ->where('dosen_user_id', $request->dosen_user_id)
            ->where('kelas', $request->kelas)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kombinasi Tahun Akademik, Dosen, dan Kelas sudah ada'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $kelasDosenP2K->tahun_akademik_id = $tahunAkademik->id;
            $kelasDosenP2K->kode_dosen = $request->kode_dosen;
            $kelasDosenP2K->kelas = $request->kelas;
            $kelasDosenP2K->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kelas berhasil diperbarui',
                'data' => $kelasDosenP2K->load(['tahunAkademik', 'dosen'])
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
        $kelasDosenP2K = KelasDosenP2K::find($id);

        if (!$kelasDosenP2K) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        try {
            $kelasDosenP2K->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kelas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}