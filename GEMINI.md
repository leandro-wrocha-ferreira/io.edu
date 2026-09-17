# AGENTS.md

## Project

CodeIgniter 3 project — Plataforma de Educação (LMS). PHP >= 8.2.

## Structure

- `index.php` — front controller; sets `ENVIRONMENT` via `CI_ENV` env var (default: `development`)
- `application/` — all app code: controllers, models, views, config, libraries, helpers
- `system/` — framework core (do not modify)
- `public/` — web root; serves `assets/css/` and `assets/js/` (Bootstrap files copied from vendor)
- `docker/` — Docker configs: nginx, php-fpm, startup script
- Default controller: `Welcome` (maps to `/`)

## Architecture: DDD Lite

O projeto segue **Domain-Driven Design simplificado** dentro do CI3.

### Camadas

```
Request → Controller → Use Case → Domain → Repository Interface → Model (CI3)
```

| Camada | Pasta | Responsabilidade |
|--------|-------|------------------|
| **Presentation** | `controllers/`, `views/` | Receber request, retornar response |
| **Application** | `usecases/` | Orquestrar regras de negócio |
| **Domain** | `domain/` | Entities, Value Objects, Interfaces |
| **Infrastructure** | `models/` | Implementar interfaces (DB, APIs) |

### Regras

- **Controllers** extend `MY_Controller` (in `application/core/MY_Controller.php`).
- **Controllers** delegam lógica para Use Cases, nunca direto para Models.
- **Controllers** NÃO devem conter regra de negócio — toda lógica de contagem, filtro ou processamento pertence ao Use Case.
- **Controllers (Form Flow)**: Sem métodos privados `_handle_*()` duplicando carregamento de views. Checar `$this->form_validation->run() === TRUE` diretamente na ação. A montagem do `$data` e a renderização da view acontecem em ponto único no final do método.
- **Tratamento Global de Exceções**: `MY_Controller::_remap()` intercepta todas as chamadas de ações. Exceções não capturadas são tratadas automaticamente:
  - **Requisições AJAX**: Retornam JSON via `json_response()` com código HTTP equivalente (404, 409, 422, 500).
  - **Requisições HTML**: Alerta via flashdata / página 404 amigável.
- **Exceções Semânticas**: Lançar exceções de `app\domain\exceptions\` (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`) nos use cases em vez de `\RuntimeException` genérica.
- **Controllers** NÃO devem carregar idiomas (`$this->lang->load()`) nem checar `HTTP_ACCEPT_LANGUAGE` manualmente — a detecção e carregamento de idioma é gerenciada globalmente via hook `Language_check` no `post_controller_constructor` com fallback para `english`.
- **Models** ficam em `application/models/` (lowercase) — CI3 requer esta convenção.
- **Controllers** ficam em `application/controllers/` com subdiretórios (`auth/`, `admin/`, `student/`).
- Controllers NÃO devem usar namespaces — CI3 carrega por path discovery.

## Naming Conventions

| Camada | Padrão | Exemplo |
|--------|--------|---------|
| Controllers | `Name_controller` (sem namespace) | `Auth.php` em `controllers/auth/` |
| Models | `Name_model` (sem namespace) | `User_model.php` em `models/` |
| Use Cases | `VerbNounUseCase` (com namespace) | `AuthenticateUserUseCase.php` |
| Entities | `PascalCase` (com namespace) | `User.php`, `Course.php` |
| Value Objects | `PascalCase` (com namespace) | `Email.php`, `Role.php` |
| Interfaces | `NameInterface` (com namespace) | `UserRepositoryInterface.php` |
| Factories | `PascalCase` (com namespace) | `Model_factory.php` |

## PSR-4 Autoloading

O projeto usa Composer PSR-4 para autoloading:

```json
"autoload": {
    "psr-4": {
        "app\\": "application/"
    }
},
"autoload-dev": {
    "classmap": [
        "tests/"
    ]
}
```

### Namespaces

| Camada | Namespace | Exemplo |
|--------|-----------|---------|
| Domain | `app\domain\<context>` | `app\domain\identity\User` |
| Use Cases | `app\usecases\<context>` | `app\usecases\identity\AuthenticateUserUseCase` |
| Factories | `app\factories` | `app\factories\Model_factory` |

### Notas Importantes

- **Controllers e Models** NÃO têm namespace — CI3 os carrega via path discovery.
- Para usar classes com namespace em controllers/models: `use app\...\ClassName;`
- CI3 carrega models de `application/models/` (lowercase) — NÃO renomear para `Models/`.
- Composer autoloader é carregado via `$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';`
- **Diretórios** ficam em lowercase para manter convenção CI3: `domain/`, `usecases/`, `factories/`
- **Arquivos PHP** devem ter nome PascalCase para bater com o nome da classe (obrigação PSR-4)

## Code Style

### PSR-12 Bracket Style

Chaves `{` na **próxima linha** para classes e métodos:

```php
class User
{
    public function get_id(): ?int
    {
        return $this->id;
    }
}
```

### Docblocks

Todas as classes, métodos e parâmetros devem ter docblocks:

```php
/**
 * Cria um novo usuário.
 *
 * @param string $name Nome do usuário
 * @param Email $email Email do usuário (Value Object)
 * @param string $password Senha em texto plano
 * @return self
 */
public static function create(string $name, Email $email, string $password): self
```

### Indentation

- **Tabs** para indentação (per `.editorconfig`)
- **Line endings:** LF
- **Charset:** UTF-8

### No Vertical Alignment

- **NÃO** utilize alinhamento vertical (Smart Alignment / Column Alignment) para arrays ou atribuições. 
- O uso de espaços extras apenas para alinhar símbolos como `=>` ou `=` é **proibido**, pois gera diffs ruidosos no controle de versão.
- Siga a PSR-12: utilize apenas **um espaço** antes e depois do símbolo.

### No Single-Letter Variables

- **NÃO** utilize variáveis de uma única letra (como `$i`, `$k`, `$v`, `$u`, etc.).
- Variáveis devem ser **sempre descritivas**, mesmo em loops ou em testes. Exemplo: use `$index` em vez de `$i`, `$user` em vez de `$u`.

Exemplo correto:
```php
$data = [
    'id' => 123,
    'nome_longo' => 'João Silva',
    'content' => $content
];
```

## Presentation Layer (Controllers)

Controller architecture, routing rules, and presentation flow are strictly delegated to the `ci3-controller` skill.
**Before writing, modifying, or reviewing any controllers, you MUST read:**
`.agents/skills/ci3-controller/SKILL.md`

## Domain Layer

Domain rules (Entities, Value Objects, Exceptions, Repository Interfaces) are strictly delegated to the `ci3-domain` skill.
**Before writing, modifying, or reviewing any domain code, you MUST read:**
`.agents/skills/ci3-domain/SKILL.md`

## Use Case Layer

Use Case orchestration and dependency injection rules are strictly delegated to the `ci3-usecase` skill.
**Before writing, modifying, or reviewing any use cases, you MUST read:**
`.agents/skills/ci3-usecase/SKILL.md`

## Infrastructure Layer (Models)

Database persistence, Query Builder usage, and explicit filtering are strictly delegated to the `ci3-model` skill.
**Before writing, modifying, or reviewing any models, you MUST read:**
`.agents/skills/ci3-model/SKILL.md`

## UI Design Layer

UI components, styling, centralized palette (Light/Dark mode), and layout rules are strictly delegated to the `ci3-ui` skill.
**Before writing, modifying, or reviewing any UI code, you MUST read:**
`.agents/skills/ci3-ui/SKILL.md`

## Auth Pattern

- **Session-based** com CI3 session (driver: database)
- **Password hashing:** `password_hash(PASSWORD_BCRYPT)` / `password_verify()`
- **RBAC:** users → roles → permissions (tabelas `users`, `roles`, `permissions`, `user_roles`, `role_permissions`)
- **Middleware via CI3 Hooks:** verificar sessão no `post_controller_constructor` hook

### Hook Configuration

```php
// application/config/hooks.php
$hook['post_controller_constructor'][] = [
    'class'    => 'Language_check',
    'function' => 'detect',
    'filename' => 'Language_check.php',
    'filepath' => 'hooks'
];

$hook['post_controller_constructor'][] = [
    'class'    => 'Middleware',
    'function' => 'validate',
    'filename' => 'Middleware.php',
    'filepath' => 'hooks'
];
```

### Auth & RBAC Middleware Hook

```php
// application/hooks/Middleware.php
class Middleware
{
    public function validate()
    {
        $CI =& get_instance();
        $CI->load->library('session');

        // Middleware valida autenticação e permissões RBAC no banco
        // Rotas públicas ('entrar', 'sair', 'welcome') não requerem sessão
    }
}
```

## Routing

- Rotas são kebab-case em português
- Controllers em subdiretórios: `auth/Auth`, `admin/Dashboard`, `student/Dashboard`
- `$config['index_page'] = ''` (URLs sem `index.php`)

```php
// application/config/routes.php
$route['autenticacao/login'] = 'auth/auth/login';
$route['autenticacao/sair'] = 'auth/auth/logout';
$route['admin/painel'] = 'admin/dashboard/index';
$route['aluno/painel'] = 'student/dashboard/index';
```

## Database Conventions

- **Tabelas:** plural, snake_case, sem prefixo (`users`, `courses`, `lessons`)
- **Colunas padrão:** `id` (INT unsigned AI), `created_at`, `updated_at`, `deleted_at` (soft delete)
- **Migrations:** timestamp `YYYYMMDDHHIISS_name`, classe `Migration_Create_<table>`
- **Foreign keys:** migration separada, naming `fk_table_column`
- **Driver:** `mysqli`, Query Builder habilitado

## Docker

Stack: **PHP 8.2-fpm + Nginx + MySQL 8.0**

```bash
docker compose up -d          # start app + db (app on port defined in APP_PORT, default 8080)
docker compose down            # stop containers
```

- `.env` holds DB credentials, `APP_PORT`, and UID/GID for the container user
- `docker/app/start.sh` runs `composer install` then starts nginx + php-fpm
- DB data persisted in a named volume (`db_data`)

## Dev commands

```bash
docker compose exec app composer install          # install deps
docker compose exec app composer dump-autoload    # regenerate autoloader (required after creating tests/mocks via classmap)
docker compose exec app vendor/bin/phpunit tests/unit/  # run unit tests
```

## Testing Strategy

Testing rules (Unit Tests, 1:1 Use Case mapping, Shared Mocks, classmap namespaces) are strictly delegated to the `ci3-test` skill.
**Before writing, modifying, or reviewing any tests, you MUST read:**
`.agents/skills/ci3-test/SKILL.md`

## Conventions

- **Indentation:** tabs (per `.editorconfig`)
- **Line endings:** LF
- **Charset:** UTF-8
- **Database:** `mysqli` driver, Query Builder enabled; credentials read from env vars (`DB_HOSTNAME`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`, `DB_DRIVER`) with localhost fallback
- **Autoloads:** database, migration, form_validation, and session libraries auto-loaded; url, form, and response helpers auto-loaded (`application/config/autoload.php`)
- **JSON & File Responses:** controllers MUST send JSON responses using `json_response($data, $status_code)` from `response_helper.php`. For file downloads (such as PDF/DOCX), use dedicated response helpers (e.g. `response_pdf()`).
- **Composer PSR-4:** `app\` → `application/`
- **Routing:** `translate_uri_dashes` is OFF; controller methods map directly to URL segments
- **Frontend:** Bootstrap 5.3.8 (via composer); CSS/JS copied to `public/assets/` on install/update
- **UI Animations**: Todas as views de página DEVEM aplicar as classes de animação de entrada (`animate-fade-up` no `.page-header` e `.animate-fade-up.animate-delay-1` nos contêineres principais de cards/formulários/tabelas).
- **Cobertura de Testes:** Mínimo de 80% de cobertura de código em testes automatizados.
- **Hooks:** habilitados para middleware de autenticação via `post_controller_constructor`
- **PSR-12:** `{` on next line for classes and methods
- **Docblocks:** obrigatórios em todas as classes e métodos
- **Directories:** lowercase para manter convenção CI3 (`domain/`, `usecases/`, `factories/`)
- **Files:** PascalCase para classes namespaced (obrigação PSR-4)
- **Comandos Git & Push:** Todas as operações do Git (status, add, commit, diff, log, push) DEVEM SEMPRE ser executadas utilizando o comando `git-leandro` (ex: `git-leandro push -u origin master`).

## Gotchas

- `vendor/` is gitignored; must `composer install` before running tests
- `application/cache/*` and `application/logs/*` are gitignored (keep `index.html` placeholders)
- Post-install/update scripts: patch `vfsStream.php` for PHP 8+ compat **and** copy Bootstrap assets to `public/assets/` — don't skip `composer install`
- `.env` is gitignored; copy from `.env` or create manually for local/Docker setup
- CI3 models precisam de `parent::__construct()` no construtor
- Models ficam em `application/models/` (lowercase) — CI3 requer esta convenção
- Controllers não usam namespaces — CI3 carrega por path discovery
- Para acessar classes com namespace: `use app\domain\identity\User;`
- Domain entities NÃO devem chamar `get_instance()` — dependência pura
- Use cases usam `Model_factory::make()` em vez de `get_instance()`
- Rodar `composer dump-autoload` após adicionar/modificar classes namespaced
- Hooks `pre_controller` rodam ANTES do controller — usar `post_controller_constructor` para middleware
