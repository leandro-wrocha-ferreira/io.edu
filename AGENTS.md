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

- **Domain** NÃO depende de CI3. Sem `get_instance()`, sem `CI_Model`.
- **Use Cases** usam `Model_factory` para carregar models (não `get_instance()` direto).
- **Models** implementam interfaces do domain (`implements UserRepositoryInterface`).
- **Controllers** delegam lógica para Use Cases, nunca direto para Models.
- **Controllers** NÃO devem conter regra de negócio — toda lógica de contagem, filtro ou processamento pertence ao Use Case.
- **Controllers** NÃO devem carregar `session`, `url` ou `form` manualmente — já estão no autoload.
- **Controllers** NÃO devem carregar idiomas (`$this->lang->load()`) nem checar `HTTP_ACCEPT_LANGUAGE` manualmente — a detecção e carregamento de idioma é gerenciada globalmente via hook `Language_check` no `post_controller_constructor` com fallback para `english`.
- **Controllers** carregam models no `__construct()` usando **lowercase** (ex: `$this->load->model('user_model')`).
- **Models** NÃO devem setar `created_at`/`updated_at` — isso é responsabilidade do banco via triggers.
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
    "psr-4": {
        "Tests\\": "tests/"
    }
}
```

### Namespaces

| Camada | Namespace | Exemplo |
|--------|-----------|---------|
| Domain | `app\domain\<context>` | `app\domain\identity\User` |
| Use Cases | `app\usecases\<context>` | `app\usecases\identity\AuthenticateUserUseCase` |
| Factories | `app\factories` | `app\factories\Model_factory` |
| Tests | `Tests\<Type>` | `Tests\Acceptance\LoginCest` |

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

All classes, methods and parameters MUST have docblocks — **always in English**:

```php
/**
 * Create a new user.
 *
 * @param string $name User's name
 * @param Email $email User's email (Value Object)
 * @param string $password Plain text password
 * @return self
 */
public static function create(string $name, Email $email, string $password): self
```

### Indentation

- **Tabs** para indentação (per `.editorconfig`)
- **Line endings:** LF
- **Charset:** UTF-8

## Domain Patterns

### Entity

```php
// application/domain/identity/User.php
namespace app\domain\identity;

class User
{
    private $id;
    private $name;
    private $email;  // Value Object

    public static function create(string $name, Email $email, string $password): self
    {
        $user = new self();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->created_at = new \DateTime();
        return $user;
    }
}
```

### Value Object

```php
// application/domain/identity/Email.php
namespace app\domain\identity;

class Email
{
    private $value;

    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email");
        }
        $this->value = $email;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

### Repository Interface

```php
// application/domain/identity/UserRepositoryInterface.php
namespace app\domain\identity;

interface UserRepositoryInterface
{
    public function find_by_id(int $id): ?User;
    public function find_by_email(Email $email): ?User;
    public function save(User $user): void;
}
```

### Model Factory

```php
// application/factories/Model_factory.php
namespace app\factories;

class Model_factory
{
    public static function make(string $name)
    {
        $CI =& get_instance();
        $CI->load->model($name);
        return $CI->{$name};
    }
}
```

## Use Case Pattern

```php
// application/usecases/identity/AuthenticateUserUseCase.php
namespace app\usecases\identity;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\factories\Model_factory;

/**
 * Use case for authenticating a user in the system.
 */
class AuthenticateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\identity\UserRepositoryInterface|null $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->user_repository = $repository;
        } else {
            $this->user_repository = Model_factory::make('user_model');
        }
    }

    /**
     * Execute authentication.
     *
     * @param string $email User email
     * @param string $password Plain text password
     * @return User Authenticated user
     * @throws \RuntimeException When invalid credentials
     */
    public function execute(string $email, string $password): User
    {
        $user = $this->user_repository->find_by_email(new Email($email));
        if ($user === null) {
            throw new \RuntimeException("Invalid credentials");
        }
        if (!$user->verify_password($password)) {
            throw new \RuntimeException("Invalid credentials");
        }
        return $user;
    }
}
```

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
    'class'    => 'Auth_check',
    'function' => 'check',
    'filename' => 'Auth_check.php',
    'filepath' => 'hooks'
];
```

### Auth Check Hook

```php
// application/hooks/Auth_check.php
class Auth_check
{
    public function check()
    {
        $CI =& get_instance();
        $CI->load->library('session');

        $uri = $CI->uri->segment(1);
        $public_routes = ['autenticacao', 'welcome'];

        if (in_array($uri, $public_routes)) {
            return;
        }

        if (!$CI->session->userdata('logged_in')) {
            redirect('autenticacao/login');
        }

        if ($uri === 'admin' && $CI->session->userdata('user_role') !== 'admin') {
            show_404();
        }

        if ($uri === 'aluno' && $CI->session->userdata('user_role') !== 'student') {
            show_404();
        }
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
- **Timestamps:** `created_at` e `updated_at` são gerenciados por **triggers do banco** (INSERT/UPDATE). Models NÃO devem setar essas colunas.
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
composer install          # install deps (phpunit 4-9, vfsstream, bootstrap, codeception)
composer dump-autoload    # regenerate PSR-4 autoloader after adding/changing classes
composer test:coverage    # run tests with coverage (sqlite config)
vendor/bin/phpunit        # run all PHPUnit tests (unit + integration)
vendor/bin/codecept run acceptance  # run Codeception E2E tests
```

## Testing Strategy

### Unit Tests

- Testam domínio e use cases isolados
- Sem banco, sem HTTP
- Mocks para repositories
- Pasta: `tests/unit/`

### Integration Tests

- Testam models com banco real (SQLite)
- Verificam queries e persistência
- Pasta: `tests/integration/`

### E2E Tests (Codeception)

- Codeception para fluxos completos (PHP nativo)
- Login, navegação, CRUD
- Pasta: `tests/acceptance/`

### Comandos

```bash
vendor/bin/phpunit                          # rodar todos os testes PHPUnit
vendor/bin/phpunit tests/unit/              # só unit
vendor/bin/phpunit tests/integration/       # só integration
vendor/bin/codecept run acceptance           # e2e
```

## Conventions

- **Indentation:** tabs (per `.editorconfig`)
- **Line endings:** LF
- **Charset:** UTF-8
- **Database:** `mysqli` driver, Query Builder enabled; credentials read from env vars (`DB_HOSTNAME`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`, `DB_DRIVER`) with localhost fallback
- **Autoloads:** database, migration, form_validation, and session libraries auto-loaded; url and form helpers auto-loaded (`application/config/autoload.php`)
- **Composer PSR-4:** `app\` → `application/`, `Tests\` → `tests/`
- **Routing:** `translate_uri_dashes` is OFF; controller methods map directly to URL segments
- **Frontend:** Bootstrap 5.3.8 (via composer); CSS/JS copied to `public/assets/` on install/update
- **Hooks:** enabled for auth middleware via `post_controller_constructor`
- **PSR-12:** `{` on next line for classes and methods
- **Docblocks:** mandatory on all classes and methods — **always in English**
- **IDE Helper:** `_ide_helper.php` at project root provides type resolution for CI3 core classes. Do NOT add `@property` annotations to individual models or controllers — they are inherited from the base class stubs.
- **Directories:** lowercase para manter convenção CI3 (`domain/`, `usecases/`, `factories/`)
- **Files:** PascalCase para classes namespaced (obrigação PSR-4)

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
