TITULO: Modelo Comercial e Ofertas de Venda do Curso (Preços e Períodos de Acesso na Venda 24/7)

STATUS: BACKLOG
DEPENDÊNCIA: IOEDU-0011

DESCRIÇÃO:
Implementar a camada comercial e de precificação do Curso (`course_offers`). Como o foco da plataforma é a venda avulsa 24/7 (venda perpétua contínua), a precificação é diretamente vinculada ao Curso, sem dependência de turmas. A modelagem através de Ofertas permite que um mesmo curso ofereça diferentes opções de compra e prazos de acesso ao aluno (ex: "Acesso por 1 Ano" por R$ 197, "Acesso Vitalício" por R$ 397 ou "Assinatura Mensal" recorrente).

---

### MODELOS DE COBRANÇA SUPORTADOS:
1. **`cash` (À Vista)**:
   - Pagamento único (PIX, boleto ou cartão 1x).
2. **`installments` (Parcelamento)**:
   - Pagamento parcelado no cartão (ex: até 12x), com indicação de juros absorvidos ou repassados ao comprador.
3. **`subscription` (Assinatura Recorrente)**:
   - Cobrança cíclica automática com intervalo definido (`monthly`, `quarterly`, `semiannual`, `yearly`).

---

### SOBREPOSIÇÃO DE PERÍODO DE ACESSO NA OFERTA:
Cada oferta pode herdar o período de acesso padrão cadastrado no curso ou customizá-lo:
* *Exemplo prático de um mesmo Curso*:
  * **Oferta A (Padrão 1 Ano)**: R$ 297,00 | `access_period_type = 'limited_time'` | `access_days = 365`.
  * **Oferta B (Vitalício VIP)**: R$ 597,00 | `access_period_type = 'lifetime'` | `access_days = NULL`.
  * **Oferta C (Assinatura Mensal)**: R$ 39,00/mês | `access_period_type = 'subscription'` | acesso ativo enquanto a assinatura estiver paga.

---

### 1. Modelagem da Tabela de Ofertas do Curso (`course_offers`):
- `id`: INT unsigned AI, PK
- `course_id`: INT unsigned, NOT NULL (FK para `courses.id` ON DELETE CASCADE)
- `name`: VARCHAR(255), NOT NULL (ex: "Plano Anual - 1 Ano de Acesso", "Plano Vitalício")
- `slug`: VARCHAR(255), NOT NULL, UNIQUE (identificador para URLs de checkout)
- `pricing_model`: ENUM('cash', 'installments', 'subscription'), NOT NULL
- `price`: DECIMAL(10,2), NOT NULL (preço base da oferta)
- `currency`: VARCHAR(3), DEFAULT 'BRL'
- `max_installments`: INT unsigned, NULL (aplicável quando pricing_model = 'installments', ex: 12)
- `has_interest`: TINYINT(1), DEFAULT 0 (0 = sem juros, 1 = com juros)
- `subscription_interval`: ENUM('monthly', 'quarterly', 'semiannual', 'yearly'), NULL
- `access_period_type`: ENUM('lifetime', 'limited_time', 'inherit_course'), DEFAULT 'inherit_course'
- `access_days`: INT unsigned, NULL (dias de acesso quando limited_time; sobrescreve o curso)
- `is_default`: TINYINT(1), DEFAULT 0 (oferta destacada principal na página do curso)
- `status`: ENUM('active', 'inactive', 'expired'), DEFAULT 'active'
- `starts_at`: DATETIME, NULL (início de lote promocional)
- `expires_at`: DATETIME, NULL (fim de lote promocional)
- `created_at`, `updated_at`, `deleted_at`: DATETIME

---

### 2. Fora de Escopo Deste Ticket (Delimitação Clara):
- Gateway de pagamento real (Stripe, Asaas, etc.).
- Tela de checkout de pagamento com preenchimento de cartão de crédito.
- Emissão de notas fiscais.
> *Nota: Este ticket implementa o catálogo comercial de ofertas do curso. O módulo de pagamento consumirá essas ofertas futuramente.*

---

### 3. Padrões de Implementação (DDD-Lite):
- **Domain Layer (`app\domain\course\commercial\`)**:
  - Entidade: `CourseOffer`.
  - Value Objects: `PricingModel`, `Money`, `InstallmentConfig`, `OfferAccessPeriod`.
  - Interface de Repositório: `CourseOfferRepositoryInterface`.
  - Semantic Exceptions: `InvalidPricingModelException`, `CourseOfferNotFoundException`.
- **Application Layer (`app\usecases\course\commercial\`)**:
  - `CreateCourseOfferUseCase`, `UpdateCourseOfferUseCase`, `ToggleOfferStatusUseCase`, `ListOffersByCourseUseCase`.
- **Infrastructure Layer (`application/models/`)**:
  - `Course_offer_model` estendendo `MY_Model` com DTO e Mapper.
- **Presentation Layer (`application/controllers/admin/`)**:
  - Aba de "Precificação & Ofertas" dentro da gestão do Curso.
  - Tabela com ações rápidas para criar ofertas (à vista, parcelado, vitalício ou 1 ano de acesso).

---

### CRITÉRIOS DE ACEITE:
- [ ] Migration cria a tabela `course_offers` vinculada a `courses` com integridade referencial.
- [ ] É possível cadastrar múltiplas opções de compra para o mesmo curso (ex: 1 ano de acesso vs vitalício).
- [ ] A oferta permite definir se o acesso do aluno será vitalício, por dias corridos (ex: 365 dias) ou herdado da configuração padrão do curso.
- [ ] Validações de domínio: modelo `installments` exige número de parcelas válido; modelo `subscription` exige intervalo de cobrança.
- [ ] É possível marcar uma oferta como principal/destacada (`is_default`).
- [ ] Testes unitários para regras de cálculo e períodos de acesso da oferta (cobertura >= 80%).
