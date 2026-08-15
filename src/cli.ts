import 'dotenv/config';
import { bragGeneratorFlow } from './flows.js';

async function main() {
  try {
    let inputStr = process.argv[2];

    if (!inputStr) {
      const chunks: Buffer[] = [];
      for await (const chunk of process.stdin) {
        chunks.push(chunk);
      }
      inputStr = Buffer.concat(chunks).toString('utf8');
    }

    if (!inputStr || !inputStr.trim()) {
      console.error(JSON.stringify({ error: 'Nenhum input fornecido para o fluxo.' }));
      process.exit(1);
    }

    let definition = inputStr.trim();
    try {
      const parsed = JSON.parse(definition);
      if (parsed && typeof parsed.definition === 'string') {
        definition = parsed.definition;
      } else if (parsed && typeof parsed.raw_brag === 'string') {
        definition = parsed.raw_brag;
      }
    } catch {
      // Input não é JSON, utiliza string direta
    }

    const result = await bragGeneratorFlow({ definition });
    console.log(JSON.stringify(result));
    process.exit(0);
  } catch (error: any) {
    console.error(JSON.stringify({
      error: error.message || 'Erro desconhecido ao executar o fluxo Genkit.',
    }));
    process.exit(1);
  }
}

main();
