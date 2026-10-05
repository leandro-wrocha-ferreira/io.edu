TITULO: [FASE 2] Gestão de Turmas (Classes/Cohorts) para Organização de Grupos e Períodos Letivos

STATUS: BACKLOG_FUTURO
DEPENDÊNCIA: IOEDU-0011, IOEDU-0012, IOEDU-0013

DESCRIÇÃO:
Implementar a funcionalidade de Turmas (`course_classes`) como uma camada opcional de organização para o Curso. Enquanto a venda padrão do curso opera 24/7 de forma avulsa e autônoma, a Turma atende cenários que demandam organização de grupos com datas específicas: turmas de pós-graduação, bootcamps com cronograma fechado, mentorias em grupo e processos seletivos com número limitado de vagas.

---

### CONCEITO DE PRODUTO (TURMA COMO ORGANIZADOR):
1. **Não Bloqueante para Vendas Avulsas**:
   - Um curso pode existir e vender indefinidamente sem nunca ter uma turma criada.
   - A criação de turmas é um recurso ativado apenas quando o curso demanda controle de grupos (cohorts).
2. **Ciclo Operacional da Turma**:
   - Vagas limitadas (`max_students`) ou ilimitadas.
   - Período de inscrições (`enrollment_starts_at` até `enrollment_ends_at`).
   - Período letivo (`starts_at` até `ends_at`).
3. **Vínculo com Matrículas**:
   - A futura tabela de matrículas (`enrollments`) suportará matrículas diretas no curso (`class_id = NULL`) ou vinculadas a uma turma específica (`class_id = ID`).

---

### 1. Modelagem da Entidade Turma (`course_classes`):
- `id`: INT unsigned AI, PK
- `course_id`: INT unsigned, NOT NULL (FK para `courses.id` ON DELETE RESTRICT)
- `name`: VARCHAR(255), NOT NULL (ex: "Turma 2026.1 - Noite", "Mentoria Grupo Alpha")
- `slug`: VARCHAR(255), NOT NULL, UNIQUE
- `description`: TEXT, NULL
- `status`: ENUM('draft', 'open_enrollment', 'in_progress', 'completed', 'cancelled'), DEFAULT 'draft'
- `enrollment_limit_type`: ENUM('unlimited', 'limited'), DEFAULT 'unlimited'
- `max_students`: INT unsigned, NULL (obrigatório se limit_type for 'limited')
- `enrollment_starts_at`: DATETIME, NULL
- `enrollment_ends_at`: DATETIME, NULL
- `starts_at`: DATETIME, NULL
- `ends_at`: DATETIME, NULL
- `created_at`, `updated_at`, `deleted_at`: DATETIME

---

### 2. Padrões de Implementação (DDD-Lite):
- **Domain Layer (`app\domain\course_class\`)**:
  - Entidade `CourseClass` com Value Objects para limites e períodos.
  - Repositório `ClassRepositoryInterface`.
- **Application Layer (`app\usecases\course_class\`)**:
  - Casos de uso de gestão e ciclo de vida de turmas.
- **Infrastructure Layer (`application/models/`)**:
  - `Course_class_model` estendendo `MY_Model`.
- **Presentation Layer (`application/controllers/admin/`)**:
  - Gestão de Turmas acessível a partir da visão do Curso.

---

### CRITÉRIOS DE ACEITE:
- [ ] Migration cria tabela `course_classes` vinculada a `courses`.
- [ ] Criação e edição de turmas funcionando como ferramenta de organização de grupos.
- [ ] Validação de vagas (`limited` exige `max_students > 0`).
- [ ] Validação de janelas de datas (inscrição e realização).
- [ ] Testes unitários para regras de domínio de turmas (cobertura >= 80%).
