---
name: ci3-js
description: Use when creating, modifying or organizing JavaScript files and logic. Enforces modularity, page-specific scripts, hybrid JS guidelines (jQuery vs Vanilla JS), and CI3 AJAX rules.
---

# JavaScript Guidelines

This project uses a modular, clean, and hybrid approach for organizing JavaScript files and logic.

---

## 1. File & Directory Organization

1. **No Inline Scripts in Views**:
   - It is **strictly prohibited** to place `<script>` blocks with business logic or component initialization directly inside `.php` view files in `application/views/`.
   - All logic must reside in static `.js` files within the `public/assets/js/` structure.

2. **Folder Structure**:
    - **Global/Theme:** `public/assets/js/theme.js` (essential scripts loaded in `<head>` before DOM render).
    - **Global HTTP Client:** `public/assets/js/http.js` (centralized HTTP Fetch client loaded in base layout).
    - **Layout/Module:** `public/assets/js/admin/layout.js` (global logic for menu, sidebar, and modals).
    - **Reusable Components:** `public/assets/js/components/` (reusable scripts such as delete confirmation, toasts, etc.).
    - **Page-Specific:** `public/assets/js/pages/<module>/<controller>/<action>.js` (exclusive view logic loaded dynamically via `$page_js` in the controller).

3. **Dynamic Page Loading (`$page_js`)**:
   - In Controller:
     ```php
     $data = [
         'page_name' => 'admin/users/index',
         'title' => 'User Management',
         'page_js' => ['admin/users/index.js'], // relative to public/assets/js/pages/
     ];
     $this->load->view('admin/index', $data);
     ```
   - In Base Layout (`admin/index.php`):
     ```php
     <?php if (!empty($page_js)): ?>
         <?php foreach ((array)$page_js as $js): ?>
             <script src="<?= base_url('public/assets/js/pages/' . $js) ?>"></script>
         <?php endforeach; ?>
     <?php endif; ?>
     ```

---

## 2. Hybrid JS Strategy (jQuery vs Vanilla JS)

The project adopts a **Hybrid Strategy** to balance the convenience of legacy components with the performance and modern capabilities of native JavaScript (ES6+).

### When to Use jQuery:
- **DataTables:** Initialization, column configuration, sorting, and integration with jQuery plugins.
- **Concise DOM Manipulation:** Quick operations on existing selectors when jQuery syntax saves substantial boilerplate.

### When to Use Vanilla JS (ES6+):
- **Layout & Theme Scripts:** Sidebar toggle, theme switcher, backdrop handlers, and animations.
- **Custom Asynchronous Requests:** All custom asynchronous operations via `Http` (`public/assets/js/http.js`).
- **Modern Web APIs:** `IntersectionObserver`, `localStorage`, `sessionStorage`, `CustomEvent`.

---

## 3. Modern Fetch & Custom Header Pattern

Instead of relying solely on the default CI3 `is_ajax_request()` check or scattered raw `fetch()` calls, all frontend network requests MUST use the centralized native client located at `public/assets/js/http.js`.

### Mandatory Fetch Standards:
1. **Centralized Client**: Raw `fetch()` or jQuery `$.ajax` calls are **strictly prohibited** in page scripts. Always use the global `Http` (or `HttpClient`) utility.
2. **Canonical Standard Headers**: The client automatically attaches:
   - `X-App-Json: application/json` (canonical header instructing `MY_Controller` to process and respond as JSON)
   - `X-Requested-With: XMLHttpRequest` (identifies request as an AJAX operation)
   - `Accept: application/json`
3. **Automatic CSRF Handling**: Automatically resolves the CSRF token from `<meta name="csrf-token">` or `csrf_cookie_name` cookie and injects it via `X-CSRF-TOKEN` header (and inside `FormData` when applicable).
4. **Data Formats**:
   - **JSON Objects**: Automatically serialized with `Content-Type: application/json; charset=utf-8`.
   - **FormData**: Native `FormData` supported without overriding the multipart boundary.
5. **Button Loading & Disabled States**: Pass the button element directly or via `{ button: buttonEl }` to automatically disable the trigger and render an animated spinner (`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processando...`) during request execution.
6. **HTTP Error Handling**: Rejects automatically on status >= 400, parsing structured error responses (`result.message` or `result.error`) for easy consumption in `try/catch` blocks.

### The `Http` Client Interface (`public/assets/js/http.js`):

Available methods on `window.Http`:
- `Http.get(url, options = {})`
- `Http.post(url, data, optionsOrButton = {})`
- `Http.put(url, data, optionsOrButton = {})`
- `Http.patch(url, data, optionsOrButton = {})`
- `Http.delete(url, options = {})`
- `Http.request(url, options = {})`

### Usage in Page Scripts (`public/assets/js/pages/...`):

In your specific page logic, use `Http` directly:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('btn-save');
    const userForm = document.getElementById('form-user');

    if (saveBtn && userForm) {
        userForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = new FormData(userForm);

            try {
                // Pass button element as 3rd parameter to manage loading/disabled states
                const response = await Http.post(userForm.action, payload, saveBtn);

                if (response.success) {
                    window.location.href = response.redirect || '/admin/users';
                }
            } catch (error) {
                console.error('Request failed:', error);
                alert(error.message || 'Ocorreu um erro ao processar a solicitação.');
            }
        });
    }
});
```

---

## 4. JavaScript Code Review Checklist

When creating or modifying JavaScript code, verify the following:

- [ ] **Zero Inline Scripts:** The `.php` view contains no inline `<script>` blocks with business logic or library initializations.
- [ ] **Centralized HTTP Client:** Asynchronous requests use exclusively the centralized `Http` client (`public/assets/js/http.js`), with zero ad-hoc `fetch()` or jQuery AJAX calls.
- [ ] **Canonical Headers:** The client automatically issues `X-App-Json: application/json` and `X-Requested-With: XMLHttpRequest` (obsolete headers like `X-App-Response` are prohibited).
- [ ] **Native FormData:** Form submissions use `new FormData(element)` rather than jQuery `.serialize()`.
- [ ] **Loading & Disabled States:** Buttons and interactive triggers are disabled with spinner feedback during ongoing network calls.
- [ ] **Null Checks (Guards):** Scripts verify DOM element existence before manipulating them (`if (!tableEl) return;`).
- [ ] **Scoped Execution:** Event listeners are bound to `DOMContentLoaded` or scoped to page-specific IDs/classes.
- [ ] **Clean Error Handling:** Network calls handle exceptions cleanly using `try/catch` blocks.

---

## Anti-Patterns

❌ **Inline Scripts in Views**: Placing `<script>` tags with JavaScript logic inside `.php` files in `application/views/`.
❌ **Ad-hoc `fetch()` or `$.ajax()` Calls**: Invoking raw `fetch()` or jQuery AJAX directly in page scripts instead of using `public/assets/js/http.js`.
❌ **Obsolete Headers (`X-App-Response`)**: Using deprecated header names instead of the canonical `X-App-Json: application/json`.
❌ **jQuery Serialize for AJAX**: Using `$(form).serialize()` instead of native `new FormData(form)`.
❌ **Ignoring Button States**: Triggering asynchronous actions without disabling the trigger element, leading to duplicate submissions.
❌ **Unguarded DOM Selectors**: Executing queries like `document.getElementById('my-el').addEventListener(...)` without null checks (`if (!el) return;`).

