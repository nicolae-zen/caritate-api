<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * @group Rapoarte financiare (Admin)
     * 
     * Obtinem lista tuturor rapoartelor (Publicate/Nepublicate)
     * 
     * @authenticated
     */
    public function index()
    {
        $reports = Report::orderByDesc('created_at')->get();

        return response()->json([
            'message' => 'Lista tuturor rapoartelor.',
            'causes' => $reports,
        ]);
    }

    /**
     * @group Rapoarte financiare (Admin)
     * 
     * Creaza un raport financiar
     * 
     * @authenticated
     * 
     * @bodyParam title string required Titlul raportului. Example: Raport trimestrial Q2
     * @bodyParam description string optional Descrierea raportului. Example: Situatie financiara Q2.
     * @bodyParam file file required Fisierul PDF care contine raportul.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $request->file('file')->store('reports', 'public');

        $report = Report::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'is_published' => false,
            'published_at' => null,
        ]);

        return response()->json([
            'message' => 'Raport creat cu succes.',
            'raport' => $report, 
        ], 201);
    } 

    /**
     * @group Rapoarte financiare (Admin)
     * 
     * Publica un raport financiar | 'is_published' = true
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul raportului care trebuie publicat. Example: 4
     */
    public function publish($id)
    {
        $report = Report::findOrFail($id);
        
        if($report->is_published)
        {
            return response()->json([
                'message' => 'Raportul este deja publicat.',
            ], 400);
        }

        $report->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json([
            'messasge' => 'Raportul a fost publicat cu succes.',
            'report' => $report,
        ]);
    }

    /**
     * @group Rapoarte financiare (Admin)
     * 
     * Revoca publicarea unui raport financiar | 'is_published' = false | 'published_at' = null
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul raportului care trebuie revocat. Example: 4
     */
    public function unpublish($id)
    {
        $report = Report::findOrFail($id);

        if(!$report->is_published)
        {
            return response()->json([
                'message' => 'Raportul nu este publicat.',
            ], 400);
        }

        $report->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        return response()->json([
            'message' => 'Publicarea raportului a fost revocata',
            'raport' => $report,
        ]);
    }

    /**
     * @group Rapoarte financiare (Admin)
     * 
     * Editeaza un raport financiar existent
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul raportului care trebuie actualizat. Example: 1
     * 
     * @bodyParam title string optional Titlul nou al raportului. Example: Raport revizuit Q2
     * @bodyParam description string optional Descriere noua a raportului. Example: Varianta actualizata
     * @bodyParam file file optional Fisier PDF nou pentru raport. Must be a .pfg file.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Precatim datele pentru update
        $data = [];

        if($request->filled('title'))
        {
            $data['title'] = $validated['title'];
        }

        if($request->filled('description'))
        {
            $data['description'] = $validated['description'];
        }

        if($request->hasFile('file'))
        {
            // Stergem fisierul vechi daca este nevoie
            if($report->file_path && Storage::disk('public')->exists($report->file_path))
            {
                Storage::disk('public')->delete($report->file_path);
            }

            $data['file_path'] = $request->file('file')->store('reports', 'public');
        }

        $report->update($data);

        return response()->json([
            'message' => 'Raportul a fost actualizat cu succes.',
            'raport' => $report->fresh(),
        ]);
    }

    /**
     * @group Rapoarte financiare (Admin)
     * 
     * Sterge definitiv raportul financiar
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID-ul raportului care trebuie sters. Example: 1
     */
    public function destroy($id)
    {
        $report = Report::find($id);

        if(!$report)
        {
            return response()->json([
                'message' => 'Raportul nu a fost gasit.',
            ], 404);
        }

        $report->delete();

        return response()->json([
            'message' => 'Raportul a fost sters definitiv.',
        ]);
    }
}
