<?php

namespace App\Http\Controllers;

use App\Models\Brag;
use App\Rules\SafeBragPrompt;
use App\Services\GenkitBragService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BragController extends Controller
{
    public function index()
    {
        $brags = Brag::latest()->get();

        return Inertia::render('Dashboard', [
            'brags' => $brags,
        ]);
    }

    public function show($id)
    {
        $brag = Brag::findOrFail($id);

        return Inertia::render('Detail', [
            'brag' => $brag,
        ]);
    }

    public function generate(Request $request, GenkitBragService $genkitService)
    {
        $request->validate([
            'raw_brag' => ['required', 'string', new SafeBragPrompt],
        ]);

        try {
            $rawBrag = $request->input('raw_brag');
            $generated = $genkitService->generate($rawBrag);

            Brag::create([
                'title' => $generated['title'],
                'context' => $generated['context'],
                'impact' => $generated['impact'],
                'technologies' => $generated['technologies'],
            ]);

            return redirect()->route('dashboard')->with('success', 'Conquista destilada com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Falha ao destilar conquista: '.$e->getMessage());
        }
    }
}
