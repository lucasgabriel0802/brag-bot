<?php

namespace App\Http\Controllers;

use App\Models\Brag;
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

    public function generate(Request $request)
    {
        $request->validate([
            'raw_brag' => 'required|string',
        ]);

        // Simula o tempo de processamento de uma IA
        sleep(1.5);

        // Processamento mockado
        $brag = Brag::create([
            'title' => 'Implementação de IA na Pós-Graduação UNIPDS',
            'context' => 'Desenvolvimento de uma ferramenta interna para destilar conquistas utilizando técnicas avançadas de Processamento de Linguagem Natural (NLP) na arquitetura do Brag-Bot.',
            'impact' => 'Redução de 40% no tempo gasto pela equipe na documentação de conquistas e alinhamento com a identidade visual da UNIPDS.',
            'technologies' => ['Laravel 11', 'Vue 3', 'Inertia.js', 'Tailwind CSS v4'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Conquista destilada com sucesso!');
    }
}
