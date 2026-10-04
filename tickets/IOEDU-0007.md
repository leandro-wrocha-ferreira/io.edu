TITULO: Implementar Subagente Auditor para Validação Estrita das Execuções do Code-Implementer
STATUS: CONCLUÍDO

DESCRIÇÃO:
Criar e integrar um subagente auditor especializado para atuar como revisor obrigatório e minucioso de cada execução realizada pelo agente `code-implementer` (`.agents/workflows/code-implementer.md`). O subagente deve operar com temperatura 0.1 e nível máximo de critério sobre as regras arquiteturais, convenções de código e diretrizes de skills do projeto (`GEMINI.md` / `AGENTS.md`), validando de forma 100% estrita os arquivos alterados antes de qualquer finalização, sem necessidade de executar testes automatizados (foco exclusivo em conformidade estática e arquitetural).

1. Criação do Workflow do Subagente Auditor:
- Criar o arquivo `.agents/workflows/implementation-auditor.md` (ou definir a especificação do subagente revisor) com:
  - `mode: subagent` (ou workflow dedicado acionável).
  - `temperature: 0.1` (determinístico, rigoroso, tolerância zero a desvios).
  - Permissões estritamente de leitura (`edit: deny`, comandos bash limitados a `git-leandro status`, `git-leandro diff`, `git-leandro log`).
  - Proibição estrita de editar código: o subagente é 100% auditor e nunca altera arquivos diretamente.

2. Checklist de Auditoria Minuciosa (100% dos Pontos Obrigatórios):
- **Formatação e Padrões de Código (`GEMINI.md` / `AGENTS.md`)**:
  - [ ] **Indentação**: Uso exclusivo de TABS (espaços para indentação são estritamente proibidos).
  - [ ] **Line Endings & Charset**: LF e UTF-8.
  - [ ] **PSR-12**: Chaves de abertura `{` obrigatoriamente na linha seguinte para classes e métodos.
  - [ ] **Alinhamento Vertical Proibido**: Nenhum alinhamento em coluna para `=>` ou `=`. Apenas um espaço antes e depois de operadores.
  - [ ] **Nenhuma Variável de Uma Letra**: Proibição de variáveis como `$i`, `$k`, `$v`, `$u`. Variáveis devem ser descritivas (ex: `$index`, `$user`, `$key`).
  - [ ] **Docblocks Obrigatórios**: Todas as classes, métodos e parâmetros devem conter docblocks em inglês (exceto migrations).
- **Fronteiras Arquiteturais DDD-Lite**:
  - [ ] **Domain Layer**: Classes puras PHP sem acoplamento ao CI3 (`no get_instance()`, sem herança de CI_Model).
  - [ ] **Application Layer (Use Cases)**: Orquestram regras de negócio, recebem interfaces de repositório via construtor (resolvidos por `Model_factory`), sem dependência de models concretos.
  - [ ] **Infrastructure Layer (Models)**: Extendem `MY_Model`, implementam interfaces do domínio, utilizam estritamente Database DTOs e Mappers para tradução de entidades, sem manipulação manual de `created_at`/`updated_at`.
  - [ ] **Presentation Layer (Controllers)**: Extendem `MY_Controller`, zero regras de negócio, zero chamadas diretas a models (apenas Use Cases), validação de formulário tratada diretamente na ação com ponto único de carregamento de view ao final (`no _handle_*()`).
  - [ ] **Proibição Estrita de Bypasses**: Nenhuma violação de camadas ou consulta direta para contornar falhas de fluxos anteriores (ex: sessão ausente).
- **Conformidade com Skills Ativas**:
  - [ ] `ci3-ui`: Paleta centralizada, suporte a Light/Dark mode, classes de animação de entrada (`animate-fade-up`, `animate-delay-1`).
  - [ ] `ci3-js`: Requisições assíncronas utilizando exclusivamente `public/assets/js/http.js` com `X-App-Json: application/json` e CSRF integrado.
  - [ ] `ci3-controller`, `ci3-domain`, `ci3-model`: Conformidade com as respectivas skills.

3. Integração ao Workflow `code-implementer.md`:
- Atualizar `.agents/workflows/code-implementer.md` para incluir a etapa formal de validação:
  - Antes de concluir qualquer tarefa ou repassar para commit, o `code-implementer` DEVE acionar o subagente auditor para validar o diff completo da implementação (`git-leandro diff`).
  - Se o auditor apontar qualquer desconformidade, o implementador deve corrigir os apontamentos e submeter novamente até obter aprovação total (100% clean).

4. Formato do Relatório de Auditoria:
- Se aprovado: Declaração explícita de conformidade total com todos os critérios do `GEMINI.md`.
- Se reprovado: Lista objetiva e estruturada apontando o arquivo, número da linha e o padrão violado para correção imediata pelo implementador.

CRITÉRIOS DE ACEITE:
- Arquivo do subagente auditor criado e documentado com diretrizes 100% minuciosas.
- Configuração com `temperature: 0.1` e permissão estritamente somente-leitura.
- Workflow `code-implementer.md` atualizado com o passo mandatório de acionamento do subagente auditor.
- Checklist cobre 100% das regras sintáticas, estilísticas e arquiteturais do `GEMINI.md`.
- Nenhuma execução de testes requerida por este subagente (foco exclusivo em auditoria de código e padrões).
