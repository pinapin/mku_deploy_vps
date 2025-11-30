<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Soal;
use App\Services\UrlEncryptionService;
use Illuminate\Support\Facades\Validator;

class UjianController extends Controller
{
   

    public function index()
    {
        $ujians = Ujian::withCount('soal')->orderBy('created_at', 'desc')->get();
        return view('pages.admin.ujian.index', compact('ujians'));
    }

    public function create()
    {
        return view('pages.admin.ujian.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_ujian' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ujian = Ujian::create([
            'nama_ujian' => $request->nama_ujian,
            'durasi_menit' => $request->durasi_menit,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('master.ujian.index')
            ->with('success', 'Ujian berhasil ditambahkan');
    }

    public function show($id)
    {
        $ujian = Ujian::with(['soal.pilihan', 'soal' => function($query) {
            $query->orderBy('nomor_soal');
        }])->findOrFail($id);

        // Handle preview mode
        if (request()->get('preview') == 'true') {
            return view('pages.admin.ujian.preview', compact('ujian'));
        }

        return view('pages.admin.ujian.show', compact('ujian'));
    }

    public function edit($id)
    {
        $ujian = Ujian::findOrFail($id);
        return view('pages.admin.ujian.edit', compact('ujian'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_ujian' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ujian = Ujian::findOrFail($id);
        $ujian->update([
            'nama_ujian' => $request->nama_ujian,
            'durasi_menit' => $request->durasi_menit,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('master.ujian.index')
            ->with('success', 'Ujian berhasil diperbarui');
    }

    public function destroy($id)
    {
        try {
            $ujian = Ujian::findOrFail($id);

            // Check if there are any exam sessions
            if ($ujian->sesiUjian()->exists()) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Tidak dapat menghapus ujian yang sudah memiliki sesi ujian');
            }

            $ujian->delete();

            return redirect()->route('master.ujian.index')
                ->with('success', 'Ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal menghapus ujian: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $ujian = Ujian::findOrFail($id);
            $ujian->update([
                'is_active' => !$ujian->is_active
            ]);

            $status = $ujian->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->route('master.ujian.index')
                ->with('success', "Ujian berhasil {$status}");
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal mengubah status ujian: ' . $e->getMessage());
        }
    }

    /**
     * Show ujian dengan encrypted ID
     */
    public function showEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::with(['soal.pilihan', 'soal' => function($query) {
                $query->orderBy('nomor_soal');
            }])->findOrFail($id);

            return view('pages.admin.ujian.show', compact('ujian'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat data ujian: ' . $e->getMessage());
        }
    }

    /**
     * Edit ujian dengan encrypted ID
     */
    public function editEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);
            return view('pages.admin.ujian.edit', compact('ujian'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat form edit: ' . $e->getMessage());
        }
    }

    /**
     * Update ujian dengan encrypted ID
     */
    public function updateEncrypted(Request $request, $encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $validated = $request->validate([
                'nama_ujian' => 'required|string|max:255',
                'durasi_menit' => 'required|integer|min:1|max:720',
                'deskripsi' => 'nullable|string'
            ]);

            $ujian = Ujian::findOrFail($id);
            $ujian->update([
                'nama_ujian' => $validated['nama_ujian'],
                'durasi_menit' => $validated['durasi_menit'],
                'deskripsi' => $validated['deskripsi'],
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            return redirect()->route('master.ujian.index')
                ->with('success', 'Ujian berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui ujian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Destroy ujian dengan encrypted ID
     */
    public function destroyEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::findOrFail($id);

            // Check if there are any exam sessions
            if ($ujian->sesiUjian()->exists()) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Tidak dapat menghapus ujian yang sudah memiliki sesi ujian');
            }

            $ujian->delete();

            return redirect()->route('master.ujian.index')
                ->with('success', 'Ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal menghapus ujian: ' . $e->getMessage());
        }
    }

    /**
     * Preview ujian dengan encrypted ID
     */
    public function previewEncrypted($encryptedId)
    {
        try {
            $id = UrlEncryptionService::decryptId($encryptedId);

            if (!$id) {
                return redirect()->route('master.ujian.index')
                    ->with('error', 'Invalid or expired URL');
            }

            $ujian = Ujian::with(['soal.pilihan', 'soal' => function($query) {
                $query->orderBy('nomor_soal');
            }])->findOrFail($id);

            return view('pages.admin.ujian.preview', compact('ujian'));
        } catch (\Exception $e) {
            return redirect()->route('master.ujian.index')
                ->with('error', 'Gagal memuat preview: ' . $e->getMessage());
        }
    }
}