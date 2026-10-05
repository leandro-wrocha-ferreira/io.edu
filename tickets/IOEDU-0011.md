TITULO: Gestão de Categorias e Catálogo Base de Cursos
STATUS: CONCLUÍDO

DADOS DE FINALIZAÇÃO:
Data de Conclusão: 05/10/2026
Responsável: Antigravity Agent
Testes Automatizados: 100% aprovados (163 testes, 527 asserções)
Arquitetura: DDD-Lite com 1:1 Use Case Unit Tests

DESCRIÇÃO:
Implementar a base do catálogo educacional do LMS, compreendendo a gestão de Categorias e a entidade principal Curso. O Curso é o produto principal para venda avulsa contínua, permitindo que o aluno compre a qualquer momento e inicie seus estudos imediatamente. O Curso define sua identidade pedagógica, ementa, regras de emissão de certificado e o período padrão de acesso do aluno (ex: acesso vitalício ou acesso por tempo determinado, como 1 ano / 365 dias).

---

### CONCEITO DE PRODUTO & MODELO MENTAL:
1. **Curso é o Produto Comercial Principal**:
   - Ao contrário de turmas fechadas, o curso fica disponível para compra contínua na vitrine.
   - O aluno compra o curso, a matrícula é gerada e ele tem acesso imediato aos módulos e aulas.
2. **Período de Acesso do Aluno (Validade da Matrícula)**:
   - Todo curso possui uma regra clara de vigência de acesso para o aluno:
     - `access_period_type = 'lifetime'`: Acesso vitalício (sem data de expiração).
     - `access_period_type = 'limited_time'`: Acesso com prazo determinado em dias (`access_days`), por exemplo: 365 dias (1 ano), 180 dias (6 meses) ou 730 dias (2 anos) contados a partir da data de matrícula/compra.
3. **Turmas são Opcionais (Organização Futura)**:
   - O curso opera de forma 100% autônoma. O conceito de "Turma" (tratado no IOEDU-0014) servirá apenas como ferramenta opcional de organização para mentorias, grupos com datas de início/fim ou ambientes acadêmicos, sem travar a venda direta do curso.

---

### O QUE É SEO (meta_title, meta_description) E POR QUE INCLUIR NO CURSO:
- **`meta_title`**: Título otimizado para motores de busca (Google) e compartilhamento em redes sociais/WhatsApp (OpenGraph). Enquanto o `title` interno pode ser "PHP 8 Avançado", o `meta_title` pode ser "Curso de PHP 8 Avançado com DDD e Arquitetura Limpa | io.edu" (50-60 caracteres).
- **`meta_description`**: Breve sinopse (140-160 caracteres) exibida logo abaixo do título no resultado do Google. Permite que o time de marketing defina a chamada de atração sem poluir a descrição pedagógica formal do curso.
- **Decisão no MVP**: Ambos são campos opcionais no cadastro do curso. Se não preenchidos, o sistema usa como fallback o próprio `title` e `short_description`.
- **Decisão de Produto**: Os campos de SEO não devem existir no momento, nem dentro do banco, muito menos no front. Apenas quando o time de marketing entender que chegou o momento de fazer SEO, devemos implementar. Por enquanto, apenas com title e short_description fazemos SEO.

---

### 1. Modelagem da Entidade Categoria (`categories`):
- **Campos**:
  - `id`: INT unsigned AI, PK
  - `name`: VARCHAR(150), NOT NULL
  - `slug`: VARCHAR(180), NOT NULL, UNIQUE
  - `status`: ENUM('active', 'inactive'), DEFAULT 'active'
  - `created_at`, `updated_at`, `deleted_at`: DATETIME
- **Regras**:
  - Categoriza os cursos para navegação na vitrine e filtros no catálogo (ex: "Programação", "Design", "Marketing").
  - `slug` gerado automaticamente a partir do nome e único no sistema.

### 2. Modelagem da Entidade Curso (`courses`):
- **Campos**:
  - `id`: INT unsigned AI, PK
  - `category_id`: INT unsigned, NOT NULL (FK para `categories.id` ON DELETE RESTRICT)
  - `title`: VARCHAR(255), NOT NULL
  - `slug`: VARCHAR(255), NOT NULL, UNIQUE (URL amigável: `/cursos/nome-do-curso`)
  - `short_description`: VARCHAR(500), NULL (resumo para cards de vitrine)
  - `description`: TEXT, NULL (ementa completa e detalhes do curso)
  - `image`: VARCHAR(255), NULL (caminho/URL da imagem de capa)
  - `status`: ENUM('draft', 'active', 'archived'), DEFAULT 'draft'
  - `workload_in_hours`: INT unsigned, NULL (carga horária estimada exibida no certificado)
  - `duration_in_seconds`: INT unsigned, DEFAULT 0 (tempo total somado de mídia/vídeos)
  - `objectives`: TEXT, NULL (o que o aluno vai aprender)
  - `target_audience`: TEXT, NULL (para quem é este curso)
  - `requirements`: TEXT, NULL (pré-requisitos recomendados)
  - `access_period_type`: ENUM('lifetime', 'limited_time'), DEFAULT 'limited_time'
  - `access_days`: INT unsigned, NULL (dias de acesso quando limited_time; ex: 365 para 1 ano)
  - `certificate_enabled`: TINYINT(1), DEFAULT 1 (emite certificado ao concluir 100%)
  - `created_at`, `updated_at`, `deleted_at`: DATETIME

---

### 3. Padrões de Implementação (DDD-Lite):
- **Domain Layer (`app\domain\course\`)**:
  - Entidades: `Category`, `Course`.
  - Value Objects: `CourseSlug`, `CourseStatus`, `CourseAccessPeriod` (valida lifetime vs dias de acesso), `Workload`.
  - Interfaces de Repositório: `CategoryRepositoryInterface`, `CourseRepositoryInterface`.
  - Semantic Exceptions: `CourseNotFoundException`, `DuplicateSlugException`, `CategoryNotFoundException`.
- **Application Layer (`app\usecases\course\`)**:
  - `CreateCourseUseCase`, `UpdateCourseUseCase`, `ArchiveCourseUseCase`, `ListCoursesUseCase`, `GetCourseDetailUseCase`.
  - `CreateCategoryUseCase`, `UpdateCategoryUseCase`, `ListCategoriesUseCase`.
- **Infrastructure Layer (`application/models/`)**:
  - `Category_model` e `Course_model` estendendo `MY_Model`, utilizando Mappers e Database DTOs.
- **Presentation Layer (`application/controllers/admin/`)**:
  - `Courses.php` e `Categories.php` estendendo `MY_Controller`.
  - Padrão de formulário sem `_handle_*()`, com resposta unificada ao final.
- **UI/Visual (`ci3-ui`)**:
  - Telas administrativas com suporte a Light/Dark mode e classes de animação (`animate-fade-up`).
  - Painel de edição com seções: "Identificação", "Regras de Acesso e Certificação", "Dados Pedagógicos", "SEO".

---

### CRITÉRIOS DE ACEITE:
- [x] Migration cria tabelas `categories` e `courses` com integridade referencial, índices e campos de período de acesso.
- [x] CRUD completo de Categorias no Painel Admin (com validação de nome, slug único e status).
- [x] CRUD completo de Cursos no Painel Admin com seleção de categoria, upload/URL de imagem e preenchimento de metadados educacionais.
- [x] Configuração de período de acesso funcionando: se selecionar `limited_time`, exige preenchimento de `access_days > 0` (ex: 365 para 1 ano).
- [x] Toggle de emissão de certificado (`certificate_enabled`) no cadastro do curso.
- [x] Garantia de unicidade de `slug` para cursos e categorias com validação preventiva.
- [x] Status do curso respeita os estados `draft`, `active` e `archived`.
- [x] Testes unitários para as entidades de Domínio e Use Cases (cobertura mínima de 80%).

---

### ROADMAP DOS TICKETS SUBSEQUENTES:
- **IOEDU-0012**: Estrutura de Conteúdo do Curso (Módulos, Aulas e Blocos Reutilizáveis).
- **IOEDU-0013**: Modelo Comercial e Ofertas de Venda do Curso (`course_offers` - à vista, parcelado, assinatura).
- **IOEDU-0014**: [FASE 2] Gestão de Turmas para Organização Acadêmica e Grupos Específicos.
- **IOEDU-0015**: [FASE 2] Composição Avançada de Conteúdo Exclusivo para Turmas.
