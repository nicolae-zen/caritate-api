<?php

namespace App\Http\Controllers\Achievement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Achievement;

class AchievementController extends Controller
{
    /**
     * @group Achievements (Realizari)
     * 
     * Returneaza lista tuturor realizarilor posibile din platforma.
     */

    public function index()
    {
        $achievements = Achievement::orderBy('created_at', 'asc')->get();

        if($achievements->isEmpty())
        {
            return response()->json([
                'message' => 'La moment nu exista realizari disponibile',
            ]);
        }

        return response()->json([
            'message' => 'Lista realizarilor disponibile.',
            'achievements' => $achievements,
        ]);
    }

    /**
     * @group Achievements (Realizari)
     * 
     * Returneaza lista realizarilor deblocate de utilizatorul autentificat
     * 
     * @authenticated
     */

     public function userAchievements()
     {
        /** @var \App\Models\User $user */
         $user = auth('api')->user();

         $achievements = $user->achievements()
            ->orderBy('pivot_unlocked_at', 'desc')
            ->get()
            ->map(function ($achievement) {
                return [
                    'id' => $achievement->id,
                    'title' => $achievement->title,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'unlocked_at' => $achievement->pivot->unlocked_at,
                ];
            });
        
        if($achievements->isEmpty())
        {
            return response()->json([
                'message' => 'Nu aveti nici o realizare obtinuta.',
            ]);
        };

        return response()->json([
            'message' => 'Lista realizarilor obtinute.',
            'achievements' => $achievements
        ]);
     }
}
