<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    /**
     * @group Abonamente (Admin)
     * 
     * Obtinem lista tuturor abonamentelor
     * 
     * @authenticated
     */
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'cause', 'lastPayment'])
                            ->orderByDesc('created_at')
                            ->get();

        if($subscriptions->isEmpty())
        {
            return response()->json([
                'message' => 'Nu exista nici un abonament.',
            ]);
        }

        return response()->json([
            'message' => 'Lista tuturor abonamentelor.',
            'donations' => $subscriptions,
        ]);
    }  
    /**
     * @group Abonamente (Admin)
     * 
     * Dezactivarea unui anumit abonament
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul abonamentului care trebuie dezactivat. Example: 2
     */ 
    public function cancel($id)
    {
        $subscription = Subscription::find($id);

        if(!$subscription)
        {
            return response()->json([
                'messasge' => 'Abonamentul nu a fost gasit.',
            ], 404);
        }

        if($subscription->status === 'CANCELED')
        {
            return response()->json([
                'message' => 'Abonamentul este deja anulat.',
            ], 409);
        }

        $subscription->status = 'CANCELED';
        $subscription->save();

        return response()->json([
            'message' => 'Abonamentul a fost anulat cu succes.',
            'subscription' => $subscription->fresh(),
        ]);
    }
}