<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware AdminAuth
 * 
 * Middleware ini melindungi seluruh halaman admin agar hanya bisa diakses
 * oleh pengguna yang sudah login dan memiliki role 'admin'.
 * Jika belum login atau bukan admin, pengguna akan diarahkan ke halaman login.
 */
class AdminAuth
{
    /**
     * Menangani setiap request yang masuk ke rute yang dilindungi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mengecek apakah pengguna sudah login menggunakan sistem autentikasi Laravel
        if (!Auth::check()) {
            // Jika belum login, simpan halaman yang ingin dituju agar bisa dialihkan kembali setelah login
            session(['url.intended' => $request->fullUrl()]);
            // Arahkan pengguna ke halaman login dengan pesan peringatan
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
        }

        // Mengecek apakah akun yang login memiliki role 'admin'
        if (Auth::user()->role !== 'admin') {
            // Jika bukan admin, logout dan arahkan kembali ke login dengan pesan error
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }

        // Mengecek apakah status akun admin aktif
        if (Auth::user()->status !== 'aktif') {
            // Jika status tidak aktif, logout dan tolak akses
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Hubungi super admin.');
        }

        // Jika semua pengecekan lolos, lanjutkan request ke halaman yang dituju
        return $next($request);
    }
}
