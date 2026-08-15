<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class GenkitBragService
{
    /**
     * Gera um Brag estruturado a partir de uma definição informal via Genkit AI.
     *
     * @return array{title: string, context: string, impact: string, technologies: array<string>, actionTaken?: string, metrics?: array<string>}
     *
     * @throws RuntimeException
     */
    public function generate(string $definition): array
    {
        $definition = trim($definition);

        if (empty($definition)) {
            throw new RuntimeException('A descrição da conquista não pode estar vazia.');
        }

        $this->assertSafeInput($definition);

        $result = $this->callCliRunner($definition);

        return $this->formatResult($result);
    }

    /**
     * Valida o input contra padrões de injeção de prompt e comandos SQL maliciosos.
     *
     * @throws RuntimeException
     */
    protected function assertSafeInput(string $input): void
    {
        $promptInjectionPatterns = [
            '/(ignore|disregard|forget|bypass|override)\s+(all\s+)?(previous|prior|above|system)\s+(instructions|prompts|rules|commands)/i',
            '/(ignore\s+todas\s+as\s+(instruções|regras|comandos)\s+(anteriores|do\s+sistema))/i',
            '/(esqueca|esqueça)\s+(todas\s+as\s+)?(instruções|regras)\s+(anteriores|do\s+sistema)/i',
            '/(você agora é|you are now|pretend you are|act as)\s+(dan|an evil|jailbreak|unrestricted|godmode)/i',
            '/(reveal|show|print|output|display)\s+(the\s+)?(system\s+prompt|initial\s+prompt|secret\s+key|api\s+key)/i',
            '/(revelar|mostrar|exibir|imprimir)\s+(o\s+prompt\s+do\s+sistema|as\s+instruções\s+iniciais|a\s+chave\s+de\s+api)/i',
            '/system\s*:\s*["\']?you\s+are/i',
        ];

        $sqlInjectionPatterns = [
            '/(\b(or|and)\b\s+[\'"]?\d+[\'"]?\s*=\s*[\'"]?\d+[\'"]?\s*(--|#|\/\*))/i',
            '/[\'"]\s*;\s*(drop|truncate|delete|alter|grant|revoke|exec|execute)\b/i',
            '/\b(drop\s+table|drop\s+database|truncate\s+table)\b/i',
            '/\b(union\s+(all\s+)?select\b)/i',
            '/\b(information_schema|sys\.tables|pg_catalog)\b/i',
            '/\b(exec(\s+sp_|\s+xp_)|xp_cmdshell)\b/i',
            '/\b(into\s+outfile|into\s+dumpfile)\b/i',
            '/;\s*--/i',
            '/--\s*$/m',
        ];

        foreach ($promptInjectionPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                throw new RuntimeException('O texto contém padrões suspeitos de injeção de prompt ou manipulação de instruções de IA.');
            }
        }

        foreach ($sqlInjectionPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                throw new RuntimeException('O texto contém comandos ou sintaxes SQL suspeitas que violam as políticas de segurança.');
            }
        }
    }

    /**
     * Executa o runner CLI em TypeScript via Process do Laravel.
     *
     * @throws RuntimeException
     */
    protected function callCliRunner(string $definition): array
    {
        $process = Process::path(base_path())
            ->timeout(60)
            ->run([
                'node',
                '--import',
                'tsx/esm',
                'src/cli.ts',
                $definition,
            ]);

        if (! $process->successful()) {
            $errorOutput = $process->errorOutput() ?: $process->output();
            throw new RuntimeException('Falha ao processar IA no Genkit: '.$errorOutput);
        }

        $output = trim($process->output());
        $data = json_decode($output, true);

        if (! is_array($data) || isset($data['error'])) {
            $errorMsg = is_array($data) && isset($data['error']) ? $data['error'] : 'Resposta inválida do fluxo de IA.';
            throw new RuntimeException($errorMsg);
        }

        return $data;
    }

    /**
     * Formata os campos retornados pela IA para adequação ao modelo Brag.
     */
    protected function formatResult(array $data): array
    {
        $title = $data['title'] ?? 'Conquista Profissional';
        $context = $data['context'] ?? '';

        $actionTaken = $data['actionTaken'] ?? null;
        $businessImpact = $data['businessImpact'] ?? ($data['impact'] ?? '');
        $metrics = $data['metrics'] ?? [];
        $technologies = $data['technologiesUsed'] ?? ($data['technologies'] ?? []);

        // Constrói o texto consolidado de impacto e métricas
        $impactParts = [];
        if (! empty($businessImpact)) {
            $impactParts[] = $businessImpact;
        }
        if (! empty($metrics) && is_array($metrics)) {
            $impactParts[] = '• '.implode("\n• ", $metrics);
        }

        $impact = ! empty($impactParts) ? implode("\n\n", $impactParts) : ($businessImpact ?: 'Impacto relevante gerado com sucesso.');

        return [
            'title' => $title,
            'context' => $context,
            'impact' => $impact,
            'technologies' => is_array($technologies) ? array_values($technologies) : [],
            'actionTaken' => $actionTaken,
            'metrics' => $metrics,
        ];
    }
}
