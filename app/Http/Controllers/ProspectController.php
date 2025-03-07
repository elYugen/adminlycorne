<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProspectController extends Controller
{
    public function index()
    {
        $prospects = Prospect::active()->get();
        return view('prospect.index', compact('prospects'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
            'company' => 'nullable|string|max:255',
            'siret' => 'nullable|string|regex:/^\+?[0-9\s\-]+$/',
            'gender' => 'nullable|string|max:10',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string:15',
            'city' => 'required|string|max:50',
        ]);
    
        // s'assurer qu'au moins le nom ou l'entreprise est renseigné
        if (empty($request->lastname) && empty($request->company)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Le nom ou l\'entreprise doit être renseigné']);
        }

        Prospect::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'company' => $request->company,
            'siret' => $request->siret,
            'city' => $request->city
        ]);
    
        return redirect()->route('prospect.index')->with('success', 'Prospect crée avec succès');
    }

    public function edit(Request $request, Prospect $prospect)
    {
        $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:bc_prospects,email,' . $prospect->id,
            'phone_number' => 'nullable|string|regex:/^\+?[0-9\s\-]+$/',
            'company' => 'nullable|string|max:255',
            'siret' => 'nullable|string|regex:/^\+?[0-9\s\-]+$/',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string:15',
        ]);

        $prospect->update([
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

        return redirect()->route('prospect.index')->with('success', 'Prospect modifié avec succès');
    }

    public function delete(Prospect $prospect)
    {
        $prospect->update(['deleted' => 1]);
        return redirect()->route('prospect.index')->with('success', 'Prospect supprimé avec succès');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $clients = Prospect::active() 
            ->where(function($q) use ($query) {
                $q->where('firstname', 'LIKE', "%{$query}%")
                  ->orWhere('lastname', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('company', 'LIKE', "%{$query}%");
            })
            ->get(['id', 'firstname', 'lastname', 'email', 'phone_number', 'company', 'city', 'postal_code', 'address']);
    
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'required|string|regex:/^\+?[0-9\s\-]+$/',
                'company' => 'nullable|string|max:255',
                'siret' => 'nullable|string|regex:/^\+?[0-9\s\-]+$/',
                'gender' => 'nullable|string|max:10',
                'address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:15',
                'city' => 'required|string|max:50',
            ]);

            if (empty($request->lastname) && empty($request->company)) {
                return response()->json([
                    'error' => 'Le nom ou l\'entreprise doit être renseigné'
                ], 422);
            }

            Log::info('Requête reçue : ', $request->all());

            $prospect = Prospect::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'company' => $request->company,
                'siret' => $request->siret,
                'city' => $request->city
            ]);

            return response()->json([
                'success' => 'Prospect créé avec succès',
                'prospect' => $prospect
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'erreur de validation',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('erreur lors de la création du prospect : ' . $e->getMessage());
            return response()->json([
                'error' => 'erreur est survenue lors de la création du prospect'
            ], 500);
        }
    }
}
