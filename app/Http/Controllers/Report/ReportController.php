<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * @group Rapoarte financiare
     * 
     * Returneaza lista rapoartelor financiare publicate
     */
    public function index()
    {
        $reports = Report::where('is_published', 1)
            ->orderByDesc('published_at')
            ->get(['id', 'title', 'published_at']);

        if($reports->isEmpty())
        {
            return response()->json([
                'message' => 'La moment nu exista nici un raport publicat.',
            ]);
        }

        return response()->json([
            'message' => 'Lista rapoartelor financiare publicate.',
            'reports' => $reports,
        ]);
    }

    /**
     * @group Rapoarte financiare
     * 
     * Afiseaza detalii despre un raport financiar publicat.
     * 
     * @urlParam id integer required ID-ul raportului. Example: 2
     */
    public function show($id)
    {
        $report = Report::where('id', $id)
            ->where('is_published', 1)
            ->first();

        if(!$report)
        {
            return response()->json([
                'message' => 'Raportul nu a fost gasit sau nu este publicat.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detalii raport financiar.',
            'raport' => $report,
        ]);
    }
}
