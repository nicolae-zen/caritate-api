<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Returnam datele utilizatorului autentificat (curent)
     * 
     * @group Utilizatori
     * 
     * @authenticated
     */
    public function profile(Request $request)
    {
        $user = auth('api')->user();

        return response()->json([
            'message' => 'Datele profilului',
            'user' => $user,
        ], 200);
    }

    /**
     * @group Utilizatori
     * 
     * Actualizeaza datele profilului utilizatorului autentificat.
     * 
     * @authenticated
     * 
     * @bodyParam name string optional Numele utilizatorului. Example: Jogn Doe
     * @bodyParam email string optional Email-ul utilizatorului. Example: jd@exemplu.com
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        
        $request->validate([
            'name'  => 'nullable|string|max:255',
            'email' => 'nullable| email|max:255|unique:users,email, ' . $user->id, // un email nou, daca este schimbat)
        ]);

        $user->update([
            'name'  => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        return response()->json([
            'message' => 'Profil actualizat cu succes.',
            'user' => $user,
        ], 200);
    }
}
