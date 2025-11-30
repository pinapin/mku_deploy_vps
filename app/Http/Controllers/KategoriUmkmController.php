<?php

namespace App\Http\Controllers;

use App\Models\KategoriUmkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriUmkmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.kategori_umkm.index');
    }

    /**
     * Get all kategori umkm data for DataTable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData()
    {
        $kategoriUmkm = KategoriUmkm::all();
        return response()->json(['data' => $kategoriUmkm]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:kategori_umkms'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $kategoriUmkm = new KategoriUmkm();
        $kategoriUmkm->nama_kategori = $request->nama_kategori;
        $kategoriUmkm->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori UMKM berhasil ditambahkan',
            'data' => $kategoriUmkm
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $kategoriUmkm = KategoriUmkm::find($id);

        if (!$kategoriUmkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori UMKM tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kategoriUmkm
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $kategoriUmkm = KategoriUmkm::find($id);

        if (!$kategoriUmkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori UMKM tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:kategori_umkms,nama_kategori,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $kategoriUmkm->nama_kategori = $request->nama_kategori;
        $kategoriUmkm->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori UMKM berhasil diperbarui',
            'data' => $kategoriUmkm
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $kategoriUmkm = KategoriUmkm::find($id);

        if (!$kategoriUmkm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori UMKM tidak ditemukan'
            ], 404);
        }

        $kategoriUmkm->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori UMKM berhasil dihapus'
        ]);
    }
}
