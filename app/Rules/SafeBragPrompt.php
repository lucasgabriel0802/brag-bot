<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SafeBragPrompt implements ValidationRule
{
    /**
     * Padrões de injeção de prompt e jailbreak.
     */
    protected array $promptInjectionPatterns = [
        '/(ignore|disregard|forget|bypass|override)\s+(all\s+)?(previous|prior|above|system)\s+(instructions|prompts|rules|commands)/i',
        '/(ignore\s+todas\s+as\s+(instruções|regras|comandos)\s+(anteriores|do\s+sistema))/i',
        '/(esqueca|esqueça)\s+(todas\s+as\s+)?(instruções|regras)\s+(anteriores|do\s+sistema)/i',
        '/(você agora é|you are now|pretend you are|act as)\s+(dan|an evil|jailbreak|unrestricted|godmode)/i',
        '/(reveal|show|print|output|display)\s+(the\s+)?(system\s+prompt|initial\s+prompt|secret\s+key|api\s+key)/i',
        '/(revelar|mostrar|exibir|imprimir)\s+(o\s+prompt\s+do\s+sistema|as\s+instruções\s+iniciais|a\s+chave\s+de\s+api)/i',
        '/system\s*:\s*["\']?you\s+are/i',
    ];

    /**
     * Padrões de injeção de SQL e comandos destrutivos diretos.
     */
    protected array $sqlInjectionPatterns = [
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

    /**
     * Valida o valor informado contra padrões de prompt injection e comandos SQL maliciosos.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        foreach ($this->promptInjectionPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail('O texto fornecido contém padrões suspeitos de injeção de prompt ou manipulação de instruções de IA.');

                return;
            }
        }

        foreach ($this->sqlInjectionPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail('O texto fornecido contém comandos ou sintaxes SQL suspeitas que violam as políticas de segurança.');

                return;
            }
        }
    }
}
