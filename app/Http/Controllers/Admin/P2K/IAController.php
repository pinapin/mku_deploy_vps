<?php

namespace App\Http\Controllers\Admin\P2K;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Formatter;
use App\Models\LaporanAkhirMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class IAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun_ajaran', 'desc')
            ->orderBy('tipe_semester', 'desc')
            ->get();

        $tahunAktif = TahunAkademik::getActive();

        return view('pages.admin.p2k.ia.index', compact('tahunAkademiks', 'tahunAktif'));
    }

    /**
     * Return data for DataTables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $query = LaporanAkhirMahasiswa::with([
            'tahunAkademik',
            'mahasiswa',
            'kelasDosenP2K'
        ])->select('laporan_akhir_mahasiswas.*');

        // Filter by tahun akademik
        if ($request->has('tahun_akademik_id') && !empty($request->tahun_akademik_id)) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return $row->created_at ? Formatter::date($row->created_at) : '-';
            })
            ->addColumn('nim', function ($row) {
                return $row->nim ?? '-';
            })
            ->addColumn('nama_mahasiswa', function ($row) {
                return $row->mahasiswa ? $row->mahasiswa->nama : '-';
            })
            ->addColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik ?
                    $row->tahunAkademik->tahun_ajaran . ' ' . $row->tahunAkademik->tipe_semester : '-';
            })
            ->addColumn('kelas', function ($row) {
                $kelas = $row->kelas ?? '-';
                $kelompok = $row->kelompok ? " (Kelompok {$row->kelompok})" : '';
                return $kelas . $kelompok;
            })
            ->addColumn('file_ia', function ($row) {
                if ($row->file_ia) {
                    return '<a href="' . asset('storage/' . $row->file_ia) . '"
                            target="_blank"
                            class="btn btn-sm btn-info"
                            data-toggle="tooltip"
                            title="Lihat File IA">
                            <i class="fas fa-file-pdf"></i> Lihat
                        </a>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('aksi', function ($row) {
                return '<button type="button"
                        class="btn btn-sm btn-primary btn-detail"
                        data-id="' . $row->id . '"
                        data-toggle="tooltip"
                        title="Detail">
                        <i class="fas fa-info-circle"></i>
                    </button>';
            })
            ->rawColumns(['file_ia', 'aksi'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $laporan = LaporanAkhirMahasiswa::with([
            'tahunAkademik',
            'mahasiswa',
            'kelasDosenP2K',
            'validator'
        ])->find($id);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $laporan
        ]);
    }
}
