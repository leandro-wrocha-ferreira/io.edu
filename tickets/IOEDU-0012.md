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
- Bloco 1: Vídeo explicativo (YouTube, Vimeo ou Bunny)
- Bloco 2: Texto em Markdown/HTML com resumo teórico e links
- Bloco 3: Arquivo anexo para download (PDF de apoio ou código-fonte)
- Bloco 4: Questão/Quiz de fixação

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
- `slug`: VARCHAR(255), NOT NULL
- `description`: TEXT, NULL
- `duration_in_seconds`: INT unsigned, DEFAULT 0 (tempo somado dos vídeos ou informado manualmente)
- `sort_order`: INT unsigned, DEFAULT 0 (ordenação sequencial dentro do módulo)
- `status`: ENUM('draft', 'published'), DEFAULT 'draft'
- `created_at`, `updated_at`, `deleted_at`: DATETIME

#### 1.3 Blocos de Conteúdo da Aula (`lesson_blocks`):
- `id`: INT unsigned AI, PK
- `lesson_id`: INT unsigned, NOT NULL (FK para `course_lessons.id` ON DELETE CASCADE)
- `type`: ENUM('video', 'text', 'attachment', 'quiz'), NOT NULL
- `payload`: JSON, NOT NULL (estrutura flexível com dados específicos do bloco: URL do vídeo, texto markdown, arquivo anexo, etc.)
- `sort_order`: INT unsigned, DEFAULT 0
- `created_at`, `updated_at`, `deleted_at`: DATETIME

---

### 2. Regras de Negócio e Comportamentos:
- **Ordenação Explícita**: A ordem dos módulos dentro de um curso e das aulas dentro de um módulo deve ser gerenciável (arrastar/soltar ou botões de subir/descer).
- **Publicação Parcial**: Módulos ou aulas com status `draft` não aparecem para o aluno na área de membros, permitindo ao instrutor preparar novos conteúdos sem exibi-los antes da hora.
- **Cálculo de Duração**: A duração estimada da aula pode ser calculada a partir da duração dos blocos de vídeo ou preenchida manualmente pelo instrutor.

---

### 3. Padrões de Implementação (DDD-Lite):
- **Domain Layer (`app\domain\course\content\`)**:
  - Entidades: `CourseModule`, `CourseLesson`, `LessonBlock`.
  - Value Objects: `BlockType` (video, text, attachment, quiz), `SortOrder`.
  - Interfaces de Repositório: `ModuleRepositoryInterface`, `LessonRepositoryInterface`, `BlockRepositoryInterface`.
- **Application Layer (`app\usecases\course\content\`)**:
  - `CreateModuleUseCase`, `UpdateModuleUseCase`, `ReorderModulesUseCase`, `DeleteModuleUseCase`.
  - `CreateLessonUseCase`, `UpdateLessonUseCase`, `ReorderLessonsUseCase`, `DeleteLessonUseCase`.
  - `ManageLessonBlocksUseCase`.
- **Infrastructure Layer (`application/models/`)**:
  - `Course_module_model`, `Course_lesson_model`, `Lesson_block_model`.
- **Presentation Layer (`application/controllers/admin/`)**:
  - Gestor de Currículo/Ementa integrado à edição do Curso no Admin (`Course_curriculum.php`).
  - Rotas assíncronas para reordenação via drag-and-drop (`http.js`).

---

### CRITÉRIOS DE ACEITE:
- [ ] Migration cria tabelas `course_modules`, `course_lessons` e `lesson_blocks` com integridade referencial e índices.
- [ ] É possível criar, editar, reordenar e excluir módulos de um curso pelo painel administrativo.
- [ ] É possível criar, editar, reordenar e excluir aulas dentro de um módulo.
- [ ] É possível adicionar blocos de diferentes tipos (vídeo, texto, anexo) dentro de uma aula.
- [ ] A ordenação hierárquica é persistida e respeitada em todas as listagens.
- [ ] Testes unitários para regras de reordenação e validação da árvore de conteúdo (cobertura >= 80%).
