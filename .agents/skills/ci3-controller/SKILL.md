---
name: ci3-controller
description: Use when creating or modifying CodeIgniter 3 controllers (presentation layer) following DDD-lite architecture.
---

# CI3 Controller Layer

## When to Use

Use this skill when creating or modifying:
- Controllers in `application/controllers/` (e.g. `auth/Auth.php`, `admin/Users.php`, `admin/Roles.php`, `student/Dashboard.php`).

## Location & Naming Conventions

- Controller files go in `application/controllers/<area>/` (e.g. `admin/`, `student/`, `auth/`).
- Class names do NOT use namespaces. CI3 loads controllers via path discovery.
- File names must be **PascalCase** (e.g. `Users.php`, `Roles.php`).
- Controller class names match the file name and **MUST extend `MY_Controller`** (e.g. `class Users extends MY_Controller`).

## Architecture Rules (DDD-Lite)

1. **Extend `MY_Controller`**: All application controllers extend `MY_Controller` (`application/core/MY_Controller.php`). Exception handling is managed globally via `_remap()`.
2. **Presentation Layer Only**: Controllers receive HTTP requests and return responses (views or JSON).
3. **No Business Logic**: All business rules, calculations, counting, or filtering belong in Use Cases (`app\usecases\...`). Controllers delegate logic to Use Cases.
4. **Form Flow (Single View Load)**: DO NOT create private `_handle_*()` helper methods that duplicate view assembly and `$this->load->view()`. Evaluate `$this->form_validation->run() === TRUE` directly in the action. View preparation (`$data`) and rendering happen **ONCE** at the end of the action.
5. **No Try/Catch for Standard Exceptions**: Uncaught semantic domain exceptions (`NotFoundException`, `ValidationException`, `ConflictException`, etc.) are intercepted by `MY_Controller::_remap()`, which returns JSON for AJAX or sets flashdata and redirects for HTML.
6. **Controllers Delegate to Use Cases**: Controllers instantiate and call Use Cases (`app\usecases\...`) to orchestrate business actions. Controllers MUST NOT interact with Mappers (`*Mapper`) or Database DTOs (`*Database`), which belong exclusively to the Infrastructure layer.
7. **Standardized Responses**:
   - For JSON output (e.g. DataTables, AJAX, API endpoints), ALWAYS use `json_response($data, $status_code)` from `response_helper.php`.
   - DO NOT call `$this->output->set_content_type('application/json')->set_output(json_encode(...))` manually.
   - For file downloads (such as PDF/DOCX), use response helpers when available (e.g., `response_pdf()`).
8. **Global Language Management**:
   - DO NOT call `$this->lang->load()` or check `HTTP_ACCEPT_LANGUAGE` in controllers.
   - Language detection and loading is handled globally via the `Language_check` hook (`post_controller_constructor`).
9. **Autoloaded Resources**:
   - DO NOT manually load `session`, `url`, `form`, `database`, or `response` helpers/libraries — they are registered in `$autoload`.
10. **No `$this->input->post('field', TRUE)` (Input XSS Filtering is Forbidden)**:
   - NEVER pass `TRUE` as the second parameter to `$this->input->post()`, `$this->input->get()`, or `$this->input->cookie()`.
   - Passing `TRUE` triggers CI3's internal regex-heavy XSS filtering on each captured field, making requests excessively heavy and slow.
   - Receiving raw data via form submissions is acceptable; XSS protection MUST be handled exclusively when outputting data to views using CodeIgniter's built-in `html_escape()` helper (or `htmlspecialchars()`).
   - Always capture form inputs cleanly without the second argument: `$this->input->post('name')`.

## Form Input Handling & XSS Strategy

### 1. Ingestion (Controllers): Never Use `TRUE`

Controllers must capture form and query parameters **without** passing `TRUE` as the second argument:

```php
// ✅ CORRECT: Fast, clean raw input retrieval
$name = $this->input->post('name');
$email = $this->input->post('email');
$role_ids = $this->input->post('role_ids');

// ❌ FORBIDDEN: Heavy regex processing degrades request performance
$name = $this->input->post('name', TRUE);
$email = $this->input->post('email', TRUE);
```

**Why is `TRUE` forbidden?**
- Passing `TRUE` invokes CodeIgniter's `$this->security->xss_clean()`.
- `xss_clean()` executes dozens of complex, CPU-intensive regular expressions on every single field.
- This creates severe latency on form submissions and API endpoints.
- Storing raw user input (even if it contains special characters or script tags) is standard practice and completely safe in application and database layers (SQL injection is prevented by parameterized queries/Query Builder).

### 2. Rendering (Views): Always Use `html_escape()`

XSS protection belongs exclusively to the **presentation/view output layer**. When displaying dynamic data in views, always escape it using CodeIgniter's native `html_escape()` helper:

```php
<!-- In view files (e.g. application/views/admin/users/form.php) -->
<input type="text" name="name" class="form-control" value="<?= html_escape($user ? $user->get_name() : set_value('name')); ?>">

<p class="user-display"><?= html_escape($user->get_email()); ?></p>
```

## Controller Pattern Example

```php
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\ListPaginatedUsersUseCase;
use app\usecases\admin\GetUserUseCase;
use app\usecases\admin\CreateUserUseCase;
use app\usecases\admin\UpdateUserUseCase;

/**
 * Users Controller (Admin)
 */
class Users extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * List users view.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'page_name' => 'admin/users/index',
			'title'     => 'Gestão de Usuários',
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Create user form and action.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

		if ($this->form_validation->run() === TRUE) {
			$use_case = new CreateUserUseCase();
			$use_case->execute(
				$this->input->post('name'),
				$this->input->post('email'),
				$this->input->post('password')
			);

			$this->session->set_flashdata('success', 'Usuário criado com sucesso.');
			redirect('admin/usuarios');
		}

		$data = [
			'page_name' => 'admin/users/form',
			'title'     => 'Novo Usuário',
			'user'      => null,
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * AJAX endpoint returning JSON.
	 *
	 * @return void
	 */
	public function ajax_data()
	{
		$use_case = new ListPaginatedUsersUseCase();
		$result = $use_case->execute(0, 10, '', 'name', 'ASC');

		json_response([
			'status' => 'success',
			'data'   => $result,
		]);
	}
}
```

---

## Anti-Patterns

❌ **Direct Infrastructure/Mapper Access**: Using Mappers (`UserMapper`) or Database DTOs (`UserDatabase`) in controllers. Controllers communicate solely with Use Cases and presentation views/JSON.
❌ **Business logic in Controller**: Calculating metrics, sorting/filtering domain objects, or validating domain invariants directly in controllers instead of delegating to Use Cases.
❌ **Direct Model manipulation**: Invoking model CRUD queries directly from controllers for business workflows instead of calling Use Cases.
❌ **Private `_handle_*` methods**: Duplicating `$data` assembly and view loading in private helpers instead of keeping a single `$this->load->view()` at the end of the action.
❌ **Manual Try/Catch for standard exceptions**: Catching `NotFoundException`, `ValidationException`, `ConflictException`, etc. inside controllers instead of letting `MY_Controller::_remap()` handle them globally.
❌ **Manual language loading**: Calling `$this->lang->load()` or checking `HTTP_ACCEPT_LANGUAGE` in controllers instead of relying on the global `Language_check` hook.
❌ **Manual JSON formatting**: Calling `$this->output->set_output(json_encode(...))` instead of `json_response($data, $status_code)`.
❌ **Input XSS Filtering (`$this->input->post('field', TRUE)`)**: Passing `TRUE` to `$this->input->post()`, `$this->input->get()`, etc. CI3's input XSS filter degrades request performance significantly. Capture inputs without `TRUE` and escape output when rendering in the view using the `html_escape()` helper.
❌ **Ignoring Global Styles**: All style rules (PSR-12, docblocks, strict typing, no single-letter variables) MUST follow the global conventions defined in `GEMINI.md`.
