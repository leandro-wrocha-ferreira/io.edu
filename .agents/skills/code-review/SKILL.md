---
name: code-review
description: Realiza code review seguindo as convenções de arquitetura DDD-lite do projeto e organiza commits agrupados por lógica semântica (Conventional Commits).
---

# Code Reviewer & Semantic Commits Skill

Use when performing code reviews against project architectural standards and preparing clean, logically grouped semantic commits.

---

## Responsibilities

1. **Code Review**: Analyze modified code following DDD-lite architectural conventions, PSR-12, docblock requirements, error handling, and security guidelines.
2. **Semantic Commits**: Group modified files by semantic responsibility (`domain`, `usecases`, `infrastructure`, `presentation`, `config`, `tests`, `docs`) and create structured commit messages following Conventional Commits in English.

---

## Project Conventions Checklist

Always check `AGENTS.md` and `GEMINI.md` for current conventions:

### 1. Architecture (DDD-Lite)
- [ ] **Domain**: `app\domain\<context>` — Entities, Value Objects, Interfaces. NO framework dependencies.
- [ ] **Domain Exceptions**: `app\domain\exceptions\` — Throw semantic exceptions (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`).
- [ ] **Use Cases**: `app\usecases\<context>` — Orchestrate business rules and queries. Instantiate models via `Model_factory::make()`.
- [ ] **Models**: `application/models/` (lowercase filenames, no namespaces) — Extend `MY_Model` and implement Domain Repository Interfaces.
- [ ] **Controllers**: `application/controllers/` (subdirectories: `auth/`, `admin/`, `student/`, no namespaces) — Extend `MY_Controller`.
- [ ] **Global Exception Handling**: Exceptions in controllers bubble up to `MY_Controller::_remap()`. No redundant try/catch blocks in controllers.
- [ ] **Form Validation Flow**: Directly check `$this->form_validation->run() === TRUE` inside action methods with a single `$this->load->view()` at the end.
- [ ] **Language Loading**: Handled globally by `Language_check` hook. No manual `$this->lang->load()` in controllers.

### 2. Code Style
- [ ] **PSR-12**: Opening brace `{` on the next line for classes and methods.
- [ ] **Docblocks**: Mandatory on all classes and methods — **always in English**.
- [ ] **Indentation**: Tabs per `.editorconfig`.
- [ ] **Git Commands**: All Git operations MUST use `git-leandro` command (e.g. `git-leandro commit -m "..."`).

---

## Semantic Commit Groups

Group files into separate atomic commits by responsibility:

| Group | Scope | Description | Example Files |
| :--- | :--- | :--- | :--- |
| **`domain`** | `feat(domain)` | Entities, Value Objects, Interfaces, Domain Exceptions | `User.php`, `Email.php`, `Role.php` |
| **`usecases`** | `feat(usecases)` | Use case classes and business orchestration | `CreateUserUseCase.php`, `GetUserUseCase.php` |
| **`infrastructure`** | `feat(infrastructure)` | Models, Migrations, Model Factory | `User_model.php`, `MY_Model.php` |
| **`presentation`** | `feat(presentation)` | Controllers, Views, Assets | `Users.php`, `users/index.php` |
| **`config`** | `chore(config)` | Configuration, Routes, Hooks, Autoload | `routes.php`, `hooks.php` |
| **`tests`** | `test(unit/integration)` | PHPUnit unit & integration tests, Codeception E2E | `UserTest.php`, `MY_ModelTest.php` |
| **`docs`** | `docs` | Documentation, Rules, Skills | `AGENTS.md`, `GEMINI.md`, `SKILL.md` |

---

## Commit Message Format

```
<type>(<scope>): <short description in English>

- <detail 1>
- <detail 2>
```

**Types:**
- `feat`: New feature or business capability
- `fix`: Bug fix
- `refactor`: Code change without changing behavior
- `test`: Adding or updating tests
- `docs`: Documentation and guidelines
- `chore`: Config, dependencies, build scripts
- `style`: Formatting, docblocks, whitespace

---

## Code Review & Commit Execution Example

```bash
# 1. Check status
git status

# 2. Add and commit Domain layer
git add application/domain/identity/*.php
git commit -m "feat(domain): add User entity and Email value object

- Implement User entity with authentication methods
- Create Email value object with validation
- Define UserRepositoryInterface"

# 3. Add and commit Use Cases layer
git add application/usecases/identity/*.php
git commit -m "feat(usecases): implement AuthenticateUserUseCase

- Add login authentication use case
- Implement semantic validation exception handling"

# 4. Add and commit Infrastructure / Models layer
git add application/models/User_model.php
git commit -m "feat(infrastructure): implement User_model extending MY_Model

- Extend MY_Model lifecycle engine
- Implement UserRepositoryInterface persistence"

# 5. Add and commit Tests
git add tests/unit/*.php
git commit -m "test(unit): add test suites for User and AuthenticateUserUseCase

- Add UserTest covering entity methods
- Add AuthenticateUserUseCaseTest with repository mock"
```
