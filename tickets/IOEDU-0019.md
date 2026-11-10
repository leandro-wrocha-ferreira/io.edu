TITULO: Implementação do Novo Design e Layout Administrativo do Stitch (Unificação dos Fluxos IOEDU-0009, IOEDU-0014 e IOEDU-0015)

STATUS: PLANEJADO
DEPENDÊNCIA: IOEDU-0013, IOEDU-0018

REFERÊNCIA NO STITCH:
- Projeto: `io.edu — Modern LMS Administrative Hub` (`projects/13231994778025824568`)

> [!IMPORTANT]
> **DIRETRIZ MANDATÓRIA: MIGRAÇÃO EXCLUSIVA DE ESTRUTURA E LAYOUT (CORES PERMANECEM INALTERADAS)**
> - A finalidade desta demanda é **estritamente migrar o layout, a disposição espacial, os componentes visuais, a arquitetura de informação e a experiência do usuário (UX)** desenhados no Stitch.
> - **NÃO DEVEMOS REPLICAR AS CORES OU PALETAS DE CORES DO STITCH**: As cores, variáveis CSS e design tokens já configurados e consolidados no io.edu (`--edu-*` em `public/assets/css/shared/tokens.css`, com suporte nativo a Light Mode e Dark Mode) **DEVEM SER INTEGRALMENTE MANTIDOS**.
> - **Atenção às discrepâncias visuais nos mockups do Stitch**: No Stitch, algumas telas foram geradas em tema Dark e outras em tema Light. Isso **NÃO** deve ser reproduzido de forma fragmentada no sistema. O io.edu possui um sistema de temas centralizado com alternância global entre Light e Dark mode. Todas as telas migradas devem utilizar exclusivamente os tokens `--edu-*` já existentes no projeto, sem inventar novas paletas de cores.

---

### DESCRIÇÃO:
Modernizar a interface e consolidar os fluxos da área administrativa da plataforma io.edu, adotando a estrutura de layout, componentes, cartões, tabelas e padrões de navegação desenhados no projeto Stitch.
Este ticket centraliza e unifica como subtarefas a entrega de três demandas essenciais do backlog administrativo:
1. **IOEDU-0009**: Seleção de perfil/contexto no fluxo de login para usuários com múltiplos papéis e bloqueio para usuários sem perfil.
2. **IOEDU-0014**: Gestão de Turmas (`CourseClass` / Cohorts) com limites de vagas e janelas temporais de matrícula e realização.
3. **IOEDU-0015**: Composição Curricular Avançada por Turma (aulas exclusivas, ocultação de módulos e Bloco Quiz).

---

### 1. MAPA DE TELAS E ESTRUTURAS DO STITCH A MIGRAR:

*(As telas abaixo servem como guia de wireframe, disposição de dados e experiência de usuário; a estilização deve consumir os tokens `--edu-*` existentes no io.edu)*

| ID da Tela no Stitch | Nome da Tela no Stitch | Módulo Correspondente no LMS |
| :--- | :--- | :--- |
| `acd2b1494b894a2481b49532f935e0e3` | Painel Operacional Executivo | `admin/dashboard` |
| `f61635d23d9e4d1982dbfff592a89466` | Catálogo e Gestão de Cursos (MVP Simplificado) | `admin/cursos` |
| `9ddc330c8c3043cc8d76bb23da6f3132` | Cadastro e Edição de Curso (MVP Simplificado) | `admin/cursos/novo` e `editar` |
| `48308a1baffe43a589c0b1480bcc3532` | Estrutura Curricular (MVP Simplificado) | `admin/cursos/(:num)/conteudo` |
| `a703e0e49a7c4b50935c82a45f708595` | Importar Conteúdo de Outros Cursos | `admin/cursos/(:num)/importar` |
| `30921a0a5af84849a8f289b0f64377f6` | Analytics do Curso - Full Stack Moderno | `admin/cursos/(:num)/analytics` |
| `96aedae9156a46e6ab3ae0c1756cade9` | Gestão de Turmas (MVP Simplificado) | `admin/turmas` (**IOEDU-0014**) |
| `e58477991bc347c9ae52187616082827` | Cadastro e Edição de Turma (MVP Simplificado) | `admin/turmas/novo` e `editar` (**IOEDU-0014**) |
| `b468daeede6344baa7ab3a988001d976` | Disciplinas e Estrutura Curricular da Turma | `admin/turmas/(:num)/grade` (**IOEDU-0015**) |
| `10b52e759b0b4ae69a216cf10bed511d` | Gestão de Usuários (MVP Simplificado) | `admin/usuarios` |
| `866d66b59b294c81930961600fa07cd7` | Perfis de Acesso & Permissões RBAC (MVP Simplificado) | `admin/perfis` |
| `0e7cd3a7d02a4e199756e32ec9ece97e` | Gestão de Categorias e Centros de Custo | `admin/categorias` e `admin/centros-de-custo` |
| `a3f14df63eea4fc68a0ace826210bb55` | Configurações de Gateway Asaas (MVP Simplificado) | `admin/configuracoes-pagamento` |
| `49d3d19bf2174c789a354e82950076d4` | Relatórios e Indicadores Financeiros (MVP Simplificado)| `admin/relatorios/financeiros` |
| `be52a1b4f379486e8e4be14c9f033e6a` | Relatórios Acadêmicos e Prevenção de Evasão | `admin/relatorios/academicos` |

---

### 2. ESTRUTURAÇÃO DAS SUBTAREFAS INTEGRADAS:

#### TAREFA A: Atualização Estrutural do Layout Master Administrativo (`layout/admin.php`)
- Atualizar a estrutura HTML/CSS da Sidebar, Topbar e Page Header para refletir a nova organização de blocos e navegação observada no Stitch:
  - Menu lateral colapsável com agrupamentos lógicos de módulos.
  - Barra superior contendo breadcrumbs contextuais, ações rápidas e avatar com menu do usuário.
  - Manter rigorosamente a paleta de cores e tokens `--edu-*` existentes, garantindo compatibilidade total com os modos Claro e Escuro já implementados.
- Reorganizar a grade e os cards de métricas do Dashboard Principal (`admin/dashboard.php`) conforme o *Painel Operacional Executivo*.

#### TAREFA B: Fluxo de Autenticação e Seleção de Contexto (IOEDU-0009)
- **Bloqueio de Usuário Sem Perfil**:
  - Usuários sem perfil/papel atribuído não podem autenticar, recebendo mensagem padrão de credenciais inválidas.
- **Login de Contexto Direto para Perfil Único**:
  - Se o usuário possuir apenas um perfil cadastrado, o sistema autentica diretamente e redireciona para a área correspondente.
- **Tela de Seleção de Perfil para Múltiplos Papéis**:
  - Se o usuário possuir múltiplos perfis (ex: `admin-master` e `instructor`), redirecionar para tela intermediária de seleção de contexto (`auth/select_role`).
  - Apresentar cards estruturados com a identidade de cada perfil utilizando a biblioteca de componentes existente no io.edu.
- **Alternador de Contexto na Topbar**:
  - Atalho no dropdown do usuário no painel administrativo para alternar entre perfis disponíveis sem necessidade de logout.

#### TAREFA C: Gestão Administrativa de Turmas / Cohorts (IOEDU-0014)
- **Modelagem de Domínio e Banco de Dados**:
  - Migration para criar a tabela `course_classes` (`id`, `course_id`, `name`, `slug`, `description`, `status`, `enrollment_limit_type`, `max_students`, `enrollment_starts_at`, `enrollment_ends_at`, `starts_at`, `ends_at`, timestamps).
  - Entidade `CourseClass`, Value Objects de validação e repositório `ClassRepositoryInterface`.
  - Model `Course_class_model`, DTO e Mapper.
- **Casos de Uso (`app\usecases\course_class\`)**:
  - `CreateCourseClassUseCase`, `UpdateCourseClassUseCase`, `ListCourseClassesUseCase`, `GetCourseClassUseCase`, `ToggleClassStatusUseCase`.
- **Interfaces Administrativas**:
  - Layout de listagem e filtros conforme tela Stitch *Gestão de Turmas (MVP Simplificado)*.
  - Layout de formulário conforme tela Stitch *Cadastro e Edição de Turma (MVP Simplificado)* com validação condicional de vagas e janelas de datas.

#### TAREFA D: Composição Curricular Avançada por Turma & Bloco Quiz (IOEDU-0015)
- **Herança Dinâmica de Conteúdo**:
  - A turma herda 100% da matriz curricular do curso base sem duplicar registros de módulos ou aulas.
  - Tabela `class_lesson_overrides` para suporte a customizações pontuais (ocultar aulas específicas para aquela turma ou adicionar aulas exclusivas).
- **Interface de Gestão Curricular da Turma**:
  - Implementar a estrutura da tela Stitch *Disciplinas e Estrutura Curricular da Turma* (`b468daeede6344baa7ab3a988001d976`).
- **Implementação do Bloco Quiz**:
  - Finalizar a implementação do tipo de bloco `quiz` nos blocos de aula (`lesson_blocks`), adicionando formulário de questões e persistência estruturada.

---

### 3. CRITÉRIOS DE ACEITE:

- [ ] Estruturas de layout e componentes das 15 telas administrativas do Stitch reproduzidas com fidelidade na aplicação.
- [ ] As cores e a paleta do sistema permanecem 100% ancoradas nos tokens `--edu-*` existentes no io.edu, preservando o funcionamento uniforme do Light e Dark mode.
- [ ] Usuários sem perfil bloqueados no login; usuários com múltiplos perfis encaminhados para a tela de seleção de contexto (**IOEDU-0009**).
- [ ] Gestão completa de Turmas em funcionamento com controle de vagas e datas (**IOEDU-0014**).
- [ ] Matriz curricular de turma (aulas exclusivas / ocultação) e Bloco de Quiz ativos na esteira pedagógica (**IOEDU-0015**).
