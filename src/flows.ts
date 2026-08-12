import 'dotenv/config';
import { genkit, z } from 'genkit';
import { googleAI } from '@genkit-ai/google-genai';
import { v4 as uuidv4 } from 'uuid';

export const ai = genkit({
  plugins: [googleAI({ apiKey: process.env.GOOGLE_API_KEY })],
  model: googleAI.model('gemini-3.5-flash')
});

export const BragInputSchema = z.object({
  definition: z.string().describe('Rascunho informal do usuário'),
});

export const BragSchema = z.object({
  title: z.string().describe('Ação principal + Resultado de alto nível'),
  context: z.string().describe('Situação/Problema original. O que estava quebrado, lento, etc.'),
  actionTaken: z.string().describe('Ação técnica ou estratégica passo a passo tomada para resolver o problema'),
  businessImpact: z.string().describe('Qual o impacto de negócio. Tempo ganho, redução de falhas, etc.'),
  metrics: z.array(z.string()).describe('Apenas dados estritamente quantificáveis. Ex: "50% reduction"'),
  technologiesUsed: z.array(z.string()).describe('Ferramentas, linguagens e plataformas mencionadas ou inferidas'),
});

export const BragFinalSchema = BragSchema.extend({
  id: z.string().describe('ID gerado nativamente pela aplicação (UUID)'),
});

export const bragGeneratorFlow = ai.defineFlow(
  {
    name: 'bragGeneratorFlow',
    inputSchema: BragInputSchema,
    outputSchema: BragFinalSchema,
  },
  async (input) => {
    const prompt = `Persona: O modelo deve atuar como um "Senior Career Consultant" focado em Planos de Desenvolvimento Individual (IDP) para Engenheiros de Software.

Objetivo: Transformar o rascunho informal do usuário em um "Brag Document" executivo.

Regra 1: Usar tom profissional, objetivo e focado em impacto, sem adjetivos emocionais.
Regra 2: Se não existirem métricas exatas, a IA deve inferir a natureza da métrica baseada na ação tomada.
Regra 3: Seguir ESTRITAMENTE o formato do schema JSON.
Regra 4: O output deve respeitar a linguagem original do input (se mandou em português, responde em português).

Input do usuário:
${input.definition}`;

    const response = await ai.generate({
      // model: googleAI.model('gemini-flash-latest'),
      prompt,
      config: { temperature: 0.8 },
      output: { schema: BragSchema },
    });

    if (!response.output) {
      throw new Error('Falha ao gerar conteúdo: A resposta não contém um output válido.');
    }

    return {
      ...response.output,
      id: uuidv4(),
    };
  }
);
