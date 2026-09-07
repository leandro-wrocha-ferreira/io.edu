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
- **Custom `fetch()` Calls:** Asynchronous requests that do not go through DataTables abstractions.
- **Modern Web APIs:** `IntersectionObserver`, `localStorage`, `sessionStorage`, `CustomEvent`.

---

## 3. CI3 Golden Rule for AJAX & Fetch Pattern

In CodeIgniter 3, `$this->input->is_ajax_request()` checks for the presence of the HTTP header:
`HTTP_X_REQUESTED_WITH === 'xmlhttprequest'`

### Mandatory Fetch Standards:
1. **Always include `'X-Requested-With': 'XMLHttpRequest'`** in custom `fetch()` calls.
2. **Always check `response.ok`**: The `fetch()` promise resolves on HTTP 4xx and 5xx. You must check `if (!response.ok)` before processing JSON data.
3. **Manage Button Loading & Disabled States**: Always disable submit/action buttons during in-flight async requests to prevent duplicate submissions.

### Standard Async/Await Pattern:

```javascript
async function submitData(buttonEl, url, payload) {
    if (!buttonEl) return;

    const originalContent = buttonEl.innerHTML;
    buttonEl.disabled = true;
    buttonEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Processando...';

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest', // MANDATORY for CI3
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || `Erro na requisição (${response.status})`);
        }

        return result;
    } catch (error) {
        console.error('Fetch error:', error);
        alert(error.message || 'Ocorreu um erro ao processar a solicitação.');
        throw error;
    } finally {
        buttonEl.disabled = false;
        buttonEl.innerHTML = originalContent;
    }
}
```

---

## 4. JavaScript Code Review Checklist

When creating or modifying JavaScript code, verify the following:

- [ ] **Zero Inline Scripts:** The `.php` view contains no inline `<script>` blocks with business logic or library initializations.
- [ ] **AJAX Header Present:** Every `fetch()` call includes the `'X-Requested-With': 'XMLHttpRequest'` header.
- [ ] **HTTP Response Status Check:** Code explicitly verifies `if (!response.ok)` before consuming the body.
- [ ] **Loading & Disabled States:** Buttons and interactive triggers are disabled during ongoing network calls.
- [ ] **Null Checks (Guards):** Scripts verify DOM element existence before manipulating them (`if (!tableEl) return;`).
- [ ] **Scoped Execution:** Event listeners are bound to `DOMContentLoaded` or scoped to page-specific IDs/classes.
- [ ] **Error Handling:** Promises and HTTP requests handle failures cleanly using `try/catch` or `.catch()`.

---

## Anti-Patterns

❌ **Inline Scripts in Views**: Placing `<script>` tags with JavaScript logic inside `.php` files in `application/views/`.
❌ **`fetch()` without `X-Requested-With`**: Calling `fetch()` without `'X-Requested-With': 'XMLHttpRequest'`, causing CI3 to fail `is_ajax_request()` and return redirect/HTML instead of JSON.
❌ **Ignoring `response.ok`**: Assuming HTTP 200 on every resolved `fetch()` promise without checking `response.ok`.
❌ **Unguarded DOM Selectors**: Executing queries like `document.getElementById('my-el').addEventListener(...)` without null checks (`if (!el) return;`).
❌ **Missing Loading / Disabled States**: Triggering asynchronous actions without disabling the trigger element, resulting in multiple concurrent submissions.

