<?php

namespace App\Http\Controllers;

use App\Models\BcUtilisateur;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = BcUtilisateur::all();

        return view('users.index', compact('users'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:bc_utilisateurs,email',
            'phone_number' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'entreprise' => 'required|string|max:255',
            'siret' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'civilite' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string:15',
        ]);

        BcUtilisateur::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'civilite' => $request->civilite,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'entreprise' => $request->entreprise,
            'siret' => $request->siret
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur crée avec succès');
    }

    public function editUser(Request $request, BcUtilisateur $bcuser)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:bc_utilisateurs,email,' . $bcuser->id,
            'phone_number' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'entreprise' => 'required|string|max:255',
            'siret' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'civilite' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string:15',
        ]);

        $bcuser->update([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'civilite' => $request->civilite,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'entreprise' => $request->entreprise,
            'siret' => $request->siret
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié avec succès');
    }

    public function deleteUser(BcUtilisateur $bcuser)
    {
        $bcuser->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès');

    }
}
