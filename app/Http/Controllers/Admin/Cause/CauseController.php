<?php

namespace App\Http\Controllers\Admin\Cause;

use App\Http\Controllers\Controller;
use App\Models\Cause;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class CauseController extends Controller
{
    /**
     * @group Cauze (Admin)
     * 
     * Obtine lista tuturor cauzelor (Adtive/Inactive)
     * 
     * @authenticated
     */
    public function index()
    {
        $causes = Cause::orderByDesc('created_at')->get();

        return response()->json([
            'message' => 'Lista tuturor cauzelor.',
            'causes' => $causes,
        ]);
    }

    /**
     * @group Cauze (Admin)
     * 
     * Creaza o cauza noua.
     * 
     * @authenticated
     * 
     * @bodyParam title string required Titlu cauzei. Example: Ajuta animalele fara adapost
     * @bodyParam description stirng optional Descrierea detaliata. Example: Strangem fonduri pentru hrana si adaposturi.
     * @bodyParam image string optional URL imagine. Example: uploads/animale.jpg
     * @bodyParam goal_amount decimal optional Suma necesara. Example: 5000.00
     * @bodyParam is_active boolean optional Implicit true. Example: 1
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max: 255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'goal_amount' => 'nullable|numeric|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $cause = Cause::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
            'goal_amount' => $validated['goal_amount'] ?? null,
            'is_active' => $validated['is_active'] ?? 1,
        ]);

        return response()->json([
            'messasge' => 'Cauza creata cu succes.',
            'cause' => $cause,
        ], 201);
    }

    /**
     * @group Cauze (Admin)
     * 
     * Editeaza o cauza existenta.
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul cauzei care trebuie editata. Example: 3
     * 
     * @bodyParam title string optional Titlul cauzei
     * @bodyParam description string optional Descriere detaliata
     * @bodyParam image string optional URL imagine
     * @bodyParam goal_amount decimal optional Suma necesara
     * @bodyParam is_active boolean optional Activa (1) sau inactiva (0)
     */
    public function update(Request $request, $id)
    {
        $cause = Cause::find($id);

        if(!$cause)
        {
            return response()->json([
                'message' => 'Cauza nu a fost gasita.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'goal_amount' => 'nullable|numeric|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $updates = [];

        foreach (['title', 'description', 'image', 'goal_amount', 'is_active'] as $field) 
        {
            if($request->filled($field))
            {
                $updates[$field] = $validated[$field];
            }
        }

        $cause->update($updates);

        return response()->json([
            'message' => 'Cauza actualizata cu succes.',
            'cause' => $cause->fresh(),
        ]);
    }

    /**
     * @group Cauze (Admin)
     * 
     * Dezactiveaza o cauza selectata
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul cauzei care trebuie dezactivata. Example: 5
     */
    public function disable($id)
    {
        $cause = Cause::find($id);

        if(!$cause)
        {
            return response()->json([
                'message' => 'Cauza nu a fost gasita.',
            ], 404);
        }

        $cause->update(['is_active' => false]);

        return response()->json([
            'message' => 'Cauza a fost dezactivata cu succes.',
        ]);
    }

    /**
     * @group Cauze (Admin)
     * 
     * Activeaza o cazua inactiva (reactiveaza cauza)
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul cauzei care trebuie reactivata. Example: 1
     */
    public function activate($id)
    {
        $cause = Cause::where('id', $id)
            ->where('is_active', false)
            ->first();

        if(!$cause)
        {
            return response()->json([
                'message' => 'Cauza nu a fost gasita sau este deja activa.',
            ], 494);
        }

        $cause->update(['is_active' => true]);

        return response()->json([
            'message' => 'Cauza a fost activata cu succes.',
            'cause' => $cause,
        ]);
    }

    /**
     * @group Cauze (Admin)
     * 
     * Sterge definitiv cauza din Database
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul cauzei care trebuie stearsa. Example: 1
     */
    public function destroy($id)
    {
        $cause = Cause::find($id);

        if(!$cause)
        {
            return response()->json([
                'message' => 'Cauza nu a fost gasita.',
            ], 404);
        }

        $cause->delete();

        return response()->json([
            'message' => 'Cauza a fost stearsa definitiv.',
        ]);
    }
}
