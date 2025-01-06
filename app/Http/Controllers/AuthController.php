<?php

namespace App\Http\Controllers;

use App\Models\BcUtilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check())
        {
           return redirect('/dashboard');
        }

        return view('auth.index');
    }

    public function authenticate(Request $request) {

        $credentials = $request->validate([ 
            'email' => ['required', 'email'], 
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([ 
            'email' => 'Adresse mail ou mot de passe incorrect',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }      
}
