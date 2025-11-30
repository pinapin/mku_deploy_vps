<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\KategoriUmkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    public function index()
    {
        $kategoriUmkm = KategoriUmkm::all();
        return view('pages.umkm.index', compact('kategoriUmkm'));
    }

    public function getData()
    {
        $umkm = Umkm::with('kategoriUmkm')->get();
        return response()->json(['data' => $umkm]);
    }

    public function getDetail($id)
    {
        $umkm = Umkm::with(['kategoriUmkm'])->find($id);

        if (!$umkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'UMKM tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data UMKM berhasil ditemukan',
            'data' => $umkm
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_umkm_id' => 'required|exists:kategori_umkms,id',
            'nama_umkm' => 'required|string|max:255',
            'nama_pemilik_umkm' => 'required|string|max:255',
            'jabatan_umkm' => 'required|string|max:255',
            'no_hp_umkm' => 'required|string|max:20',
            'email_umkm' => 'required|string|max:255',
            'alamat_umkm' => 'required|string',
            'logo_umkm' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $nim = Session::get('nim');

        $umkm = new Umkm();
        $umkm->kategori_umkm_id = $request->kategori_umkm_id;
        $umkm->input_by = $nim; // Simpan ID user yang login
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

        $validator = Validator::make($request->all(), [
            'kategori_umkm_id' => 'required|exists:kategori_umkms,id',
            'nama_umkm' => 'required|string|max:255',
            'nama_pemilik_umkm' => 'required|string|max:255',
            'jabatan_umkm' => 'required|string|max:255',
            'no_hp_umkm' => 'required|string|max:20',
            'email_umkm' => 'required|string|max:255',
            'alamat_umkm' => 'required|string',
            'logo_umkm' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
