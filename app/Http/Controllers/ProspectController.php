<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function index()
    {
        $users = Prospect::paginate(10);

        return view('prospect.index', compact('users'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'company' => 'required|string|max:255',
            'siret' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'gender' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string:15',
        ]);

        Prospect::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'company' => $request->company,
            'siret' => $request->siret
        ]);

        return redirect()->route('prospect.index')->with('success', 'Utilisateur crée avec succès');
    }

    public function edit(Request $request, Prospect $bcuser)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:bc_utilisateurs,email,' . $bcuser->id,
            'phone_number' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'company' => 'required|string|max:255',
            'siret' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'gender' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string:15',
        ]);

        $bcuser->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'company' => $request->company,
            'siret' => $request->siret
        ]);

        return redirect()->route('prospect.index')->with('success', 'Utilisateur modifié avec succès');
    }

    public function delete(Prospect $bcuser)
    {
        $bcuser->delete();
        return redirect()->route('prospect.index')->with('success', 'Utilisateur supprimé avec succès');

    }
}
