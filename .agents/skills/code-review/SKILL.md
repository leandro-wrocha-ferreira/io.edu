---
name: code-review
description: Realiza code review seguindo as convenções de arquitetura DDD-lite do projeto e organiza commits agrupados por lógica semântica (Conventional Commits).
---

# Code Review & Architecture Compliance Skill

Use when performing code reviews against project architectural standards and verifying code health before merge or commit.

---

## Responsibilities

1. **Architecture & Code Review**: Analyze modified files (`git status` / `git diff`) against DDD-lite architectural conventions, PSR-12, docblock requirements, error handling, and test coverage.
2. **Severity-Classified Findings**: Report code review findings using an objective severity scale (`BLOCKER`, `HIGH`, `MEDIUM`, `LOW`).
3. **Commit Readiness**: Verify that code is ready for grouping into semantic commits (orchestrated via `.agents/workflows/git-committer.md`).

---

## Review Findings Severity Taxonomy

When reviewing code, classify all issues into one of the following four severity levels:

| Severity | Definition | Examples |
| :--- | :--- | :--- |
| **`BLOCKER`** | Critical violation that **blocks merging**. Must be resolved immediately. | - Business logic inside Controllers<br>- Direct framework/database coupling in Domain<br>- Incompatible PHP 8.2 method signatures (`delete`, `find_by_id`)<br>- Controller not extending `MY_Controller`<br>- Model not extending `MY_Model` |
| **`HIGH`** | High-impact bug, security defect, or missing contract. | - Generic `\RuntimeException` thrown for business rules<br>- Missing test suite for new Use Cases or Entities<br>- Incorrect PSR-4 namespace (e.g. `Application\` instead of `app\`)<br>- Unhandled exceptions causing 500 errors |
| **`MEDIUM`** | Architectural inconsistency or code quality issue. | - Portuguese docblocks instead of English<br>- PSR-12 formatting violation (braces on same line)<br>- Missing loading/disabled states on async AJAX buttons<br>- N+1 queries instead of batch relation hydration |
| **`LOW`** | Stylistic refinement or minor readability suggestion. | - Redundant inline comments<br>- Variable naming clarity<br>- Minor whitespace or docblock typo |

---

## Architecture Compliance Checklist

Verify every modified file against the project layers:

### 1. Presentation Layer (`application/controllers/`)
- [ ] Controller extends `MY_Controller` (`application/core/MY_Controller.php`).
- [ ] No namespaces used in controllers (CI3 path discovery).
- [ ] **Zero business logic**: Delegates orchestration to Use Cases (`app\usecases\...`).
- [ ] No direct model calls for business operations.
- [ ] No manual try/catch for semantic domain exceptions (handled by `_remap()`).
- [ ] Single view loading at the end of actions (no private `_handle_*` duplicating views).
- [ ] JSON responses use `json_response($data, $status_code)`.
- [ ] No manual `$this->lang->load()` (managed by `Language_check` hook).

### 2. Application Layer (`application/usecases/`)
- [ ] Uses namespace `app\usecases\<context>`.
- [ ] Constructor accepts Domain Repository Interface for dependency injection.
- [ ] Fallback constructor uses `Model_factory::make('model_name')`.
- [ ] Throws semantic exceptions from `app\domain\exceptions\` (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`). Never throws `\RuntimeException`.
- [ ] Returns Domain Entities, not raw database rows or arrays.
- [ ] Single public `execute()` method per Use Case.

### 3. Domain Layer (`application/domain/`)
- [ ] Uses namespace `app\domain\<context>`.
- [ ] **Pure PHP**: Zero CI3 dependencies (no `get_instance()`, `CI_Model`, `Model_factory`).
- [ ] Value Objects are immutable and throw `\InvalidArgumentException` on invalid scalar format.
- [ ] Repository interfaces declare compatible signatures (`find_by_id($id): ?Entity`, `delete(array $where): bool`).
- [ ] Entities do not persist timestamps (`created_at`/`updated_at` are managed by DB triggers).

### 4. Infrastructure Layer (`application/models/`)
- [ ] Models extend `MY_Model` and implement Domain Repository Interfaces.
- [ ] No namespace (CI3 path discovery).
- [ ] Timestamps (`created_at`, `updated_at`) are NEVER included in write payloads (`$data`).
- [ ] Uses KISS relation hydration (`_hydrate_user_roles`, `_hydrate_batch_user_roles`) instead of `GROUP_CONCAT` / `ANY_VALUE`.
- [ ] Filters are applied explicitly in methods (no magic `$before_get` global scopes).

### 5. Testing & Code Style
- [ ] Automated tests cover at least **80%** of application code.
- [ ] Test namespaces match PSR-4 (`app\domain\...` for domain imports).
- [ ] PSR-12 bracket style (`{` on the next line for classes and methods).
- [ ] Tabs for indentation (`.editorconfig`).
- [ ] All docblocks are in **English**.

---

## Git Operations & Commit Guidelines

Code review is separate from commit creation. Once code review passes:

1. **Semantic Groups**: Group files atomically by responsibility (`domain`, `usecases`, `infrastructure`, `presentation`, `config`, `tests`, `docs`).
2. **Conventional Commits**: `<type>(<scope>): <short description in English>`
3. **Dedicated Workflow**: When committing changes, follow `.agents/workflows/git-committer.md`.
4. **Tool Requirement**: All Git operations MUST use the project tool `git-leandro` (e.g. `git-leandro add <file>`, `git-leandro commit -m "..."`).

