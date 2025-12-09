<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form for admin
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }
    
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        $credentials = $request->only('email', 'password');
        
        // Coba login
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Redirect ke dashboard admin
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email', 'remember'));
    }
    
    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
    
    /**
     * Create default admin user if not exists
     */
    public function createDefaultAdmin()
    {
        // Cek apakah admin sudah ada
        $adminExists = User::where('email', 'admin@furawacafe.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Admin Furawa',
                'email' => 'admin@furawacafe.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]);
            
            return "Admin user created successfully!";
        }
        
        return "Admin user already exists!";
    }
}