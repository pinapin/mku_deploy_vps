<?php

namespace App\Http\Middleware;

use App\Models\DosenP2K;
use App\Models\LoginLog;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\ApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $apiKeySSO = '4c2fabb5fd1087137ecf94d041b2cad3af164883';

    public function handleSSOToken(string $token): bool
    {
        $get_aktif_TA = TahunAkademik::getActive();
        $get_data = (new ApiService())->getSSO('auth/sso', [
            'Authorization' => $token,
            'umk_api_key' => $this->apiKeySSO,
        ]);

        if (!isset($get_data['status']) || !$get_data['status']) {
            return false;
        }

        $data = $get_data['result']['data'];

        //hanya untuk debug
        // Session::put([
        //     'kode' => '051010',
        //     'role' => 'dosen',
        //     'nama' => $data['nama_gelar'],
        //     'email' => $data['email'],
        // ]);

        Session::put([
            'kode' => '202311457',
            'role' => 'mahasiswa',
            'nama' => 'qwerty',
            'email' => $data['email'],
        ]);
        return true;

        //  Session::put([
        //     'kode' => '2121',
        //     'role' => 'tamu',
        //     'nama' => 'qwerty',
        //     'email' => $data['email'],
        // ]);
        // return true;
        //tutup hanya untuk debug 

        if ($data['level'] == 'pegawai') {
            $cek_akses = User::where('username', $data['kode'])->first();
            if ($cek_akses) {
                Session::put([
                    'kode' => $data['kode'],
                    'role' => 'admin',
                    'nama' => $data['nama_gelar'],
                    'email' => $data['email'],
                ]);

                LoginLog::create([
                    'user_id' => $cek_akses->id,
                    'role' => 'admin',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'success',
                ]);

                return true;
            }
        } else if ($data['level'] == 'mahasiswa') {
            $cek_akses = Mahasiswa::where('nim', $data['nim'])
                ->where('tahun_akademik_id', $get_aktif_TA->id)
                ->first();
            if ($cek_akses) {
                Session::put([
                    'kode' => $data['nim'],
                    'role' => $data['level'],
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'prodi' => $data['prodi'],
                ]);

                LoginLog::create([
                    'user_id' => $cek_akses->id,
                    'role' => 'mahasiswa',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'success',
                ]);

                return true;
            }
        } else if ($data['level'] == 'dosen') {
            $cek_akses = DosenP2K::where('kode_dosen', $data['kode'])->first();
            if ($cek_akses) {
                Session::put([
                    'kode' => $data['kode'],
                    'role' => $data['level'],
                    'nama' => $data['nama_gelar'],
                    'email' => $data['email'],
                    'prodi' => $data['prodi'],
                ]);

                LoginLog::create([
                    'user_id' => $cek_akses->id,
                    'role' => 'dosen',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'success',
                ]);

                return true;
            }
        }

        LoginLog::create([
            'user_id' => null,
            'role' => $data['level'] ?? 'unknown',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'failed',
        ]);

        return false;
    }
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('kode')) {
            return $next($request);
        }

        if ($request->is('login')) {
            if (isset($_COOKIE['sso_token'])) {
                $result = $this->handleSSOToken($_COOKIE['sso_token']);

                if ($result) {
                    return redirect()->route('dashboard');
                } else {
                    return response()->view('404', [], 403);
                }
            }

            return $next($request);
        }

        if (isset($_COOKIE['sso_token'])) {
            $result = $this->handleSSOToken($_COOKIE['sso_token']);

            if ($result) {
                return redirect()->route('dashboard');
            } else {
                return response()->view('404', [], 403);
            }
        }

        return redirect()->route('login');
    }
}
