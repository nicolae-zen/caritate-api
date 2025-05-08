<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Cause;

class DonationController extends Controller
{
    /**
     * @group Donatii
     * 
     * Obtine lista donatiilor, facute de utilizatorul logat
     * 
     * @authenticated
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $donations = Donation::where('user_id', $user->id)
            ->with('cause') // Date despre causa (obtional)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'message' => 'Lista donatiilor utilizatorului.',
            'donations' => $donations,
        ]);
    }

    /**
     * @group Donatii
     * 
     * Cream o noua donatie, unica
     * 
     * @authenticated
     * 
     * @bodyParam amount float required Suma donata. Example: 100.00
     * @bodyParam cause_id integer optional ID-ul cauzei pentru care se face donatia. Example: 1
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        $validated = $request->validate([
            'amount'    => 'required|numeric|min:1',
            'cause_id'  => 'nullable|exists:causes,id',
        ]);

        $donation = Donation::create([
            'user_id' => $user->id,
            'cause_id' => $validated['cause_id'] ?? null,
            'amount' => $validated['amount'],
            'currency' => 'MDL', // De adaugat posibilitatea de modificare
            'status' => 'pending', // Default pending
            'payment_gateway' => null,
            'payment_reference' => null,
        ]);

        return response()->json([
            'message' => 'Donatie initiata cu succes.',
            'donation' => $donation,
        ], 201);
    }

    /**
     * @group Donatii
     * 
     * Afișează detalii despre o donație a utilizatorului.
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul donației. Example: 12
     */
    public function show(Request $request, $id)
    {
        $user = auth('api')->user();

        $donation = Donation::where('user_id', $user->id)
            ->where('id', $id)
            ->with('cause')
            ->first();
        
        if(!$donation) {
            return response()->json([
                'message' => 'Donatia nu a fost gasita.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detalii donatie.',
            'donation' => $donation,
        ]);
    }
}
