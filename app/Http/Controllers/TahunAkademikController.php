<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TahunAkademikController extends Controller
{
    public function index()
    {
        return view('pages.tahun_akademik.index');
    }

    public function getData()
    {
        $tahunAkademik = TahunAkademik::all();
        return response()->json(['data' => $tahunAkademik]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_ajaran' => 'required|string|max:255',
            'tipe_semester' => 'required|in:Semester Ganjil,Semester Genap',
            'is_aktif' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if combination already exists
        $exists = TahunAkademik::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('tipe_semester', $request->tipe_semester)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kombinasi Tahun Ajaran dan Tipe Semester sudah ada'
            ], 422);
        }

        // Jika is_aktif bernilai true, nonaktifkan semua tahun akademik lainnya
        if ($request->has('is_aktif') && $request->is_aktif) {
            TahunAkademik::where('is_aktif', 1)->update(['is_aktif' => 0]);
        }

        $tahunAkademik = new TahunAkademik();
        $tahunAkademik->tahun_ajaran = $request->tahun_ajaran;
        $tahunAkademik->tipe_semester = $request->tipe_semester;
        $tahunAkademik->is_aktif = $request->has('is_aktif') && $request->is_aktif ? 1 : 0;
        $tahunAkademik->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tahun Akademik berhasil ditambahkan',
            'data' => $tahunAkademik
        ]);
    }

    public function show($id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (!$tahunAkademik) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tahun Akademik tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $tahunAkademik
        ]);
    }

    public function update(Request $request, $id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (!$tahunAkademik) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tahun Akademik tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tahun_ajaran' => 'required|string|max:255',
            'tipe_semester' => 'required|in:Semester Ganjil,Semester Genap',
            'is_aktif' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if combination already exists (excluding current record)
        $exists = TahunAkademik::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('tipe_semester', $request->tipe_semester)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kombinasi Tahun Ajaran dan Tipe Semester sudah ada'
            ], 422);
        }

        // Jika is_aktif bernilai true, nonaktifkan semua tahun akademik lainnya
        if ($request->has('is_aktif') && $request->is_aktif) {
            TahunAkademik::where('id', '!=', $id)->where('is_aktif', 1)->update(['is_aktif' => 0]);
        }

        $tahunAkademik->tahun_ajaran = $request->tahun_ajaran;
        $tahunAkademik->tipe_semester = $request->tipe_semester;
        $tahunAkademik->is_aktif = $request->has('is_aktif') ? ($request->is_aktif ? 1 : 0) : $tahunAkademik->is_aktif;
        $tahunAkademik->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tahun Akademik berhasil diperbarui',
            'data' => $tahunAkademik
        ]);
    }

    public function destroy($id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (!$tahunAkademik) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tahun Akademik tidak ditemukan'
            ], 404);
        }

        $tahunAkademik->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tahun Akademik berhasil dihapus'
        ]);
    }
}
