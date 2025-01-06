<?php

namespace App\Http\Controllers;

use App\Models\BcUtilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = BcUtilisateur::all();
        return view('users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:bc_utilisateurs,email',
            'password' => 'required|string'

        ]);

        BcUtilisateur::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('user.index')->with('success', 'Utilisateur crée avec succès');
    }
}
