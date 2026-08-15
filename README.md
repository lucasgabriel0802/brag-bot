# 🚀 Brag Bot

**Brag Bot** é uma aplicação web focada em ajudar desenvolvedores e profissionais de tecnologia a documentarem e valorizarem suas conquistas e entregas profissionais (os famosos *"brags"*). 

Utilizando o ecossistema do **Google Genkit** com os modelos do **Google Gemini**, a aplicação converte rascunhos informais em relatórios executivos altamente estruturados (*Brag Documents*), prontos para planos de carreira, avaliações de desempenho (1:1) e atualizações de IDP.

---

## 🤖 Arquitetura e Destaque da Integração com IA

A inteligência artificial do Brag Bot foi desenhada seguindo as melhores práticas de **Engenharia de Prompt**, **Segurança com Guardrails** e **Design de Arquitetura de IA**:

```
[ Usuário ] ──► [ Frontend Vue 3 ] ──► [ Laravel 12 API ] ──► [ SafeBragPrompt Validation ]
                                                                       │
                                                                       ▼
                                                          [ GenkitBragService (PHP) ]
                                                                       │ (Subprocesso TSX)
                                                                       ▼
                                                       [ Pre-flight LLM Guardrail ]
                                                                       │
                                                       (Aprovado? isSafe: true)
                                                                       │
                                                                       ▼
                                                          [ bragGeneratorFlow (IA) ]
                                                                       │ (Output Zod Schema)
                                                                       ▼
                                                          [ Persistência & Resposta ]
```

### 🌟 Principais Destaques da IA:

1. **Google Genkit em TypeScript (`src/flows.ts`):**
   - Fluxo orquestrado (`bragGeneratorFlow`) com validação de schemas estritos via **Zod**.
   - Persona especializada de *"Senior Career Consultant"*, focada em comunicação executiva, quantificação de impacto e síntese técnica.

2. **Pre-flight LLM Guardrail (LLM-as-a-Judge):**
   - **Auditoria de Segurança Prévia:** Antes da geração do documento, o input passa por uma avaliação rápida de segurança com a própria LLM.
   - **Defesa em Profundidade:** Identifica e bloqueia tentativas de *Prompt Injection*, *Jailbreak* (ex: "DAN mode", "Ignore previous instructions") e *SQL Injection* semântico ou ofuscado.
   - **Tolerância a Contexto Legítimo:** Desenvolvedores podem descrever livremente termos técnicos normais (ex: *"Otimizei consultas SQL complexas no PostgreSQL"*), sem falsos positivos.

3. **Fidelidade e Preservação de Idioma (Multi-Language):**
   - O fluxo preserva e responde rigorosamente no mesmo idioma em que o usuário redigiu sua conquista (Português, Inglês, Espanhol, etc.), mantendo a terminologia técnica intacta.

4. **Orquestração Direta no Laravel (`GenkitBragService`):**
   - O Laravel invoca o fluxo Genkit sob demanda via subprocesso assíncrono seguro (`Process::run`), aproveitando o runtime Node.js compartilhado no ambiente Docker, sem a sobrecarga de manter servidores HTTP/Express extras abertos.

5. **Painel de Desenvolvimento do Genkit (Developer UI):**
   - Ambiente interativo para inspeção de traces, visualização de latência, testes isolados e depuração de prompts:
     ```bash
     ./vendor/bin/sail npm run genkit:ui
     ```

---

## 🛠 Stack Tecnológico

- **Backend:** Laravel 12 (PHP 8.4) com API REST versionada (`/api/v1/brags`), Eloquent ORM e migrations.
- **Frontend:** Vue 3 + Inertia.js (Single Page Application) com Tailwind CSS v4 e notificações no topo com **Sonner**.
- **Inteligência Artificial:** Google Genkit + Google Gemini API (`gemini-3-flash-preview`) com Zod Schemas e LLM Guardrails.
- **Ambiente de Desenvolvimento:** Laravel Sail (Docker Compose com contêineres para App, MySQL e Redis).

---

## 🚀 Como Iniciar o Projeto (Setup Local)

### Pré-requisitos
- [Docker](https://www.docker.com/) e Docker Compose instalados na máquina.

### Passo a passo

1. **Clonar o Repositório**
   ```bash
   git clone https://github.com/lucasgabriel0802/brag-bot.git
   cd brag-bot
   ```

2. **Configuração de Variáveis de Ambiente**
   Copie o arquivo de exemplo:
   ```bash
   cp .env.example .env
   ```
   Abra o arquivo `.env` e configure sua chave de API do Google Gemini:
   ```env
   GOOGLE_API_KEY=sua_chave_do_gemini_aqui
   ```

3. **Instalação das dependências do Composer**
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. **Iniciar o ambiente Sail (Docker)**
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Gerar chave da aplicação e rodar as Migrations**
   ```bash
   ./vendor/bin/sail php artisan key:generate
   ./vendor/bin/sail php artisan migrate
   ```

6. **Instalar dependências do Frontend & Genkit**
   ```bash
   ./vendor/bin/sail npm install
   ```

---

## ⚙️ Executando a Aplicação

Para o desenvolvimento diário, inicie o compilador de assets do Vite:
```bash
./vendor/bin/sail npm run dev
```
Acesse a aplicação no navegador em: **`http://localhost`**

### 🤖 Acessar o Genkit Developer UI
Para inspecionar os fluxos de IA e traces:
```bash
./vendor/bin/sail npm run genkit:ui
```
Acesse a interface do Genkit em: **`http://localhost:4000`**

---

## 📡 Endpoints da API REST (V1)

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/api/v1/brags` | Lista todas as conquistas salvas em JSON |
| `GET` | `/api/v1/brags/{id}` | Retorna os detalhes de uma conquista específica |
| `POST` | `/api/v1/brags` | Processa o texto na IA Genkit e persiste a conquista |
| `POST` | `/api/brag` | Endpoint de compatibilidade para clientes diretos |

**Exemplo de Payload (`POST /api/v1/brags`):**
```json
{
  "definition": "Migrei o banco de dados para PostgreSQL e aumentei a velocidade de resposta em 40%."
}
```

---

## 🧪 Qualidade de Código e Testes

- **Executar a suíte de testes automatizados (PHPUnit/Feature Tests):**
  ```bash
  ./vendor/bin/sail php artisan test
  ```
- **Formatação de código com Laravel Pint:**
  ```bash
  ./vendor/bin/sail pint
  ```
- **Compilação de produção dos assets do frontend:**
  ```bash
  ./vendor/bin/sail npm run build
  ```

---

## 📂 Fluxo de Contribuição e Gitflow

O projeto adota rigorosamente o fluxo do **Gitflow**:
- Novas funcionalidades e manutenções partem da branch `develop` (`git flow feature start <nome>`).
- Commits seguem o padrão **Conventional Commits** (`feat:`, `fix:`, `docs:`, `test:`, etc.).

---

**Brag Bot** — Valorizando e estruturando as conquistas dos desenvolvedores com Inteligência Artificial.
