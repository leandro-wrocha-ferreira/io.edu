TITULO: Estrutura de Conteúdo do Curso (Módulos, Aulas e Blocos de Aprendizagem)

STATUS: BACKLOG
DEPENDÊNCIA: IOEDU-0011

DESCRIÇÃO:
Implementar a estrutura hierárquica de conteúdo pedagógico vinculada ao Curso. O conteúdo do curso é organizado em três níveis de profundidade: Módulos -> Aulas -> Blocos de Conteúdo. Essa árvore representa a matriz de aprendizagem completa que será consumida imediatamente pelos alunos matriculados no curso (modelo de venda 24/7).

---

### ESTRUTURA HIERÁRQUICA:
```text
Curso (courses)
└── Módulos (course_modules)
    └── Aulas (course_lessons)
        └── Blocos de Conteúdo (lesson_blocks)
```

Exemplo prático de blocos em uma aula:
- **Bloco Vídeo**: Provedor (YouTube, Vimeo, Bunny), URL/Embed e duração em segundos.
- **Bloco Texto**: Conteúdo em Markdown ou HTML estruturado com resumo teórico e links.
- **Bloco Anexo**: Arquivo para download (PDF de apoio, slides ou código-fonte).
- **Bloco Quiz**: Questões de fixação ou autoavaliação rápida.

---

### 1. Modelagem das Tabelas de Conteúdo:

#### 1.1 Módulos do Curso (`course_modules`):
- `id`: INT unsigned AI, PK
- `course_id`: INT unsigned, NOT NULL (FK para `courses.id` ON DELETE CASCADE)
- `title`: VARCHAR(255), NOT NULL
- `description`: TEXT, NULL
- `sort_order`: INT unsigned, DEFAULT 0 (ordenação sequencial dentro do curso)
- `status`: ENUM('draft', 'published'), DEFAULT 'draft'
- `created_at`, `updated_at`, `deleted_at`: DATETIME

#### 1.2 Aulas do Módulo (`course_lessons`):
- `id`: INT unsigned AI, PK
- `module_id`: INT unsigned, NOT NULL (FK para `course_modules.id` ON DELETE CASCADE)
- `title`: VARCHAR(255), NOT NULL
- `description`: TEXT, NULL
- `sort_order`: INT unsigned, DEFAULT 0 (ordenação sequencial dentro do módulo)
- `status`: ENUM('draft', 'published'), DEFAULT 'draft'
- `created_at`, `updated_at`, `deleted_at`: DATETIME
- *Nota Arquitetural*: `slug` e `duration_in_seconds` foram descontinuados/removidos via migration (`20261009000003`); o roteamento de aulas utiliza o ID numérico e a duração é calculada sob demanda pelos blocos de vídeo.

#### 1.3 Blocos de Conteúdo da Aula (`lesson_blocks`):
- `id`: INT unsigned AI, PK
- `lesson_id`: INT unsigned, NOT NULL (FK para `course_lessons.id` ON DELETE CASCADE)
- `type`: ENUM('video', 'text', 'attachment', 'quiz'), NOT NULL
- `sort_order`: INT unsigned, DEFAULT 0 (ordenação sequencial dentro da aula)
- `is_required`: TINYINT(1), DEFAULT 1
- `created_at`, `updated_at`, `deleted_at`: DATETIME
- *Normalização Polimórfica (Class Table Inheritance)*:
  - `video_blocks`: `provider`, `url`, `duration_in_seconds`.
  - `text_blocks`: `content`, `min_reading_time_in_minutes`.
  - `attachment_blocks`: `filename`, `filepath`, `file_size`, `file_type`, `min_reading_time_in_minutes`.
  - *Bloco Quiz (`quiz`)*: Postergado para a demanda de Avaliações / Testes de Fixação (IOEDU-0014). Na interface administrativa, a ação exibe alerta amigável de "Recurso em Desenvolvimento".

---

### 2. Regras de Negócio e Comportamentos:
- **Ordenação Explícita e Isolada por Escopo**:
  - A reordenação de módulos afeta apenas os módulos pertencentes ao mesmo `course_id`.
  - A reordenação de aulas afeta apenas as aulas do mesmo `module_id`.
  - A reordenação de blocos afeta apenas os blocos da mesma `lesson_id`.
- **Flexibilidade de Títulos**:
  - Módulos e aulas podem compartilhar títulos idênticos no mesmo curso (ex: múltiplos módulos "Exercícios Práticos" ou "Introdução"), permitindo organização pedagógica livre pelo instrutor.
- **Publicação Parcial & Controle de Acesso**:
  - Módulos ou aulas com status `draft` permanecem visíveis apenas na área administrativa/instrutor e são filtrados na área do aluno.
- **Payload Estruturado por Tipo de Bloco Ativo**:
  - `video`: `{"provider": "youtube|custom", "url": "...", "duration_in_seconds": 360}`
  - `text`: `{"content": "...", "min_reading_time_in_minutes": 5}`
  - `attachment`: `{"filename": "...", "filepath": "...", "file_size": 1024, "file_type": "pdf", "min_reading_time_in_minutes": 2}`
  - `quiz`: *(Em desenvolvimento — IOEDU-0014)*

---

### 3. Padrões de Implementação (DDD-Lite):

#### 3.1 Domain Layer (`app\domain\course\`):
- **Entidades**:
  - `CourseModule` (em `application/domain/course/CourseModule.php`)
  - `CourseLesson` (em `application/domain/course/CourseLesson.php`)
  - `LessonBlock` (em `application/domain/course/LessonBlock.php`)
- **Value Objects** (`app\domain\course\value_objects\`):
  - `LessonSlug.php`: Validação e normalização de slug de aula.
  - `ModuleStatus.php` / `LessonStatus.php`: Validação dos estados `draft` e `published`.
  - `LessonDuration.php`: Encapsulamento de duração em segundos e conversão para formatos amigáveis.
- **Constantes** (`app\domain\course\constants\`):
  - `BlockType.php`: Tipos de bloco permitidos (`VIDEO = 'video'`, `TEXT = 'text'`, `ATTACHMENT = 'attachment'`, `QUIZ = 'quiz'`).
- **Interfaces de Repositório** (`app\domain\course\repositories\`):
  - `CourseModuleRepositoryInterface.php`
  - `CourseLessonRepositoryInterface.php`
  - `LessonBlockRepositoryInterface.php`
- **Semantic Exceptions** (`app\domain\exceptions\`):
  - `NotFoundException`, `ValidationException`, `ConflictException`.

#### 3.2 Application Layer (`app\usecases\course\`):
- **Use Cases de Módulos**:
  - `CreateCourseModuleUseCase`
  - `UpdateCourseModuleUseCase`
  - `ReorderCourseModulesUseCase`
  - `DeleteCourseModuleUseCase`
  - `GetCourseModuleUseCase`
- **Use Cases de Aulas**:
  - `CreateCourseLessonUseCase`
  - `UpdateCourseLessonUseCase`
  - `ReorderCourseLessonsUseCase`
  - `DeleteCourseLessonUseCase`
  - `GetCourseLessonUseCase`
- **Use Cases de Blocos**:
  - `CreateLessonBlockUseCase`
  - `UpdateLessonBlockUseCase`
  - `DeleteLessonBlockUseCase`
  - `ReorderLessonBlocksUseCase`

#### 3.3 Infrastructure Layer (`application/models/`):
- **Models** (estendendo `MY_Model` e implementando as interfaces de repositório):
  - `Course_module_model.php`
  - `Course_lesson_model.php`
  - `Lesson_block_model.php`
- **Database DTOs** (`application/models/dtos/`):
  - `CourseModuleDatabase.php`
  - `CourseLessonDatabase.php`
  - `LessonBlockDatabase.php`
- **Mappers** (`application/models/mappers/`):
  - `CourseModuleMapper.php`
  - `CourseLessonMapper.php`
  - `LessonBlockMapper.php`

#### 3.4 Presentation Layer (`application/controllers/admin/`):
- `Curriculum.php` (ou `Course_curriculum.php`) estendendo `MY_Controller`.
- Fluxo unificado de formulários e respostas AJAX com `json_response()`.
- Rotas amigáveis em português (`application/config/routes.php`):
  - `admin/cursos/(:num)/curriculo` -> `admin/curriculum/index/$1`
  - `admin/cursos/modulos/salvar` -> `admin/curriculum/save_module`
  - `admin/cursos/modulos/reordenar` -> `admin/curriculum/reorder_modules`
  - `admin/cursos/aulas/salvar` -> `admin/curriculum/save_lesson`
  - `admin/cursos/aulas/reordenar` -> `admin/curriculum/reorder_lessons`
  - `admin/cursos/blocos/salvar` -> `admin/curriculum/save_block`
  - `admin/cursos/blocos/reordenar` -> `admin/curriculum/reorder_blocks`

#### 3.5 Frontend & Interatividade (`ci3-ui` e `ci3-js`):
- Views integradas ao layout master `layout/admin` com componentes `.edu-*` e animações `.animate-fade-up`.
- Scripts modulares em `public/assets/js/curriculum.js` utilizando o cliente `Http` nativo (`public/assets/js/http.js`) para operações assíncronas e drag-and-drop.

---

### 4. Estratégia de Testes Automatizados (`ci3-test`):
- Testes unitários 1:1 para cada Use Case em `tests/unit/usecases/course/`.
- Repositórios em memória compartilhados em `tests/mocks/repositories/` (`InMemoryCourseModuleRepository`, `InMemoryCourseLessonRepository`, `InMemoryLessonBlockRepository`).
- Cobertura de testes automatizados >= 80%.

---

### CRITÉRIOS DE ACEITE:
- [ ] Migration cria tabelas `course_modules`, `course_lessons` e `lesson_blocks` com integridade referencial, índices e triggers de timestamp.
- [ ] CRUD e reordenação de módulos funcionando de forma isolada por curso.
- [ ] CRUD e reordenação de aulas funcionando de forma isolada por módulo.
- [ ] Adição, edição, remoção e reordenação atômica de blocos de conteúdo (vídeo, texto, anexo, quiz) por aula.
- [ ] Endpoints assíncronos respondendo com tratamento semântico de erros e HTTP status codes via `MY_Controller`.
- [ ] Suite de testes unitários cobrindo todas as regras de negócio dos use cases e entidades (cobertura >= 80%).
