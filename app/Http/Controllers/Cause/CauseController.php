<?php

namespace App\Http\Controllers\Cause;

use App\Http\Controllers\Controller;
use App\Models\Cause;

class CauseController extends Controller
{
    /**
     * @group Cauze
     * 
     * Obtine lista cauzelor active (public)
     * 
     */
    public function index()
    {
        $causes = Cause::where('is_active', 1)->orderByDesc('created_at')->get();

        return response()->json([
            'message' => 'Lista cauzelor active.',
            'causes' => $causes,
        ]);
    }

    /**
     * @group Cauze
     * 
     * Obtine detalii despre o cauza anume (public)
     * 
     * @urlParam id integer required ID-ul cauzei. Example: 2
     */
    public function show($id)
    {
        $cause = Cause::where('is_active', 1)
            ->where('id', $id)
            ->first();

        if(!$cause)
        {
            return response()->json([
                'message' => 'Cauza nu a fost gasita sau este inactiva.',
            ], 404);
        }
        
        return response()->json([
            'message' => 'Detalii despre cauza solicitata.',
            'cause' => $cause,
        ]);
    }
}
