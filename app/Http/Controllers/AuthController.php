<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login page with role selection
     */
    public function showLoginPage()
    {
        return view('auth.login');
    }

    /**
     * Show the registration page with role selection
     */
    public function showRegisterPage()
    {
        return view('auth.register');
    }

    /**
     * Handle user login with role selection
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required|in:front_office,back_office',
        ], [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez entrer un email valide.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'role.required' => 'Veuillez sélectionner un profil.',
            'role.in' => 'Le profil sélectionné est invalide.',
        ]);

        // Vérifier les identifiants
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants fournis sont incorrects.',
            ]);
        }

        // Vérifier que l'utilisateur a le rôle demandé
        if ($user->role !== $request->role) {
            return back()->withErrors([
                'role' => 'Vous n\'avez pas accès à ce profil.',
            ]);
        }

        // Authentifier l'utilisateur
        Auth::login($user);

        // Rediriger directement vers le dashboard du profil
        if ($user->isBackOffice()) {
            return redirect()->route('admin.dashboard')->with('success', 'Connecté avec succès !');
        } else {
            return redirect()->route('sales.dashboard')->with('success', 'Connecté avec succès !');
        }
    }

    /**
     * Handle user registration with role selection
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:front_office,back_office',
        ], [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez entrer un email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Veuillez sélectionner un profil.',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Authentifier l'utilisateur
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Inscription réussie !');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Vous avez été déconnecté.');
    }
}
