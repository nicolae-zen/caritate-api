<?php

namespace App\Http\Controllers\Admin\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;

class DonationController extends Controller
{
    /**
     * @group Donatii (Admin)
     * 
     * Obtinem lista tuturor donatiilor
     * 
     * @authenticated
     */
    public function index()
    {
        $donations = Donation::with(['user', 'cause'])
                            ->orderByDesc('created_at')
                            ->get();

        if($donations->isEmpty())
        {
            return response()->json([
                'message' => 'Nu s-a inregistrat nici o donatie.',
            ]);
        }

        return response()->json([
            'message' => 'Lista tuturor donatiilor.',
            'donations' => $donations,
        ]);
    }
}