<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DosenP2K;
use App\Models\TahunAkademik;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DosenP2KController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        $tahunAktif = TahunAkademik::getActive();
        return view('pages.admin.p2k.dosen.index', compact('tahunAkademiks', 'tahunAktif'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        $tahunAkademikId = $request->tahun_akademik_id;

        $query = DosenP2K::with('tahunAkademik');

        if ($tahunAkademikId) {
            $query->where('tahun_akademik_id', $tahunAkademikId);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik ? $row->tahunAkademik->tahun_ajaran . ' ' . $row->tahunAkademik->tipe_semester : '-';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-sm btn-edit" data-toggle="tooltip" data-placement="top" title="Edit Dosen"><i class="fas fa-edit"></i></button>';
                $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" data-placement="top" title="Hapus Dosen"><i class="fas fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_dosen' => 'required|string|max:255',
            'nama_dosen' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah data sudah ada
        $exists = DosenP2K::where('kode_dosen', $request->kode_dosen)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen dengan kode tersebut sudah ada pada tahun akademik yang dipilih'
            ], 422);
        }

        $dosenP2K = DosenP2K::create([
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->nama_dosen,
            'tahun_akademik_id' => $request->tahun_akademik_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil ditambahkan',
            'data' => $dosenP2K
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dosenP2K = DosenP2K::with('tahunAkademik')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $dosenP2K
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_dosen' => 'required|string|max:255',
            'nama_dosen' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $dosenP2K = DosenP2K::findOrFail($id);

        // Cek apakah data sudah ada (selain data yang sedang diupdate)
        $exists = DosenP2K::where('kode_dosen', $request->kode_dosen)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen dengan kode tersebut sudah ada pada tahun akademik yang dipilih'
            ], 422);
        }

        $dosenP2K->update([
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->nama_dosen,
            'tahun_akademik_id' => $request->tahun_akademik_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil diperbarui',
            'data' => $dosenP2K
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dosenP2K = DosenP2K::findOrFail($id);
        $dosenP2K->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil dihapus'
        ]);
    }

    /**
     * Get dosen data from API.
     */
    public function getDosenFromApi(ApiService $apiService)
    {
        $result = $apiService->getDosenData();
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'error' => $result['error'] ?? null
        ], 500);
    }
}
