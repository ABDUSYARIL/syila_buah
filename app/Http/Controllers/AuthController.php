<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function loginPage()
    {
        // Jika pengguna sudah login sebagai pelanggan, langsung arahkan ke beranda pelanggan
        // Sehingga tidak perlu login ulang jika sudah ada sesi aktif
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                // Admin yang sudah login langsung diarahkan ke dashboard admin
                return redirect()->route('admin.dashboard');
            }
            // Pelanggan yang sudah login langsung diarahkan ke halaman home pelanggan
            return redirect()->route('home');
        }

        // Menyimpan URL halaman sebelumnya agar setelah login bisa diarahkan kembali
        $prev = url()->previous();
        if ($prev && !str_contains($prev, '/login') && !str_contains($prev, '/register')) {
            session(['url.intended' => $prev]);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek status aktif
            if (isset($user->status) && $user->status !== 'aktif') {
                Auth::logout();
                return back()->with('error', 'Akun Anda tidak aktif.')->withInput();
            }

            // Catat waktu login terbaru
            $user->last_login_at = now();
            $user->save();

            // JIKA USER MENEKAN TOMBOL "MASUK SEBAGAI ADMIN"
            if ($request->has('login_admin')) {
                // Cek apakah role di database memang admin
                if ($user->role !== 'admin') {
                    Auth::logout();
                    return back()->with('error', 'Anda tidak memiliki akses sebagai Admin.')->withInput();
                }

                // Sukses masuk sebagai Admin
                session(['role' => $user->role, 'username' => $user->name]);
                return redirect()->route('admin.dashboard');
            } 
            
            // JIKA USER MENEKAN TOMBOL "MASUK" BIASA (PELANGGAN)
            else {
                // Jika akun admin tapi mencoba login lewat tombol pelanggan biasa
                if ($user->role === 'admin') {
                    Auth::logout();
                    return back()->with('error', 'Akun Admin silakan login melalui tombol Admin.')->withInput();
                }

                // Sukses masuk sebagai Pelanggan
                session(['role' => $user->role, 'username' => $user->name]);

                $intended = session()->pull('url.intended');
                if (!$intended || str_contains($intended, '/logout')) {
                    $intended = route('home');
                }
                return redirect()->to($intended);
            }
        }

        // Tampilan Pesan Error Spesifik Anda
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            $message = 'Email tidak terdaftar.';
        } else {
            $message = 'Kata sandi salah.';
        }

        return back()->with('error', $message)->withInput();
    }

    public function registerPage()
    {
        return view('auth.register');
    }

    // Fungsi untuk memproses pendaftaran akun baru
    public function register(Request $request)
    {
        // Validasi input data pendaftaran dari form pendaftaran
        $request->validate([
            'name' => 'required|string|max:255', // Nama lengkap wajib diisi, berupa teks, maksimal 255 karakter
            'email' => 'required|string|email|max:255|unique:users', // Email wajib diisi, format email valid, maksimal 255 karakter, dan harus unik (belum terdaftar)
            'password' => 'required|string|min:8', // Password wajib diisi, berupa teks, minimal 8 karakter
            'phone' => 'required|string|max:20', // Nomor HP wajib diisi, berupa teks, maksimal 20 karakter
            'address' => 'required|string', // Alamat lengkap wajib diisi dan berupa teks
        ]);

        // Simpan data pengguna baru ke dalam database tabel users
        $user = User::create([
            'name' => $request->name, // Menyimpan nama lengkap pengguna
            'email' => $request->email, // Menyimpan email pengguna
            'phone' => $request->phone, // Menyimpan nomor HP pengguna ke dalam kolom phone
            'address' => $request->address, // Menyimpan alamat lengkap pengguna ke dalam kolom address
            'password' => Hash::make($request->password), // Mengenkripsi password pengguna sebelum disimpan menggunakan Hash bcrypt
            'role' => 'pelanggan', // Menetapkan peran (role) default sebagai pelanggan
            'status' => 'aktif', // Menetapkan status akun aktif secara default
        ]);

        // Sesuai permintaan, tidak langsung login otomatis setelah mendaftar
        // Mengalihkan pengguna kembali ke halaman login dengan menyertakan pesan sukses dalam flash session
        return redirect()->route('login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk menggunakan akun baru Anda.');
    }

    public function logout()
    {
        Auth::logout();
        session()->forget(['role', 'username', 'cart']);
        return redirect()->route('landing');
    }
}