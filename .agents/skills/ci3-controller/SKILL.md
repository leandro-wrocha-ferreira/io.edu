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
6. **No Direct Model Manipulation for Business Rules**: Controllers load models in `__construct()` using lowercase (`$this->load->model('user_model')`), but invoke Use Cases to perform actions.
7. **Standardized Responses**:
   - For JSON output (e.g. DataTables, AJAX, API endpoints), ALWAYS use `json_response($data, $status_code)` from `response_helper.php`.
   - DO NOT call `$this->output->set_content_type('application/json')->set_output(json_encode(...))` manually.
   - For file downloads (such as PDF/DOCX), use response helpers when available (e.g., `response_pdf()`).
8. **Global Language Management**:
   - DO NOT call `$this->lang->load()` or check `HTTP_ACCEPT_LANGUAGE` in controllers.
   - Language detection and loading is handled globally via the `Language_check` hook (`post_controller_constructor`).
9. **Autoloaded Resources**:
   - DO NOT manually load `session`, `url`, `form`, `database`, or `response` helpers/libraries — they are registered in `$autoload`.

## Controller Pattern Example

```php
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\ListUsersUseCase;
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
        $this->load->model('user_model');
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

        $this->load->view('admin/index', $data);
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
                $this->input->post('name', TRUE),
                $this->input->post('email', TRUE),
                $this->input->post('password', TRUE)
            );

            $this->session->set_flashdata('success', 'Usuário criado com sucesso.');
            redirect('admin/usuarios');
        }

        $data = [
            'page_name' => 'admin/users/form',
            'title'     => 'Novo Usuário',
            'user'      => null,
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * AJAX endpoint returning JSON.
     *
     * @return void
     */
    public function ajax_data()
    {
        $use_case = new ListUsersUseCase();
        $result = $use_case->execute();

        json_response([
            'status' => 'success',
            'data'   => $result,
        ]);
    }
}
```
