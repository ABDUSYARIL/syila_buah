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

            // --- LOGIKA UTAMA PEMISAH TOMBOL LOGIN ---

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

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan',
            'status' => 'aktif',
        ]);

        Auth::login($user);

        session(['role' => $user->role, 'username' => $user->name]);
        
        $intended = session()->pull('url.intended', route('home'));
        return redirect()->to($intended);
    }

    public function logout()
    {
        Auth::logout();
        session()->forget(['role', 'username', 'cart']);
        return redirect()->route('landing');
    }
}