TITULO: Fluxo de Pedidos, Compras, Matrículas e Processamento de Pagamentos

STATUS: BACKLOG_FUTURO
DEPENDÊNCIA: IOEDU-0013

DESCRIÇÃO:
Demanda futura responsável por conceber e implementar o fluxo transacional completo de compras da plataforma io.edu.
Compreende o ciclo de vida do Pedido (`Order`), a orquestração do Checkout, a integração com os Provedores de Pagamento (`PaymentProvider`), o processamento de transações financeiras (`Payment` / `Transaction`), o tratamento de webhooks assíncronos e a concessão automática de acesso via Matrículas (`Enrollment`).

---

### 1. OBJETIVO DA DEMANDA:

Viabilizar que alunos adquiram produtos educacionais (inicialmente Cursos, futuramente Turmas e Bancos de Questões) através de uma experiência de checkout fluida e segura, garantindo auditoria financeira, conciliação e rastreabilidade total entre o que foi contratado e o acesso liberado ao aluno.

---

### 2. LIMITES DO ESCOPO E PRINCIPAIS RESPONSABILIDADES:

```text
Fluxo Transacional de Compras (IOEDU-0017):
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. Checkout & Criação do Pedido (Order)                                     │
│  - Identifica o comprador (User / Student)                                  │
│  - Seleciona o item do pedido (Order Item -> Referência ao Produto)         │
│  - Realiza o SNAPSHOT das condições comerciais vigentes                     │
│    (preço contratado, modelo de cobrança, parcelas, taxas e centro de custo)│
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ gera cobrança
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. Processamento Financeiro & Cobrança (Payment / Transaction)              │
│  - Dispara requisição ao gateway com base na PaymentConfiguration ativa     │
│  - Registra a transação com external_id (id da transação no Asaas/Stripe)   │
│  - Gerencia parcelas individuais ou ciclos de assinatura                    │
│  - Recebe e processa notificações via Webhooks com validação de assinatura  │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ pagamento confirmado (status = 'paid')
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 3. Concessão de Acesso Educacional (Enrollment)                             │
│  - Criação automática da matrícula do aluno no curso                        │
│  - Vigência do acesso baseada nas regras educacionais (vitalício / limitado)│
│  - Bloqueio ou cancelamento automático em caso de estorno ou inadimplência  │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### 2.1 Distinção Clara entre Pedido (`Order`) e Pagamento (`Payment`):
- **Pedido (`Order`)**: Representa a intenção comercial e a compra em si. Contém o comprador, os itens adquiridos (`order_items`), o valor total e o status do pedido (`pending`, `completed`, `cancelled`).
- **Pagamento / Transação (`Payment`)**: Representa a operação financeira no gateway. Um pedido pode ter múltiplos pagamentos (ex: tentativa que falhou e nova tentativa bem-sucedida, pagamentos com múltiplos cartões, ou parcelas/faturas mensais no caso de assinaturas).

#### 2.2 Preservação do Histórico e Snapshot das Condições Comerciais:
- Uma compra nunca deve depender exclusivamente de um ponteiro direto para um registro mutável de configuração de venda.
- Ao fechar o pedido, o sistema registrará um **snapshot imutável** ou utilizará versionamento estrito das condições comerciais vigentes naquele instante:
  - Preço efetivamente contratado.
  - Modelo de cobrança (`installment` ou `subscription`).
  - Quantidade de parcelas escolhida.
  - Intervalo de recorrência e datas de cobrança.
  - Meios de pagamento e configuração financeira utilizados na data da compra.
  - Centro de custo alocado.
- Reajustes futuros no catálogo nunca afetarão o histórico contábil de pedidos passados.

#### 2.3 Relação entre Produto Adquirido e Matrícula (`Enrollment`):
- O pedido concluído dispara a criação do registro de matrícula (`enrollment`) para o aluno no produto correspondente (ex: `course_id`).
- A matrícula respeita o período de acesso configurado no Domínio Educacional (`access_period_type = 'lifetime'` ou dias corridos via `access_days`).
- Falhas subsequentes, estornos (`chargeback`) ou cancelamentos de assinatura acionam rotinas para suspender ou revogar a matrícula.

#### 2.4 Integração com Provedores, Webhooks e Conciliação:
- Adapters desacoplados para cada provedor configurado no IOEDU-0013 (Asaas, Stripe, Pagar.me, etc.).
- Endpoints idempotentes de recebimento de Webhooks para atualização em tempo real de status (`pending` -> `paid` / `failed` / `refunded`).
- Rotinas de conciliação assíncrona para checagem ativa de transações pendentes.

---

### 3. DEPENDÊNCIA EM RELAÇÃO AO IOEDU-0013:

A demanda **IOEDU-0017** depende integralmente das estruturas administrativas e de configuração comercial estabelecidas no **IOEDU-0013**:
1. Utiliza a `ProductSaleConfiguration` como fonte da verdade para precificação, limites de parcelamento e métodos aceitos durante a montagem do checkout.
2. Utiliza a `PaymentConfiguration` vinculada ao produto para resolver credenciais de API, conta recebedora e ambiente do gateway adequado no momento da cobrança.
3. Utiliza a regra de precedência de `CostCenter` definida na configuração comercial e financeira para roteamento da receita faturada.

---

### 4. DELIMITAÇÃO:

Este documento estabelece as fronteiras arquiteturais e os requisitos esperados para a IOEDU-0017. A modelagem detalhada de tabelas (`orders`, `order_items`, `payments`, `enrollments`), a escrita de migrations, mappers, use cases de checkout e endpoints de webhooks serão formalizados no momento da execução desta demanda.
