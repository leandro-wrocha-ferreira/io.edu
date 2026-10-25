TITULO: Catálogo Comercial de Produtos, Condições de Venda, Versionamento de Preços e Estrutura Financeira

STATUS: PLANEJADO
DEPENDÊNCIA: IOEDU-0011, IOEDU-0012

DESCRIÇÃO:
Reestruturar e delimitar as fronteiras arquiteturais entre o Domínio Educacional, o Domínio Comercial de Produtos e o Domínio Financeiro da plataforma io.edu.
Esta especificação introduz a entidade comercial `Product`, que estabelece uma relação 1:1 obrigatória e atômica com a entidade educacional `Course`. O `Product` encapsula a identidade comercial estável do que é vendido, seu preço em centavos inteiros, modelo de cobrança (compra avulsa/parcelamento ou assinatura recorrente), restrições de meios de pagamento e vínculos financeiros.
A disponibilidade comercial e o ciclo de vida pertencem exclusivamente ao `Course` (`status` pedagógico e flag `is_available_for_sale`); o `Product` não possui status próprio.
A criação de Curso e Produto ocorre de forma estritamente atômica em transação única de banco de dados, impedindo a existência de cursos órfãos sem produto, mesmo em rascunho.
Adiciona-se o mecanismo imutável de Versionamento Comercial por Alteração de Preço (`product_price_versions`), preservando um snapshot contextual desnormalizado integral a cada reajuste efetivo de valor, sob controle estrito de concorrência com bloqueio pessimista (`FOR UPDATE`).
O Domínio Financeiro centraliza Provedores (`PaymentProvider`), Credenciais com criptografia reversível autenticada em repouso AES-256-GCM (`PaymentCredential`), Contas Recebedoras (`PaymentAccount`) e Perfis de Pagamento (`PaymentConfiguration`).
O Centro de Custo (`CostCenter`) é totalmente desacoplado da configuração de pagamento, atuando como classificação gerencial independente informada diretamente no Produto quando aplicável, sem herança silenciosa ou seeds de vinculação automática.
A modalidade pedagógica (online, presencial, híbrido) permanece completamente fora do escopo.
O ciclo transacional de compras, checkout, pedidos, transações financeiras, conciliação e webhooks permanece formalmente delimitado para a demanda futura IOEDU-0017.

---

### 1. SEPARAÇÃO CONCEITUAL & ARQUITETURA DE DOMÍNIOS:

```text
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. DOMÍNIO EDUCACIONAL (app\domain\course\)                                     │
│                                                                                 │
│ Course (courses):                                                               │
│  - Identidade Pedagógica: title, slug, category_id, workload, content tree      │
│  - Vigência Pedagógica de Matrícula: access_period_type, access_days            │
│  - Ciclo de Vida: status [draft, active, inactive, archived]                    │
│  - Disponibilidade Comercial: is_available_for_sale (0 ou 1)                    │
│  (NÃO contém preço, modelo de cobrança, credenciais nem dados de gateway)       │
└────────────────────────────────────────┬────────────────────────────────────────┘
                                         │ Vínculo 1:1 Mandatório Criado Atomicamente
                                         │ (products.course_id UNIQUE NOT NULL -> courses.id)
                                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 2. DOMÍNIO COMERCIAL (app\domain\commercial\)                                   │
│                                                                                 │
│ Product (products):                                                             │
│  - Identidade Comercial Estável vinculada a exatamente um Course                │
│  - price_in_cents (INT UNSIGNED: 0 = Gratuito, > 0 = Pago) & currency ('BRL')   │
│  - billing_model: [one_time, subscription]                                      │
│  - max_installments: 1 a 24 parcelas (apenas se one_time; 1 = à vista)          │
│  - subscription_interval: [monthly, quarterly, semiannual, yearly]              │
│  - max_billing_cycles: ciclos de cobrança recorrente (NULL = ilimitado)         │
│  - payment_methods: JSON ['pix', 'credit_card', 'boleto']                       │
│  - payment_configuration_id: FK obrigatória para Produto Pago em venda         │
│  - cost_center_id: FK direta para classificação gerencial (opcional/explícita)  │
│  (PRODUTO NÃO TEM STATUS PRÓPRIO; a venda depende do estado do Course)          │
└────────────────────────────────────────┬────────────────────────────────────────┘
                                         │ Snapshot em cada alteração efetiva de preço
                                         │ (1:N imutável ordenado por version_number)
                                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 2.1 HISTÓRICO COMERCIAL IMUTÁVEL (app\domain\commercial\)                       │
│                                                                                 │
│ ProductPriceVersion (product_price_versions):                                   │
│  - product_id: FK para products(id)                                             │
│  - version_number: sequencial estrito (1, 2, 3...)                              │
│  - previous_price_in_cents: preço anterior em centavos (NULL na versão inicial) │
│  - new_price_in_cents: novo preço fixado em centavos                            │
│  - currency: moeda ('BRL')                                                      │
│  - snapshot: JSON com contexto comercial desnormalizado integral                │
│    (termos de venda, títulos, dados do perfil financeiro mascarado e centro)    │
│  - change_reason: justificativa da alteração informada pelo operador            │
│  - created_by: ID do usuário autenticado responsável                            │
│  - created_at: carimbo imutável da alteração                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
                                         │ Referência de Perfil Financeiro
                                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 3. DOMÍNIO FINANCEIRO (app\domain\finance\)                                     │
│                                                                                 │
│ PaymentProvider (ex: Asaas ativo; Pagar.me e Mercado Pago inativos/planejados)  │
│   └── PaymentCredential (API Key e segredos cifrados em repouso AES-256-GCM)    │
│         └── PaymentAccount (Conta do recebedor por ambiente: sandbox/production)│
│               └── PaymentConfiguration (Perfil consolidado selecionável)       │
│                                                                                 │
│ CostCenter (Classificação gerencial independente - SEM vínculo com gateway)     │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

### 2. CONCEITOS E REGRAS DE NEGÓCIO:

#### 2.1 Domínio Educacional (`Course`):
- A entidade `Course` concentra a identidade pedagógica, ementa, categorização, carga horária e a árvore de aprendizagem (módulos, aulas e blocos de conteúdo).
- **Remoção Total da Modalidade Pedagógica**: O campo `modality` (`online`, `presencial`, `hibrido`), o Value Object `CourseModality` e quaisquer badges/filtros associados foram completamente removidos desta especificação. A plataforma atende cursos online contínuos, sem modelagem prematura de infraestrutura de turmas ou aulas físicas presenciais.
- A entidade `Course` **NÃO** armazena preço, modelo de cobrança, parcelas, contas recebedoras nem credenciais de adquirentes.

#### 2.2 Domínio Comercial (`Product`):
- O `Product` é a entidade comercial estável que representa o item comercializado na plataforma.
- **Ausência de Status Próprio no Produto**: O `Product` **NÃO** possui coluna `status` nem máquina de estados própria. O ciclo de vida pertence exclusivamente ao `Course` (`status`: `draft`, `active`, `inactive`, `archived`) e à sua flag de elegibilidade comercial (`is_available_for_sale`).
- **Garantia Arquitetural do Vínculo 1:1 Curso-Produto**:
  - A restrição de banco `products.course_id UNIQUE NOT NULL` assegura que um Produto aponte para um único Curso e impede que múltiplos produtos apontem para o mesmo Curso.
  - Para garantir que **nenhum Curso seja persistido sem Produto correspondente**, a aplicação impõe uma criação obrigatoriamente atômica na camada de Use Case: a persistência de um novo `Course` e de seu `Product` (mesmo com parâmetros comerciais iniciais de rascunho) ocorre na mesma transação de banco de dados via `TransactionManagerInterface`. Se a gravação de qualquer uma das entidades falhar, a transação sofre rollback integral.
- **Identidade Estável**:
  - O Produto mantém seu identificador único inalterado mesmo após reajustes de preço ou alterações em opções de parcelamento. Alterar preço nunca cria um novo Produto.
- **Desativação de Vendas vs. Política de Acesso de Compradores**:
  - Desativar ou arquivar um Curso (`status = 'inactive'` ou `status = 'archived'`) força `is_available_for_sale = 0`, impedindo novas vendas no catálogo.
  - Essa desativação **NÃO** exclui o Produto nem revoga o acesso dos alunos matriculados anteriormente. A política de vigência de matrículas e o direito de acesso permanecem desacoplados da disponibilidade de novas vendas.

#### 2.3 Estados e Elegibilidade de Venda:
A tabela a seguir consolida a matriz de regras entre o estado do Curso, disponibilidade para venda e consistência comercial:

| `Course.status` | `Course.is_available_for_sale` | Preço do Produto (`price_in_cents`) | Perfil Financeiro (`payment_configuration_id`) | Elegibilidade / Comportamento |
|---|---|---|---|---|
| `draft` | `0` | `0` (Gratuito) | `NULL` | **Válido (Rascunho):** Edição pedagógica e comercial em andamento. Vendas bloqueadas. |
| `draft` | `0` | `> 0` (Pago) | `NULL` ou Definido | **Válido (Rascunho):** Permite salvar preço antes de vincular conta financeira. Vendas bloqueadas. |
| `draft` | `1` | Qualquer | Qualquer | **Inválido / Rejeitado:** Cursos em rascunho NUNCA podem ter disponibilidade de venda ativa (`is_available_for_sale = 1`). |
| `active` | `0` | Qualquer | Qualquer | **Válido (Publicado sem Venda):** Conteúdo pedagógico publicado, mas novas vendas suspensas temporariamente (ex: vitrine fechada). Alunos existentes acessam normalmente. |
| `active` | `1` | `0` (Gratuito) | `NULL` | **Válido (Venda Ativa Gratuita):** Inscrições gratuitas liberadas no catálogo. Não requer nem permite configuração de pagamento. |
| `active` | `1` | `> 0` (Pago) | `NULL` | **Inválido / Rejeitado:** É estritamente proibido ativar vendas de produto pago sem configuração financeira ativa e válida! |
| `active` | `1` | `> 0` (Pago) | Ativo e Válido | **Válido (Venda Ativa Paga):** Produto liberado para compras no catálogo com recebimento configurado. |
| `inactive` / `archived` | `0` | Qualquer | Qualquer | **Válido:** Curso inativado ou arquivado. Novas vendas totalmente suspensas. Alunos anteriores mantêm acesso conforme prazo de suas matrículas. |
| `inactive` / `archived` | `1` | Qualquer | Qualquer | **Inválido / Rejeitado:** Cursos inativos ou arquivados não podem ser colocados à venda. |

- **Prazos de Acesso Explícitos**:
  - Elimina-se qualquer prazo de acesso implícito (como suposição automática de 365 dias ou 1 ano).
  - Se `access_period_type = 'limited_time'`, o campo `access_days` deve ser explicitamente fornecido como um número inteiro maior que zero (`> 0`). Se `access_period_type = 'lifetime'`, o campo `access_days` deve ser nulo (`NULL`).

#### 2.4 Produto Gratuito vs. Produto Pago & Transições:
- **Produto Gratuito (`price_in_cents = 0`)**:
  - `billing_model = 'one_time'`.
  - `max_installments = 1` (não existe parcelamento de valor zero).
  - `subscription_interval = NULL` e `max_billing_cycles = NULL` (não há ciclos recorrentes em produto gratuito).
  - `payment_methods = []` (array JSON vazio; não há processamento em adquirentes).
  - `payment_configuration_id = NULL` (nenhum perfil financeiro pode ser associado).
- **Produto Pago (`price_in_cents > 0`)**:
  - Exige definição de modelo de cobrança: `one_time` (com `max_installments` entre 1 e 24) OU `subscription` (com `subscription_interval` obrigatório).
  - Exige seleção de ao menos um método de pagamento suportado pela configuração financeira selecionada.
  - Para estar habilitado para novas vendas (`Course.is_available_for_sale = 1`), exige obrigatoriamente `payment_configuration_id` apontando para um perfil ativo.
  - **Proibição de Fallback Silencioso**: Não existe perfil financeiro padrão selecionado automaticamente caso o operador não informe um. A ausência de configuração financeira nunca é tratada como gratuidade.
- **Transições Comerciais**:
  - **Gratuito → Pago**: O operador define `price_in_cents > 0`, modelo de cobrança, parcelas/intervalo, meios de pagamento e configuração financeira (se ativo para venda). Essa alteração efetiva de valor gera atomicamente uma nova versão em `product_price_versions` (`previous_price_in_cents = 0`, `new_price_in_cents = X`).
  - **Pago → Gratuito**: O operador define `price_in_cents = 0`. O sistema anula `payment_configuration_id`, limpa `payment_methods` para `[]`, ajusta `billing_model = 'one_time'` e gera atomicamente uma nova versão em `product_price_versions` (`previous_price_in_cents = X`, `new_price_in_cents = 0`).

#### 2.5 Centro de Custo (`CostCenter` Totalmente Desacoplado):
- O Centro de Custo é uma classificação puramente gerencial e orçamentária por unidade de negócio (ex: Cursos Livres, Certificações Executivas).
- **Desacoplamento Completo da Estrutura Financeira**:
  - O `CostCenter` **NÃO** pertence nem se vincula à `PaymentConfiguration`. A coluna `cost_center_id` foi **completamente removida** da tabela `payment_configurations`.
  - O Centro de Custo é associado **diretamente ao `Product`** (`products.cost_center_id`) de forma explícita pelo operador quando a regra de negócio da instituição exigir segmentação gerencial.
  - Se não for informado, permanece `NULL`. Fica expressamente abolida qualquer regra de "herança ou precedência automática silenciosa".
  - Fica **removido o seed `CC-GERAL`** ou qualquer centro de custo genérico pré-fabricado com o objetivo de auto-vincular produtos.

#### 2.6 Representação Estrita de Valores Monetários:
- É **proibido o uso de tipos de ponto flutuante (`float`)** para representação, cálculo, comparação e persistência de valores monetários.
- Todos os preços são manipulados no Domínio, nos Casos de Uso e no Banco de Dados como **números inteiros representando a unidade mínima da moeda (centavos)** (`price_in_cents INT UNSIGNED`). Exemplo: R$ 199,90 = `19990`.
- O tipo `float` é tolerado **exclusivamente na camada de apresentação visual** para renderização formatada ao usuário.
- O Value Object `Price` encapsula o valor inteiro em centavos (`int $cents`) e a moeda (`string $currency = 'BRL'`). Métodos de conversão de entrada string (ex: `"199,90"`) realizam parsing aritmético inteiro direto sem passar por ponto flutuante.

#### 2.7 Versionamento Comercial por Alteração de Preço:
- O sistema mantém um histórico imutável na tabela `product_price_versions`.
- **Gatilho de Criação**:
  - A primeira definição de preço do produto gera a **Versão 1** (`previous_price_in_cents = NULL`, `new_price_in_cents = valor`).
  - Atualizações posteriores geram uma nova versão **exclusivamente quando houver alteração efetiva no valor monetário** (`new_price_in_cents !== previous_price_in_cents`).
  - Salvar o produto mantendo o mesmo preço **NÃO** gera nova versão histórica.
- **Controle Estrito de Concorrência e Sequenciamento**:
  - Para garantir sequenciamento rigoroso (`version_number = latest + 1`) e impedir condições de corrida ou deadlocks sob requisições concorrentes, a transação de reajuste executa um **bloqueio pessimista de linha** no produto:
    `SELECT id FROM products WHERE id = ? FOR UPDATE;`
  - Com a linha do produto bloqueada na transação, obtém-se o próximo número de versão de forma isolada e sequencial.
  - A restrição de banco `UNIQUE KEY uq_product_version (product_id, version_number)` atua como garantia final de integridade física no motor InnoDB.
- **Snapshot Desnormalizado Contextual**:
  - O campo `snapshot` (JSON) armazena o estado comercial integral vigente no instante do reajuste, preservando contexto suficiente para que a versão seja interpretada mesmo se entidades relacionadas forem alteradas no futuro:
    - `price_in_cents`: int
    - `currency`: string ('BRL')
    - `billing_model`: string ('one_time' | 'subscription')
    - `max_installments`: int (1..24)
    - `subscription_interval`: ?string
    - `max_billing_cycles`: ?int
    - `payment_methods`: array de strings
    - `course_id`: int
    - `course_title`: string (título pedagógico no momento do reajuste)
    - `access_period_type`: string ('lifetime' | 'limited_time')
    - `access_days`: ?int
    - `payment_configuration_id`: ?int
    - `payment_configuration_name`: ?string
    - `payment_provider_slug`: ?string (ex: 'asaas')
    - `payment_account_name`: ?string
    - `payment_account_identifier_masked`: ?string (ex: 'acc_****1234')
    - `cost_center_id`: ?int
    - `cost_center_code`: ?string (ex: 'CC-CURSOS-ONLINE')
    - `cost_center_name`: ?string
  - **Segurança Estrita**: O snapshot **NUNCA** armazena chaves de API, tokens, segredos de webhook ou dados confidenciais de credenciais.
  - **Fronteira Contábil**: A tabela `product_price_versions` é um histórico de condições comerciais de catálogo. Ela **NÃO** substitui livros contábeis, faturas fiscais ou registros transacionais de pedidos e pagamentos.

#### 2.8 Segregação: Versionamento Comercial vs. Auditoria vs. Snapshot de Pedido:
1. **Versionamento Comercial (`product_price_versions`)**:
   - Histórico imutável de catálogo acionado estritamente em reajustes de preço, capturando snapshot contextual das condições de venda.
2. **Logs de Auditoria Sistêmica (`logs` via `MY_Model::log_audit`)**:
   - Mecanismo nativo já implementado na camada de infraestrutura (`MY_Model`), que registra automaticamente mutações genéricas (insert, update, delete) com diff de colunas (`before`/`after`), ID do registro, tabela, usuário autenticado da sessão (`user_id`) e timestamp. Auditoria não substitui a tabela de versões e nem é usada para reconstruir catálogo histórico.
3. **Snapshot de Pedido (Fronteira com IOEDU-0017)**:
   - Contrato comercial da compra fechado no checkout pelo aluno. Pertence ao domínio de compras (`Order`) na demanda futura IOEDU-0017 e congela as condições contratadas pelo comprador independentemente de alterações posteriores no catálogo.

#### 2.9 Domínio Financeiro & Consistência de Provedores:
- **`PaymentProvider`**:
  - Representa o provedor adquirente.
  - O projeto possui integração ativa exclusivamente com o **Asaas**. O seed inicial cadastra apenas o Asaas como ativo (`is_active = 1`). Provedores adicionais (`Pagar.me`, `Mercado Pago`) podem constar cadastrados com `is_active = 0` para fins de metadados, sem sugerir falsamente integrações funcionais inexistentes.
  - `supported_methods`: Lista teórica das modalidades que a API do provedor é capaz de suportar (ex: `["pix", "credit_card", "boleto"]`).
- **`PaymentCredential` (Segurança Estrita)**:
  - Armazena chaves de API e segredos de webhook por ambiente (`sandbox` vs `production`).
  - **Criptografia Reversível Autenticada em Repouso**: Utiliza cifra AES-256-GCM via OpenSSL com chave de ambiente `PAYMENT_ENCRYPTION_KEY` mantida estritamente isolada do banco.
  - Proibição absoluta de exposição de segredos em DTOs, telas (exibir apenas máscara `sk_live_****1234`), logs ou exceptions.
  - Seeds e fixtures de teste usam apenas strings simuladas, jamais segredos de produção.
- **`PaymentAccount`**:
  - Representa a conta/subconta recebedora no gateway.
  - **Integridade Referencial entre Provedor e Credencial**: Uma conta não pode referenciar credenciais de um provedor diferente. Essa restrição é garantida no próprio banco de dados através de chave composta `(provider_id, credential_id)` referenciando `payment_credentials(provider_id, id)`.
  - O ambiente da conta deve corresponder estritamente ao ambiente da credencial (`sandbox` com `sandbox`, `production` com `production`).
- **`PaymentConfiguration`**:
  - Perfil operacional de recebimento que conecta um nome comercial à `PaymentAccount` e define os `allowed_methods`.
  - Os `allowed_methods` da configuração devem ser um subconjunto válido dos métodos ativos na conta e no provedor.

#### 2.10 Experiência Administrativa Unificada:
- O formulário administrativo de cursos (`/admin/courses/create` e `/admin/courses/edit`) proporciona uma gestão fluida sem quebrar a separação de domínios.
- Os Controllers atuam apenas como receptores HTTP e orquestradores de apresentação, delegando 100% da lógica e das validações de negócio para Use Cases especializados.
- A tela apresenta seções bem delimitadas:
  1. Identificação Pedagógica (Título, Categoria, Resumo, Carga Horária, Imagem, Ementa, Período de Acesso explícito).
  2. Condições Comerciais e Financeiras (Preço em centavos com máscara monetária, Switch Gratuito/Pago, Modelo de Cobrança, Parcelas/Intervalo, Meios de Pagamento, Perfil Financeiro e Centro de Custo).
  3. Tabela Histórica de Reajustes (na edição): Consulta somente leitura de `product_price_versions`, exibindo Data, Versão, Preço Anterior, Novo Preço, Usuário e Justificativa. Sem ações de edição ou exclusão manual.

---

### 3. MODELAGEM DO BANCO DE DADOS & INTEGRIDADE REFERENCIAL:

#### 3.1 Alteração na Tabela de Cursos (`courses`):
- Migration: `20261018000001_add_is_available_for_sale_to_courses.php`
- Campos adicionados:
  - `is_available_for_sale`: TINYINT(1) NOT NULL DEFAULT 0 (após `status`)
- Índices:
  - `KEY idx_courses_sales_eligibility (status, is_available_for_sale)`
- *Nota*: A tabela `courses` continua livre de preços, dados bancários ou regras de cobrança.

#### 3.2 Tabelas do Domínio Financeiro:
- Migration: `20261018000002_create_financial_domain_tables.php`
- Tabelas criadas:
  1. `payment_providers`:
     - `id`: INT UNSIGNED AUTO_INCREMENT, PK
     - `name`: VARCHAR(100) NOT NULL
     - `slug`: VARCHAR(50) NOT NULL UNIQUE
     - `supported_methods`: JSON NOT NULL
     - `is_active`: TINYINT(1) NOT NULL DEFAULT 1
     - `created_at`, `updated_at`: DATETIME NOT NULL
  2. `payment_credentials`:
     - `id`: INT UNSIGNED AUTO_INCREMENT, PK
     - `provider_id`: INT UNSIGNED NOT NULL
     - `name`: VARCHAR(150) NOT NULL
     - `environment`: ENUM('sandbox', 'production') NOT NULL DEFAULT 'sandbox'
     - `api_key_encrypted`: TEXT NOT NULL
     - `webhook_secret_encrypted`: TEXT NULL DEFAULT NULL
     - `is_active`: TINYINT(1) NOT NULL DEFAULT 1
     - `created_at`, `updated_at`: DATETIME NOT NULL
     - `UNIQUE KEY uq_cred_provider_id (provider_id, id)`
     - `KEY idx_credentials_provider_env (provider_id, environment, is_active)`
  3. `payment_accounts`:
     - `id`: INT UNSIGNED AUTO_INCREMENT, PK
     - `provider_id`: INT UNSIGNED NOT NULL
     - `credential_id`: INT UNSIGNED NOT NULL
     - `name`: VARCHAR(150) NOT NULL
     - `environment`: ENUM('sandbox', 'production') NOT NULL DEFAULT 'sandbox'
     - `account_identifier`: VARCHAR(150) NOT NULL
     - `document_number`: VARCHAR(30) NULL DEFAULT NULL
     - `status`: ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'active'
     - `created_at`, `updated_at`: DATETIME NOT NULL
     - `KEY idx_accounts_provider (provider_id)`
     - `KEY idx_accounts_credential (credential_id)`
     - `KEY idx_accounts_status (status)`
  4. `cost_centers`:
     - `id`: INT UNSIGNED AUTO_INCREMENT, PK
     - `name`: VARCHAR(150) NOT NULL
     - `code`: VARCHAR(50) NOT NULL UNIQUE
     - `description`: TEXT NULL DEFAULT NULL
     - `is_active`: TINYINT(1) NOT NULL DEFAULT 1
     - `created_at`, `updated_at`, `deleted_at`: DATETIME NULL
  5. `payment_configurations`:
     - `id`: INT UNSIGNED AUTO_INCREMENT, PK
     - `name`: VARCHAR(150) NOT NULL
     - `payment_account_id`: INT UNSIGNED NOT NULL
     - `allowed_methods`: JSON NOT NULL
     - `description`: TEXT NULL DEFAULT NULL
     - `is_active`: TINYINT(1) NOT NULL DEFAULT 1
     - `created_at`, `updated_at`, `deleted_at`: DATETIME NULL
     - *Nota*: `cost_center_id` foi expressamente removido desta tabela.

#### 3.3 Tabela de Produtos Comerciais (`products`):
- Migration: `20261018000003_create_products.php`
- Campos:
  - `id`: INT UNSIGNED AUTO_INCREMENT, PK
  - `course_id`: INT UNSIGNED NOT NULL, UNIQUE (FK para `courses.id`)
  - `price_in_cents`: INT UNSIGNED NOT NULL DEFAULT 0
  - `currency`: VARCHAR(3) NOT NULL DEFAULT 'BRL'
  - `billing_model`: ENUM('one_time', 'subscription') NOT NULL DEFAULT 'one_time'
  - `max_installments`: TINYINT UNSIGNED NOT NULL DEFAULT 1
  - `subscription_interval`: ENUM('monthly', 'quarterly', 'semiannual', 'yearly') NULL DEFAULT NULL
  - `max_billing_cycles`: SMALLINT UNSIGNED NULL DEFAULT NULL
  - `payment_methods`: JSON NOT NULL
  - `payment_configuration_id`: INT UNSIGNED NULL DEFAULT NULL (FK para `payment_configurations.id`)
  - `cost_center_id`: INT UNSIGNED NULL DEFAULT NULL (FK para `cost_centers.id`)
  - `created_at`, `updated_at`, `deleted_at`: DATETIME NULL
- Índices:
  - `UNIQUE KEY uq_products_course_id (course_id)`
  - `KEY idx_products_payment_configuration (payment_configuration_id)`
  - `KEY idx_products_cost_center (cost_center_id)`

#### 3.4 Tabela de Versões de Preço (`product_price_versions`):
- Migration: `20261018000004_create_product_price_versions.php`
- Campos:
  - `id`: INT UNSIGNED AUTO_INCREMENT, PK
  - `product_id`: INT UNSIGNED NOT NULL (FK para `products.id`)
  - `version_number`: INT UNSIGNED NOT NULL
  - `previous_price_in_cents`: INT UNSIGNED NULL DEFAULT NULL
  - `new_price_in_cents`: INT UNSIGNED NOT NULL
  - `currency`: VARCHAR(3) NOT NULL DEFAULT 'BRL'
  - `snapshot`: JSON NOT NULL
  - `change_reason`: VARCHAR(255) NULL DEFAULT NULL
  - `created_by`: INT UNSIGNED NULL DEFAULT NULL (FK para `users.id`)
  - `created_at`: DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- Índices:
  - `UNIQUE KEY uq_product_version (product_id, version_number)`
  - `KEY idx_product_versions_created_at (created_at)`
  - `KEY idx_product_versions_created_by (created_by)`

#### 3.5 Foreign Keys & Integridade Referencial:
- Migration: `20261018000005_add_commercial_and_financial_foreign_keys.php`
  - FK `fk_prod_course` -> `products.course_id` references `courses(id)` ON DELETE RESTRICT ON UPDATE CASCADE.
  - FK `fk_prod_payment_config` -> `products.payment_configuration_id` references `payment_configurations(id)` ON DELETE RESTRICT ON UPDATE CASCADE.
  - FK `fk_prod_cost_center` -> `products.cost_center_id` references `cost_centers(id)` ON DELETE SET NULL ON UPDATE CASCADE.
  - FK `fk_ver_product` -> `product_price_versions.product_id` references `products(id)` ON DELETE RESTRICT ON UPDATE CASCADE.
  - FK `fk_ver_user` -> `product_price_versions.created_by` references `users(id)` ON DELETE SET NULL ON UPDATE CASCADE.
  - FK Composta `fk_pay_acc_provider_credential` -> `payment_accounts(provider_id, credential_id)` references `payment_credentials(provider_id, id)` ON DELETE RESTRICT.
  - FK `fk_pay_cfg_account` -> `payment_configurations.payment_account_id` references `payment_accounts(id)` ON DELETE RESTRICT.

#### 3.6 Seeds Iniciais:
- Migration: `20261018000006_seed_initial_financial_providers.php`
  - Seed do provedor ativo `Asaas` (`slug = 'asaas'`, `is_active = 1`, `supported_methods = ["pix", "credit_card", "boleto"]`).
  - Seed de provedores futuros `Pagar.me` e `Mercado Pago` com `is_active = 0` (inativos).
  - Sem seed de centro de custo genérico (`CC-GERAL` removido).
  - Sem seed de configuração de pagamento padrão para auto-vinculação.

#### 3.7 Esclarecimento sobre Migrations Anteriores:
- As migrations mencionadas na proposta preliminar do card anterior (`20261015000001` a `20261015000005`) eram apenas propostas conceituais em rascunho de ticket e **nunca existiram fisicamente no repositório** (a última migration real executada no projeto é a `20261012000002_change_min_reading_time_to_minutes.php`).
- As novas migrations seguem estritamente a sequência cronológica a partir de `20261018000001`.

---

### 4. ESCOPO EXPLICITAMENTE EXCLUÍDO:

Ficam formalmente excluídos da implementação desta demanda:
- Modalidade pedagógica (online, presencial ou híbrido) e gestão de salas físicas.
- Turmas (`classes`) e Bancos de Questões (`question_banks`).
- Checkout público de compras e carrinho de cursos.
- Tabela de pedidos (`orders`), itens de pedido (`order_items`) e snapshot contratual de compras (IOEDU-0017).
- Processamento transacional de pagamentos (`payments`), cobranças e faturas.
- Execução de chamadas transacionais de gateway e endpoints de webhooks.
- Parcelas como registros transacionais individuais de banco.
- Assinaturas recorrentes processadas ativamente junto a adquirentes.
- Conciliação bancária, split de pagamentos e liquidação financeira.
- Matrículas automáticas originadas de confirmação de pagamento.
- Campanhas promocionais, cupons de desconto e regras sazonais de precificação.
- Mecanismo genérico de restauração arbitrária de versões históricas de preços.
- Implementação de adaptadores reais para adquirentes que não sejam o Asaas.

---

### 5. PADRÕES DE IMPLEMENTAÇÃO (DDD-LITE):

#### 5.1 Domain Layer:

##### Subdomínio Educacional (`app\domain\course\`):
- `Course.php`:
  - Entidade pura preservada com seus atributos pedagógicos e a nova propriedade de disponibilidade `private bool $is_available_for_sale;`.
  - Métodos de negócio: `publish()`, `archive()`, `enable_for_sale()`, `disable_for_sale()`.
  - Invariante de domínio: `enable_for_sale()` só pode ser executado se `status->is_active()`.

##### Subdomínio Comercial (`app\domain\commercial\`):
- **Entidade `Product`** (`application/domain/commercial/Product.php`):
  - Propriedades encapsuladas:
    - `private ?int $id;`
    - `private int $course_id;`
    - `private Price $price;`
    - `private BillingModel $billing_model;`
    - `private InstallmentLimit $max_installments;`
    - `private ?SubscriptionInterval $subscription_interval;`
    - `private ?int $max_billing_cycles;`
    - `private EnabledPaymentMethods $payment_methods;`
    - `private ?int $payment_configuration_id;`
    - `private ?int $cost_center_id;`
  - *Nota*: A entidade `Product` **NÃO** possui propriedade `$status` nem `$is_available_for_sale`.
  - Métodos de fábrica e validações semânticas:
    - `create(...)`: Fábrica unificada.
    - `update_conditions(...)`: Atualiza condições garantindo invariantes (ex: produto gratuito limpa configuração de pagamento e parcelas; produto pago exige dados consistentes).
    - `is_free(): bool`: Retorna `$this->price->is_zero()`.
    - `validate_sales_eligibility(): void`: Lança `ValidationException` se o produto for pago e não possuir configuração financeira válida e ativa.
- **Entidade `ProductPriceVersion`** (`application/domain/commercial/ProductPriceVersion.php`):
  - Entidade imutável de leitura histórica:
    - `private ?int $id;`
    - `private int $product_id;`
    - `private int $version_number;`
    - `private ?Price $previous_price;`
    - `private Price $new_price;`
    - `private array $snapshot;`
    - `private ?string $change_reason;`
    - `private ?int $created_by;`
    - `private \DateTimeImmutable $created_at;`
- **Value Objects** (`app\domain\commercial\value_objects\`):
  - `Price.php`:
    - Propriedades: `private int $cents;` e `private string $currency;`.
    - Construtor privado validando `$cents >= 0`.
    - Métodos: `from_cents(int $cents, string $currency = 'BRL'): self`, `from_string(string $formatted, string $currency = 'BRL'): self` (parsing inteiro determinístico sem conversão float intermediária), `get_cents(): int`, `is_zero(): bool`, `equals(Price $other): bool`, `to_float_display(): float` (exclusivo para view helpers), `format(): string`.
  - `BillingModel.php`: Encapsula `one_time` e `subscription`.
  - `InstallmentLimit.php`: Valida parcelas entre 1 e 24 (obrigatório 1 se à vista).
  - `SubscriptionInterval.php`: Valida intervalos (`monthly`, `quarterly`, `semiannual`, `yearly`).
  - `EnabledPaymentMethods.php`: Encapsula array de métodos (`pix`, `credit_card`, `boleto`), validando unicidade.
- **Interfaces de Repositório** (`app\domain\commercial\repositories\`):
  - `ProductRepositoryInterface.php`:
    - `find_by_id(int $id): ?Product;`
    - `find_by_course_id(int $course_id): ?Product;`
    - `find_by_id_for_update(int $id): ?Product;` (executa row-level lock pessimista `FOR UPDATE`)
    - `save(Product $product): Product;`
  - `ProductPriceVersionRepositoryInterface.php`:
    - `get_next_version_number(int $product_id): int;`
    - `find_latest_by_product_id(int $product_id): ?ProductPriceVersion;`
    - `list_by_product_id(int $product_id): array;`
    - `save(ProductPriceVersion $version): ProductPriceVersion;` (apenas inserção; sem métodos de update ou delete)

##### Subdomínio Financeiro (`app\domain\finance\`):
- **Entidades**:
  - `PaymentProvider.php`: Provedor adquirente (`id`, `name`, `slug`, `supported_methods`, `is_active`).
  - `PaymentCredential.php`: Credenciais de API (`id`, `provider_id`, `name`, `environment`, `api_key_encrypted`, `webhook_secret_encrypted`, `is_active`).
  - `PaymentAccount.php`: Conta recebedora (`id`, `provider_id`, `credential_id`, `name`, `environment`, `account_identifier`, `document_number`, `status`).
  - `CostCenter.php`: Unidade gerencial (`id`, `name`, `code`, `description`, `is_active`).
  - `PaymentConfiguration.php`: Perfil de pagamento (`id`, `name`, `payment_account_id`, `allowed_methods`, `description`, `is_active`).
- **Interfaces de Repositório** (`app\domain\finance\repositories\`):
  - `PaymentProviderRepositoryInterface.php`, `PaymentConfigurationRepositoryInterface.php`, `CostCenterRepositoryInterface.php`.

##### Contrato Comum de Transações (`app\infrastructure\database\`):
- `TransactionInterface.php`:
  - `public function begin(): void;`
  - `public function commit(): void;`
  - `public function rollback(): void;`
  - `public function run(callable $operation): mixed;`

#### 5.2 Application Layer (`app\usecases\`):

- **Use Cases do Curso & Criação Atômica com Produto**:
  - `CreateCourseUseCase`:
    - Orquestra a criação pedagógica do `Course` e a criação imediata de seu `Product` correspondente dentro de uma mesma transação atômica gerenciada por `TransactionManagerInterface`.
    - Persiste o Curso, obtém o ID gerado, cria o Produto com os dados comerciais informados (ou padrão de rascunho com preço zero), persiste o Produto e registra a Versão 1 de preço.
    - Em caso de falha em qualquer etapa, a transação sofre rollback total, garantindo que nenhum Curso seja salvo sem seu Produto.
- **Use Cases Comerciais (`app\usecases\commercial\`)**:
  - `UpdateProductCommercialConditionsUseCase`:
    - Executa dentro de transação atômica via `TransactionManagerInterface`.
    - Executa bloqueio pessimista no produto via `ProductRepositoryInterface::find_by_id_for_update($product_id)` para serializar concorrência.
    - Se o preço for alterado (`new_price->get_cents() !== current_price->get_cents()`):
      1. Obtém o próximo número de versão isolado (`get_next_version_number($product_id)`).
      2. Monta o snapshot desnormalizado com o contexto pedagógico e financeiro atual.
      3. Salva a nova `ProductPriceVersion`.
      4. Atualiza o `Product`.
    - Se o preço for mantido inalterado, apenas atualiza as demais condições do `Product` sem gerar registro na tabela de versões.
  - `GetProductByCourseUseCase`: Retorna o Produto a partir do ID do curso.
  - `ListProductPriceVersionsUseCase`: Retorna o histórico de reajustes de preço do produto para consulta administrativa.
- **Use Cases Financeiros (`app\usecases\finance\`)**:
  - `ListActivePaymentConfigurationsUseCase`: Retorna perfis ativos.
  - `ListActiveCostCentersUseCase`: Retorna centros de custo ativos.

#### 5.3 Infrastructure Layer (`application/models/`):

- **Transações (`application/models/Transaction_model.php`)**:
  - Implementa `TransactionInterface` estendendo `CI_Model`.
  - Gerencia transações no driver de banco do CI3 (`$this->db->trans_begin()`, `$this->db->trans_status()`, `$this->db->trans_commit()`, `$this->db->trans_rollback()`).
  - O método `run(callable $operation)` captura `\Throwable`, executa rollback e repassa a exceção sem vazar estado corrompido.
- **Models de Persistência (estendendo `MY_Model`)**:
  - `Product_model.php`: Implementa `ProductRepositoryInterface`. Utiliza `ProductDatabase` e `ProductMapper`. Inclui `find_by_id_for_update()` emitindo query com cláusula `FOR UPDATE`.
  - `Product_price_version_model.php`: Implementa `ProductPriceVersionRepositoryInterface`.
  - `Payment_provider_model.php`, `Payment_credential_model.php`, `Payment_account_model.php`, `Cost_center_model.php`, `Payment_configuration_model.php`.
- **Auditoria Transparente**:
  - As operações de escrita nas tabelas continuam sendo auditadas automaticamente pelo mecanismo nativo do `MY_Model` (`log_audit` gravando na tabela `logs`), capturando o usuário logado e o diff JSON de campos alterados.
- **Biblioteca de Criptografia (`application/libraries/Credential_encryption_library.php`)**:
  - Implementa `CredentialEncryptionInterface` com cifra AES-256-GCM.
  - Chave derivada de `getenv('PAYMENT_ENCRYPTION_KEY')`.
  - Formato de armazenamento encapsulado no Value Object `EncryptedSecret`: `base64(iv . tag . ciphertext)`.

#### 5.4 Presentation Layer (`application/controllers/admin/`):

- **Controller `Courses.php`**:
  - `create()`:
    - Valida dados pedagógicos e comerciais via `form_validation`.
    - Converte a string de preço (ex: `"199,90"`) em centavos inteiros (`19990`) sem cálculos em `float`.
    - Injeta dependências via `ModelFactory` e executa o `CreateCourseUseCase`.
  - `edit($id)`:
    - Carrega o Curso, o Produto correspondente (`GetProductByCourseUseCase`) e o histórico de versões (`ListProductPriceVersionsUseCase`).
    - Na submissão, executa `UpdateCourseUseCase` e `UpdateProductCommercialConditionsUseCase`.
    - Erros semânticos (`ValidationException`, `ConflictException`) são capturados globalmente pelo `MY_Controller::_remap()`.

#### 5.5 Frontend & Views (`application/views/admin/courses/`):
- **Formulário Integrado (`form.php`)**:
  - Sem campos de modalidade pedagógica.
  - Seção Comercial:
    - Campo de preço com formatação visual monetária.
    - Switch "Curso Gratuito" que zera o valor e desabilita temporariamente seletores de pagamento.
    - Seletor dinâmico via JavaScript (`form.js`) entre opções de parcelamento e intervalo recorrente.
    - Seleção de Meios de Pagamento, Perfil Financeiro e Centro de Custo.
  - **Aba de Histórico Comercial**:
    - Tabela de consulta de `product_price_versions` (Data, Versão, Preço Anterior, Novo Preço, Usuário, Justificativa). Sem controles de edição ou exclusão.

---

### 6. ESTRATÉGIA DE TESTES AUTOMATIZADOS (`ci3-test`):

1. **Testes Unitários de Domínio (`tests/unit/domain/`)**:
   - `PriceTest.php`:
     - Validação de operações aritméticas com centavos inteiros (`int`).
     - Parsing rigoroso de string monetária sem perda de precisão float.
     - Rejeição de valores negativos.
   - `ProductTest.php`:
     - Validação de produto gratuito (`price_in_cents = 0` com `payment_configuration_id = null`).
     - Rejeição de ativação comercial de produto pago sem perfil financeiro.
     - Rejeição de parcelas em modelo de assinatura e de recorrência em compra avulsa.
     - Validação de invariantes na transição gratuito ↔ pago.
   - `ProductPriceVersionTest.php`:
     - Imutabilidade da entidade de versão.
     - Validação de integridade do snapshot desnormalizado sem dados sensíveis.
2. **Testes Unitários de Use Cases (`tests/unit/usecases/`)**:
   - `CreateCourseUseCaseTest.php`:
     - Sucesso: Criação atômica de Curso e Produto na mesma transação.
     - Falha: Rollback integral se a persistência do produto falhar, garantindo ausência de cursos órfãos.
   - `UpdateProductCommercialConditionsUseCaseTest.php`:
     - Reajuste efetivo de preço gera Versão N+1 imutável com bloqueio de concorrência.
     - Atualização mantendo mesmo preço atualiza condições sem criar nova versão de preço.
     - Transação executa rollback caso ocorra falha na gravação da versão histórica.
   - `ListProductPriceVersionsUseCaseTest.php`.
   - `ListActivePaymentConfigurationsUseCaseTest.php` e `ListActiveCostCentersUseCaseTest.php`.
3. **Mocks de Repositório (`tests/unit/mocks/repositories/`)**:
   - `MockProductRepository.php`, `MockProductPriceVersionRepository.php`, `MockTransactionManager.php`, `MockPaymentConfigurationRepository.php`, `MockCostCenterRepository.php`.
4. **Testes de Segurança e Criptografia**:
   - `CredentialEncryptionLibraryTest.php` e `EncryptedSecretTest.php`: Criptografia reversível AES-256-GCM, proteção de integridade com tag de autenticação e verificação de que segredos nunca vazam em representações de texto plano ou logs.
5. **Cobertura de Código**:
   - Cobertura mínima de **80%** do novo código de Domínio, Casos de Uso e Models, conforme diretriz estrita do projeto (`AGENTS.md` e `GEMINI.md`).

---

### 7. CRITÉRIOS DE ACEITE:

- [ ] A entidade comercial central é `Product`, sem coluna `status` própria, com ciclo de vida e disponibilidade de venda governados exclusivamente pela entidade `Course`.
- [ ] Todo Curso criado possui obrigatoriamente um Produto associado, com integridade garantida por transação atômica na aplicação e pela chave `products.course_id UNIQUE NOT NULL`.
- [ ] A criação de Curso e Produto (mesmo em rascunho) ocorre na mesma transação atômica de banco de dados com rollback garantido em caso de falha.
- [ ] A modalidade pedagógica (`modality`, `CourseModality`, 'online/presencial/hibrido') permanece 100% excluída de tabelas, entidades, formulários e testes.
- [ ] Todos os cálculos, comparações e persistências monetárias utilizam estritamente números inteiros em centavos (`price_in_cents INT UNSIGNED`), sendo o tipo `float` proibido fora da formatação visual.
- [ ] Um Produto gratuito (`price_in_cents = 0`) possui `payment_configuration_id = NULL`, `payment_methods = []`, `max_installments = 1` e `subscription_interval = NULL`.
- [ ] Um Produto pago não pode ser habilitado para venda (`Course.is_available_for_sale = 1`) sem uma configuração financeira ativa e válida, sem fallback automático silencioso.
- [ ] As transições de gratuito para pago e de pago para gratuito geram atomicamente uma nova versão em `product_price_versions` preservando o histórico de preços.
- [ ] A tabela `product_price_versions` é imutável e preserva um snapshot contextual desnormalizado integral a cada alteração efetiva de preço, sem armazenar dados confidenciais de credenciais.
- [ ] A gravação do mesmo preço não gera nova versão na tabela de preços.
- [ ] O sequenciamento de versões (`version_number`) utiliza bloqueio pessimista de linha (`FOR UPDATE`) para serializar concorrência, além da restrição única no banco.
- [ ] A tabela de histórico de preços não é descrita nem tratada como substituto de registros contábeis ou transações de pedidos.
- [ ] O Centro de Custo é totalmente desacoplado da `PaymentConfiguration`, sendo associado diretamente ao `Product` de forma explícita, sem seed `CC-GERAL` ou herança silenciosa.
- [ ] A consistência entre provedor e credencial nas contas recebedoras é validada no domínio e assegurada por chave estrangeira composta no banco de dados.
- [ ] O seed de provedores cadastra apenas o Asaas como ativo, com outros provedores inativos sem sugerir integrações funcionais não implementadas.
- [ ] A auditoria de mutações de banco é realizada pelo mecanismo nativo do `MY_Model` (`log_audit` na tabela `logs`), mantendo-se separada do versionamento comercial de preços.
- [ ] As credenciais financeiras utilizam criptografia autenticada reversível em repouso (AES-256-GCM) com segredos isolados do banco de dados.
- [ ] Pedidos, checkout, processamento transacional de pagamentos, parcelas individuais, webhooks e matrículas automáticas permanecem fora do escopo desta demanda.
- [ ] A suíte de testes unitários 1:1 atinge cobertura mínima de 80% conforme convenção real do projeto, sem credenciais de produção ou chamadas externas a gateways.
