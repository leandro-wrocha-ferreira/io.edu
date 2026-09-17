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
   - **Reusable Components:** `public/assets/js/components/` (reusable scripts such as delete confirmation, toasts, etc.). *Note: if this folder does not exist yet, create it when implementing your first reusable component.*
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

## 3. Modern Fetch & Custom Header Pattern

Instead of relying solely on the default CI3 `is_ajax_request()` check, we focus on native `fetch()` requests and use a custom header to instruct the backend to return JSON.

### Mandatory Fetch Standards:
1. **Centralization**: The `fetch()` utility function must NOT be rewritten in every `page_js` file. Abstract generic network requests (e.g., `submitData`, `apiFetch`) into a global file like `public/assets/js/components/http.js` and reuse it across pages.
2. **Custom JSON Header**: Always include a custom header (e.g., `'X-App-Response': 'json'`) in custom `fetch()` calls so the CI3 controller/MY_Controller knows it must return a JSON response instead of the default HTML flow.
3. **Always check `response.ok`**: The `fetch()` promise resolves on HTTP 4xx and 5xx. You must check `if (!response.ok)` before processing JSON data.
4. **Modern Form Data**: When submitting forms via Vanilla JS `fetch()`, use `new FormData(formElement)` natively. **DO NOT** use jQuery's `$(form).serialize()`.
5. **Manage Button Loading & Disabled States**: Always disable submit/action buttons during in-flight async requests to prevent duplicate submissions.

### The `http.js` Utility (public/assets/js/components/http.js):

When you need to make API calls, you MUST use or create the global `Http` utility. If the file doesn't exist, create it with this structure:

```javascript
/**
 * Global HTTP Utility wrapper for fetch.
 * File: public/assets/js/components/http.js
 */
const Http = {
    async post(url, payload, buttonEl = null) {
        if (buttonEl) {
            buttonEl.dataset.originalContent = buttonEl.innerHTML;
            buttonEl.disabled = true;
            buttonEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Processando...';
        }

        try {
            const isFormData = payload instanceof FormData;
            
            const headers = {
                'X-App-Response': 'json', // Custom header for JSON response
                'Accept': 'application/json'
            };
            
            if (!isFormData) {
                headers['Content-Type'] = 'application/json';
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: headers,
                body: isFormData ? payload : JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Erro na requisição (${response.status})`);
            }

            return result;
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        } finally {
            if (buttonEl) {
                buttonEl.disabled = false;
                buttonEl.innerHTML = buttonEl.dataset.originalContent;
            }
        }
    },
    // Add get, put, delete etc. as needed...
};
```

### Usage in Page Scripts (`public/assets/js/pages/...`):

In your specific page logic, DO NOT call `fetch()` directly. Use the `Http` utility:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('btn-save');
    const myForm = document.getElementById('my-form');

    if (saveBtn && myForm) {
        saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const payload = new FormData(myForm); // Use native FormData
            
            try {
                // Use the centralized Http utility
                const response = await Http.post(window.location.origin + '/admin/module/save', payload, saveBtn);
                
                alert('Success: ' + response.message);
                // Redirect or update UI...
            } catch (error) {
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
- [ ] **Custom JSON Header:** Every `fetch()` call includes the `'X-App-Response': 'json'` (or similar chosen custom header) to ensure JSON replies.
- [ ] **Native FormData:** Form submissions use `new FormData(element)` rather than jQuery `.serialize()`.
- [ ] **Centralized Network Logic:** `fetch` calls are abstracted into a shared file (e.g., `components/http.js`) rather than duplicated in `pages/`.
- [ ] **HTTP Response Status Check:** Code explicitly verifies `if (!response.ok)` before consuming the body.
- [ ] **Loading & Disabled States:** Buttons and interactive triggers are disabled during ongoing network calls.
- [ ] **Null Checks (Guards):** Scripts verify DOM element existence before manipulating them (`if (!tableEl) return;`).
- [ ] **Scoped Execution:** Event listeners are bound to `DOMContentLoaded` or scoped to page-specific IDs/classes.
- [ ] **Error Handling:** Promises and HTTP requests handle failures cleanly using `try/catch` or `.catch()`.

---

## Anti-Patterns

❌ **Inline Scripts in Views**: Placing `<script>` tags with JavaScript logic inside `.php` files in `application/views/`.
❌ **`fetch()` without Custom Header**: Calling `fetch()` without the custom JSON header (`X-App-Response: json`), causing the backend to return HTML/redirects instead of JSON.
❌ **jQuery Serialize for AJAX**: Using `$(form).serialize()` in new fetch calls instead of native `new FormData(form)`.
❌ **Ignoring `response.ok`**: Assuming HTTP 200 on every resolved `fetch()` promise without checking `response.ok`.
❌ **Unguarded DOM Selectors**: Executing queries like `document.getElementById('my-el').addEventListener(...)` without null checks (`if (!el) return;`).
❌ **Missing Loading / Disabled States**: Triggering asynchronous actions without disabling the trigger element, resulting in multiple concurrent submissions.

