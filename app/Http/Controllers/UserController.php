<?php

namespace App\Http\Controllers;

use App\Models\BcUtilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = BcUtilisateur::active()->get();
        return view('users.index', compact('users'));
    }

    public function delete(BcUtilisateur $bcuser)
    {
        if (Auth::id() === $bcuser->id) {
            return redirect()->route('user.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $bcuser->update(['deleted' => 1]);

        return redirect()->route('user.index')->with('success', 'Utilisateur supprimé avec succès');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:bc_utilisateurs,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:revendeur,administrateur',
        ]);

        BcUtilisateur::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('user.index')->with('success', 'Utilisateur crée avec succès');
    }

    public function edit(Request $request, BcUtilisateur $bcuser)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:bc_utilisateurs,email,' . $bcuser->id,
            'role' => 'required|in:revendeur,administrateur',
        ]);

        $bcuser->update($request->only(['name', 'email', 'role']));


        return redirect()->route('user.index')->with('success', 'Utilisateur mis à jour avec succès');
    }

}
