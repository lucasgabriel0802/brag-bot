<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brag;
use App\Rules\SafeBragPrompt;
use App\Services\GenkitBragService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BragApiController extends Controller
{
    /**
     * Lista todas as conquistas cadastradas.
     */
    public function index(): JsonResponse
    {
        $brags = Brag::latest()->get();

        return response()->json([
            'data' => $brags,
        ]);
    }

    /**
     * Exibe os detalhes de uma conquista específica.
     *
     * @param  int|string  $id
     */
    public function show($id): JsonResponse
    {
        $brag = Brag::find($id);

        if (! $brag) {
            return response()->json([
                'message' => 'Conquista não encontrada.',
            ], 404);
        }

        return response()->json([
            'data' => $brag,
        ]);
    }

    /**
     * Recebe a definição informal, processa no Genkit e persiste no banco de dados.
     */
    public function store(Request $request, GenkitBragService $genkitService): JsonResponse
    {
        $validated = $request->validate([
            'definition' => ['nullable', 'string', new SafeBragPrompt],
            'raw_brag' => ['nullable', 'string', new SafeBragPrompt],
        ]);

        $definition = $validated['definition'] ?? ($validated['raw_brag'] ?? null);

        if (empty($definition) || ! is_string($definition)) {
            return response()->json([
                'message' => 'O campo definition (ou raw_brag) é obrigatório e deve ser uma string.',
                'errors' => [
                    'definition' => ['O campo definition é obrigatório.'],
                ],
            ], 422);
        }

        try {
            $generated = $genkitService->generate($definition);

            $brag = Brag::create([
                'title' => $generated['title'],
                'context' => $generated['context'],
                'impact' => $generated['impact'],
                'technologies' => $generated['technologies'],
            ]);

            return response()->json([
                'message' => 'Conquista gerada e salva com sucesso!',
                'data' => $brag,
                'ai_output' => $generated,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar a geração de conquista com IA.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
