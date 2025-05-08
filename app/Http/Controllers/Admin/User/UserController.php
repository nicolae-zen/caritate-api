<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * @group Utilizatori (Admin)
     * 
     * Obtinem lista tuturor utilizatorilor
     * 
     * @authenticated
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();

        return response()->json([
            'message' => 'Lista tuturor utilizatorilor.',
            'users' => $users,
        ]);
    }

    /**
     * @group Utilizatori (Admin)
     * 
     * Adaugam un nou utilizator
     * 
     * @authenticated
     * 
     * @bodyParam name string optional  Numele utilizatorului. Example: John Doe
     * @bodyParam email string optional  Email-ul utilizatorului. Example: john@eeample.com
     * @bodyParam phone_number string required Numar de telefon. Example: 37370707070
     * @bodyParam is_admin boolean required Rolul de admin. Example: false
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email',
            'phone_number'  => 'required|string|min:8|max:20',
            'is_admin'      => 'required|boolean',
        ]);

        if (User::where('phone_number', $validated['phone_number'])->exists()) {
            return response()->json([
                'message' => 'Un utilizator cu acest număr de telefon deja există.',
                'suggestion' => 'Introduceți un alt număr.',
            ], 409);
        }
        
        if (!empty($validated['email']) && User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'Email-ul este deja folosit.',
                'suggestion' => 'Introduceți alt email sau lăsați gol.',
            ], 409);
        }
        

        $user = User::create([
            'phone_number'  => $validated['phone_number'],
            'name'          => $validated['name'] ?? null,
            'email'         => $validated['email'] ?? null,
            'is_admin'      => $validated['is_admin'],
        ]);

        return response()->json([
            'message' => 'Utilizator creat cu succes.',
            'user' => $user,
        ], 201);
    }

    /**
     * @group Utilizatori (Admin)
     * 
     * Actualizam un utilizator existent
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul utilizatorului. Example: 4
     * @bodyParam name string optional Numele utilizatorului. Example: John Cena
     * @bodyParam email string optional Email-ul utilizatorului. Example: john@cena.com
     * @bodyParam phone_number string required Numarul de telefon. Example: 37371717171
     * @bodyParam is_admin boolean optional Rolul de admin. Example: true
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email',
            'phone_number'  => 'required|string|min:8|max:20',
            'is_admin'      => 'nullable|boolean',
        ]);

        $data = [];

        // Validam phone_number de unicitate
        if($validated['phone_number'] !== $user->phone_number)
        {
            $conflict = User::where('phone_number', $validated['phone_number'])
                            ->where('id', '!=', $user->id)
                            ->exists();

            if($conflict)
            {
                return response()->json([
                    'message' => 'Acest numar de telefon este deja folosit de alt utilizator.',
                ], 409);
            }
            $data['phone_number'] = $validated['phone_number'];
        }

        if(!empty($validated['email']) && $validated['email'] !== $user->email)
        {
            $conflict = User::where('email', $validated['email'])
                            ->where('id', '!=', $user->id)
                            ->exists();
            
            if($conflict)
            {
                return response()->json([
                    'message' => 'Email-ul este deja folosit de alt utilizator.',
                ], 409);
            }
            $data['email'] = $validated['email'];
        }

        if($request->filled('name'))
        {
            $data['name'] = $validated['name'];
        }

        if(array_key_exists('is_admin', $validated))
        {
            $data['is_admin'] = $validated['is_admin'];
        }

        if(!empty($data))
        {
            $user->update($data);
        }

        return response()->json([
            'message' => 'Utilizator actualizat cu succes.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * @group Utilizatori (Admin)
     * 
     * Stergem definitiv un utilizator
     * 
     * @authenticated
     * @urlParam id integer required ID-ul utilizatorului care trebuie sters. Example: 2
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return response()->json([
                'message' => 'Utilizatorul nu a fost gasit.',
            ], 404);
        }

        // Prevenim stergerea proprie daca suntem admin
        if($user->is_admin && $request->user()->id === $user->id)
        {
            return response()->json([
                'message' => 'Nu poti sterge propriul cont de administrator.',
            ], 403);
        } 

        $user->delete();

        return response()->json([
            'message' => 'Utilizatorul a fost sters cu succes.',
        ]);
    }
}
