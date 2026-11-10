TITULO: Framework de Testes de Integração HTTP In-Process para Rotas e Controllers com PHPUnit

STATUS: PLANEJADO
PRIORIDADE: MÁXIMA / URGENTE
DEPENDÊNCIA: IOEDU-0003, IOEDU-0013

> [!IMPORTANT]
> **Esclarecimento de Escopo sobre a analogia ao "Supertest":**
> A menção ao `supertest` (ecossistema Node.js) é uma **referência exclusivamente conceitual** para caracterizar testes de integração executados em memória (*in-process*), sem dependência de servidor web externo ativo (`nginx`, `php -S` ou requisições de rede via cURL).
> **A sintaxe e o design da API de teste NÃO precisam seguir ou emular o padrão fluente encadeado do `supertest`**.
> A implementação deve adotar a abordagem que for mais idiomática, robusta e limpa para o ecossistema PHPUnit + CodeIgniter 3 (por exemplo, um método direto `$this->request('POST', 'admin/cursos', $params, $session, $headers)` ou um harness de teste de requisições equivalente), desde que inicialize o CI3 e valide a pilha completa da rota.

---

### DESCRIÇÃO:
Conceber e implementar a infraestrutura oficial de testes de integração HTTP da plataforma io.edu utilizando PHPUnit e execução in-process (sem necessidade de servidor web externo ativo ou subida do CI3 via HTTP).
Além da construção da base, este ticket possui como **prioridade mandatória a cobertura integral de 100% das rotas existentes no projeto**, garantindo que nenhuma rota permaneça sem testes de integração completos.

---

### 1. OBJETIVOS E PRIORIDADES PRINCIPAIS:

1. **Construção do Harness Base de Teste In-Process**:
   - Inicializar o kernel do CodeIgniter 3 em memória diretamente no PHPUnit.
   - Permitir despachar requisições simuladas para as rotas com dados de sessão, cabeçalhos (`X-App-Json`), parâmetros `POST`/`GET` e payloads JSON.
   - Garantir rollback transacional automático a cada teste para isolamento absoluto de banco.
2. **Cobertura Integral de Todas as Rotas do Projeto (Prioridade Máxima)**:
   - Ao concluir a infraestrutura base, cobrir **100% das rotas da aplicação** que ainda não possuem testes de integração.
   - **Regra de Portão de Qualidade (Quality Gate 1:1 Obrigatório)**: Toda rota registrada em `application/config/routes.php` deve possuir testes de integração correspondentes.
   - **Caminho Feliz + Todas as Possíveis Exceções**: Cada rota DEVE conter:
     - **1 teste para o Caminho Feliz** (execução bem-sucedida com credenciais, parâmetros e fluxo esperado).
     - **Vários testes cobrindo todas as exceções e variações de falha configuradas para a rota** (ex: requisição não autenticada com redirecionamento, usuário sem permissão RBAC retornando `403 Forbidden`, falhas de validação de formulário retornando `422`, registro inexistente retornando `404 Not Found`, conflitos semânticos retornando `409 Conflict`, etc.).
     - Isso blinda a aplicação contra regressões estruturais na pilha completa (Router $\rightarrow$ Hooks $\rightarrow$ Middleware $\rightarrow$ Controller $\rightarrow$ Use Case $\rightarrow$ Repositório $\rightarrow$ MY_Controller Exception Handler).

---

### 2. REGRA DE PORTÃO DE QUALIDADE (QUALITY GATE DETERMINÍSTICO):

Para assegurar conformidade estrita e impedir que novas rotas sejam adicionadas sem testes de integração, será implementado um script guardião automatizado:
- **Script**: `.agents/scripts/verify-route-integration-tests.php`
- **Comportamento**:
  - Lê todas as rotas ativas em `application/config/routes.php`.
  - Verifica se existe classe e métodos de teste de integração correspondentes sob `tests/integration/`.
  - Verifica se tanto o cenário de sucesso quanto os cenários de barramento (não autenticado, permissão negada, validação inválida) estão cobertos.
  - Integrado ao validador geral `.agents/scripts/check-conventions.sh`, falhando caso alguma rota esteja desprovida de teste.

---

### 3. ARQUITETURA DA SOLUÇÃO (IN-PROCESS HTTP RUNNER):

```text
Teste de Integração (PHPUnit TestCase)
       │
       ▼
Harness / Cliente de Teste HTTP (In-Process)
 ├── Define Sessão (ex: user_id, user_role para validação RBAC)
 ├── Define Headers (ex: X-App-Json, X-Requested-With)
 └── Dispara Requisição (GET, POST, PUT, DELETE) para a URI da rota
       │
       ▼ (Configuração In-Memory: $_SERVER, $_POST, php://input, CI_URI, CI_Router)
CodeIgniter 3 Application Kernel / Front Controller In-Memory
 ├── Dispara Hooks (Language_check, Middleware RBAC)
 ├── Resolve Rota e Despacha para Controller -> Action (MY_Controller)
 └── Captura Buffer de Saída (CI_Output, Headers e HTTP Status Code)
       │
       ▼
Resultado da Resposta (Response Wrapper ou Array de Saída)
 ├── Verificação de Status Code (200, 201, 401, 403, 404, 422)
 ├── Verificação de Payload JSON ou View renderizada
 └── Verificação de Redirecionamento ou Mutação de Sessão
```

#### 3.1 Diretrizes de Implementação:

1. **`IntegrationTestCase` (`tests/integration/IntegrationTestCase.php`)**:
   - Classe base para todos os testes de integração sob `tests/integration/`.
   - Inicializa a instância do CodeIgniter (`get_instance()`) de forma limpa antes de cada teste.
   - Fornece métodos utilitários claros para disparo de requisição (seja via método único `$this->request($method, $uri, $params, $session, $headers)` ou via helpers dedicados `$this->get(...)`, `$this->post(...)`).
   - Gerencia rollback automático de transação de banco de dados (`$this->db->trans_begin()` no `setUp()` e `$this->db->trans_rollback()` no `tearDown()`) para garantir isolamento e idempotência absoluta entre testes.

2. **Simulação Completa do Contexto HTTP**:
   - Capacidade de injetar variáveis de sessão (`session->userdata`) para simular usuários autenticados com perfis específicos (`admin-master`, `student`, etc.).
   - Suporte ao envio de dados via `$_POST`, `FormData` ou JSON raw (`php://input`).
   - Envio dos headers padrão do projeto (`X-App-Json: application/json` e `X-Requested-With: XMLHttpRequest`).

---

### 4. PLANO DE AÇÃO E IMPLEMENTAÇÃO:

- [ ] **Etapa 1: Harness Base `IntegrationTestCase`**:
  - Implementar o carregamento in-process do CI3 sem dependência de servidor web externo.
  - Implementar rollback transacional de banco de dados ao final de cada teste.
  - Implementar injeção de sessão e captura de headers/buffers de resposta.
- [ ] **Etapa 2: Script Guardião de Portão de Rotas**:
  - Criar `.agents/scripts/verify-route-integration-tests.php` e plugar no `.agents/scripts/check-conventions.sh`.
- [ ] **Etapa 3: Cobertura Completa de Todas as Rotas do Projeto**:
  - **Autenticação**:
    - `POST /autenticacao/login` (caminho feliz, credenciais inválidas, usuário sem perfil, campos em branco).
    - `GET /autenticacao/sair` (destruição de sessão e redirecionamento).
  - **Categorias e Cursos**:
    - Listagens, paginações e filtros (sucesso e barramento RBAC).
    - Criação e Edição (sucesso com persistência atômica, falhas de validação 422, duplicidade de slug).
    - Arquivamento e Exclusão (sucesso, entidade inexistente 404, conflitos).
  - **Estrutura Curricular e Blocos**:
    - Módulos, Aulas e Blocos (caminho feliz de criação, reordenação e deleção; parâmetros inválidos).
    - Endpoints multipart de storage (assinatura, listagem e conclusão).
  - **Módulo Financeiro e Comercial**:
    - Centros de Custo (CRUD com happy path e exceções de duplicidade).
    - Configurações de Pagamento (CRUD com happy path, validação de provedores e credenciais).
  - **Relatórios e Dashboards**:
    - Acesso autorizado vs. bloqueio de permissão (403).

---

### 5. CRITÉRIOS DE ACEITE:

- [ ] Suíte de testes de integração executável diretamente via `docker compose exec app vendor/bin/phpunit tests/integration/`.
- [ ] Nenhuma dependência de servidor HTTP externo (`nginx`, `php -S`, Apache ou cURL de rede) para executar os testes.
- [ ] 100% das rotas declaradas no sistema cobertas por testes de integração 1:1.
- [ ] Cada rota testada possui 1 teste de caminho feliz e múltiplos testes para cada exceção/barramento configurado (sem sessão, sem permissão RBAC, validação de payload inválida, 404, etc.).
- [ ] Script `.agents/scripts/verify-route-integration-tests.php` integrado ao portão determinístico atestando conformidade total.
- [ ] Isolamento de banco de dados garantido por transação/rollback automático em cada execução de teste.
- [ ] 100% de conformidade com as regras sintáticas e convenções do `GEMINI.md`.
