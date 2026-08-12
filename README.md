# 🚀 Brag Bot

**Brag Bot** é uma aplicação web focada em ajudar desenvolvedores e profissionais a manterem um registro contínuo de suas conquistas, atividades e entregas, os famosos "brags". Ao utilizar inteligência artificial (com Google Gemini via Genkit), o sistema converte rascunhos informais e fragmentados em relatórios estruturados e profissionais, os chamados *Brag Documents*.

## 🛠 Stack Tecnológico

A aplicação adota uma arquitetura full-stack moderna dividida em:

- **Backend:** Laravel 12, fornecendo uma API consistente, modelagem de banco de dados robusta e a base para a infraestrutura.
- **Frontend:** Vue 3 + Inertia.js, combinando a facilidade de um desenvolvimento SPA (Single Page Application) com o roteamento nativo do Laravel. A interface é estilizada utilizando Tailwind CSS e integra o componente de notificação Sonner.
- **Inteligência Artificial:** O fluxo de geração e enriquecimento de documentos utiliza o **Google Genkit** em TypeScript, se comunicando de forma integrada à infraestrutura (atualmente utilizando os modelos da API do Google Gemini).
- **Ambiente de Desenvolvimento:** Laravel Sail (Docker), facilitando o encapsulamento do ecossistema e mantendo um padrão uniforme entre devs.

## 🚀 Como iniciar o projeto (Setup Local)

### Pré-requisitos
- Docker e Docker Compose instalados.
- Opcional: Composer e PHP instalados localmente, embora seja recomendado fazer tudo via [Laravel Sail](https://laravel.com/docs/sail).

### Passo a passo

1. **Clonar o Repositório**
   ```bash
   git clone https://github.com/lucasgabriel0802/brag-bot.git
   cd brag-bot
   ```

2. **Configuração de Variáveis de Ambiente**
   Copie o arquivo base de configuração:
   ```bash
   cp .env.example .env
   ```
   **Importante:** Adicione as chaves necessárias, especialmente sua chave de acesso do Google AI para o Genkit:
   ```env
   GEMINI_API_KEY=sua_chave_aqui
   ```

3. **Instalação das dependências e Build inicial**
   Usaremos um contêiner pequeno temporário para instalar as dependências do Composer sem precisar do PHP na sua máquina:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. **Iniciando o ambiente (Sail)**
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Gerar chave da aplicação e rodar migrations**
   ```bash
   ./vendor/bin/sail php artisan key:generate
   ./vendor/bin/sail php artisan migrate
   ```

6. **Instalar dependências Frontend**
   ```bash
   ./vendor/bin/sail npm install
   ```

## ⚙️ Executando a Aplicação

Para o desenvolvimento diário, é necessário deixar o frontend compilando seus assets em modo "watch":
```bash
./vendor/bin/sail npm run dev
```
Você pode acessar o site através do navegador em `http://localhost`.

### 🤖 Painel do Genkit
Caso precise testar os fluxos de IA de forma isolada, disponibilizamos um comando nativo para abrir o Genkit Developer UI:
```bash
./vendor/bin/sail npm run genkit:ui
```

## 📂 Padrões do Projeto e Contribuição

- O projeto segue o fluxo do **Gitflow**. Toda nova `feature/` parte da branch `develop`.
- Utilizamos o **Laravel Pint** para a padronização de código do backend:
  ```bash
  ./vendor/bin/sail pint
  ```
- O padrão de escrita para commits é o **Conventional Commits** (`feat:`, `fix:`, `chore:`, etc), preferencialmente em português do Brasil.

---
**Brag Bot** - Facilitando a criação do seu *Brag Document* anual.
