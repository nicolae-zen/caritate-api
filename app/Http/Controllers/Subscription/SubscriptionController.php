<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * @group Abonamente
     * 
     * Afiseaza abonamentul recurent activ
     * 
     * @authenticated
     */
    public function index()
    {
        $user = auth('api')->user();

        $subscriptions = Subscription::where('user_id', $user->id)
            ->with('cause')
            ->get();
        
            return response()->json([
                'message' => 'Lista abonamentelor active.',
                'subscriptions' => $subscriptions
            ]);
    }

    /**
     * @group Abonamente
     * 
     * Creaza un nou abonament de donatie lunara
     * 
     * @authenticated
     * 
     * @bodyParam amount decimal required Suma lunara donata. Example: 200
     * @bodyParam day_of_month integerrequired Ziua lunii pentru debitare (1-28). Example: 15
     * @bodyParam cause_id pptional ID-ul cauzei (sau null pentr fond general). Example: 1
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
    
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'day_of_month' => 'required|integer|between:1,28',
            'cause_id' => 'nullable|exists:causes,id',
        ]);
    
        $day = (int) $validated['day_of_month'];
        $today = now();
    
        // START_DATE = următoarea zi calendaristică 15 (azi sau luna viitoare)
        if ($today->day > $day) {
            $startDate = $today->copy()->addMonthNoOverflow()->day($day);
        } else {
            $startDate = $today->copy()->day($day);
        }
    
        // NEXT_CHARGE = luna următoare față de start
        $nextChargeDate = $startDate->copy()->addMonthNoOverflow();
    
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'cause_id' => $validated['cause_id'] ?? null,
            'amount' => $validated['amount'],
            'day_of_month' => $day,
            'status' => 'ACTIVE',
            'start_date' => $startDate->toDateString(),
            'next_charge_date' => $nextChargeDate->toDateString(),
            'payment_token' => null,
        ]);
    
        return response()->json([
            'message' => 'Abonament creat cu succes.',
            'subscription' => $subscription
        ], 201);
    }    
    

    /**
     * @group Abonamente
     * 
     * Actualizeaza suma/ziua debitarii pentru un abonament existent
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul abonamentului care trebuie actualizat. Example: 3
     * 
     * @bodyParam amount decimal optional Noua suma lunara donata. Trebuie sa fie >= 1. Example: 150
     * @bodyParam day_of_month integer optional Noua zi a lunii pentru debitare (intre 1 si 28): Example: 10
     * @bodyParam status string optional Statusul nou: ACTIVE, PAUSED, CANCELED. Example: PAUSED
     */
    public function update(Request $request, $id)
    {
        $user = auth('api')->user();

        $subscription = Subscription::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:1',
            'day_of_month' => 'nullable|integer|between:1,28',
            'status' => 'nullable|in:ACTIVE,PAUSED,CANCELED',
        ]);

        $updates = [];

        if(isset($validated['amount'])) 
        {
            $updates['amount'] = $validated['amount'];
        }

        if(isset($validated['day_of_month'])) 
        {
            $day = (int) $validated['day_of_month'];
            $today = now();

            if($today->day > $day)
            {
                $startDate = $today->copy()->addMonthNoOverflow()->day($day);
            } else {
                $startDate = $today->copy()->day($day);
            }

            $nextChargeDate = $startDate->copy()->addMonthNoOverflow();

            $updates['day_of_month'] = $day;
            $updates['next_charge_date'] = $nextChargeDate->toDateString();
        }

        if(isset($validated['status']))
        {
            $updates['status'] = $validated['status'];
        }

        $subscription->update($updates);

        return response()->json([
            'message' => 'Abonament actualizat cu succes.',
            'subscription' => $subscription->fresh(),
        ]);
    }

    /**
     * @group Abonamente
     * 
     * Reporneste abonamentul marcat anterior ca 'PAUSED' sau 'CANCELED'
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul abonamentului care trebuie reactivat. Example: 2
     */
    public function resume($id)
    {
        $user = auth('api')->user();

        $subscription = Subscription::where('user_id', $user->id)->findOrFail($id);

        if($subscription->status === 'ACTIVE')
        {
            return response()->json([
                'message' => 'Abonamentul este deja activ.',
            ], 200);
        }

        // Resetam next_charge_date in functie de ziua reactivarii
        $day = $subscription->day_of_month;
        $today = now();

        if($today->day > $day)
        {
            $startDate = $today->copy()->addMonthNoOverflow()->day($day);
        } else {
            $startDate = $today->copy()->day($day);
        }

        $nextChargeDate = $startDate->copy()->addMonthNoOverflow();

        $subscription->update([
            'status' => 'ACTIVE',
            'next_charge_date' => $nextChargeDate->toDateString(),
        ]);

        return response()->json([
            'message' => 'Abonamentul a fost reactivat cu succes.',
            'subscription' => $subscription->fresh(),
        ]);
    }
}
