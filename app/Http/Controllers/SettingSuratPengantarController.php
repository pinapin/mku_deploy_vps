<?php

namespace App\Http\Controllers;

use App\Models\SettingSuratPengantar;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SettingSuratPengantarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        return view('pages.setting_surat_pengantar.index', compact('tahunAkademiks'));
    }

    /**
     * Get setting surat pengantar data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData()
    {
        $settings = SettingSuratPengantar::with('tahunAkademik')->get();
        return response()->json(['data' => $settings]);
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
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'no_surat' => 'required|string|max:255',
            'qr_surat_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Cek apakah sudah ada setting untuk tahun akademik yang sama
        $existingSetting = SettingSuratPengantar::where('tahun_akademik_id', $request->tahun_akademik_id)->first();

        if ($existingSetting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting surat pengantar untuk tahun akademik ini sudah ada. Silahkan edit setting yang sudah ada.'
            ], 422);
        }

        $setting = new SettingSuratPengantar();
        $setting->tahun_akademik_id = $request->tahun_akademik_id;
        $setting->no_surat = $request->no_surat;

        // Handle image upload
        if ($request->hasFile('qr_surat_image')) {
            $image = $request->file('qr_surat_image');
            $imageName = 'qr_surat_pengantar_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('qr_surat', $imageName);
            $setting->qr_surat_image = 'qr_surat/' . $imageName;
        }

        $setting->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Setting surat pengantar berhasil ditambahkan',
            'data' => $setting
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
        $setting = SettingSuratPengantar::find($id);

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting surat pengantar tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $setting
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
        $setting = SettingSuratPengantar::find($id);

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting surat pengantar tidak ditemukan'
            ], 404);
        }

        $rules = [
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'no_surat' => 'required|string|max:255',
            'qr_surat_image' => ($setting->qr_surat_image ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Cek apakah ada setting lain dengan tahun akademik yang sama (selain setting ini)
        $existingSetting = SettingSuratPengantar::where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSetting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting surat pengantar untuk tahun akademik ini sudah ada.'
            ], 422);
        }

        $setting->tahun_akademik_id = $request->tahun_akademik_id;
        $setting->no_surat = $request->no_surat;

        // Handle image upload
        if ($request->hasFile('qr_surat_image')) {
            // Delete old image if exists
            if ($setting->qr_surat_image && Storage::disk('public')->exists($setting->qr_surat_image)) {
                Storage::disk('public')->delete($setting->qr_surat_image);
            }

            $image = $request->file('qr_surat_image');
            $imageName = 'qr_surat_pengantar_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('qr_surat', $imageName);
            $setting->qr_surat_image = 'qr_surat/' . $imageName;
        }

        $setting->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Setting surat pengantar berhasil diperbarui',
            'data' => $setting
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
        $setting = SettingSuratPengantar::find($id);

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting surat pengantar tidak ditemukan'
            ], 404);
        }

        // Delete image if exists
        if ($setting->qr_surat_image && Storage::disk('public')->exists($setting->qr_surat_image)) {
            Storage::disk('public')->delete($setting->qr_surat_image);
        }

        $setting->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Setting surat pengantar berhasil dihapus'
        ]);
    }
}
