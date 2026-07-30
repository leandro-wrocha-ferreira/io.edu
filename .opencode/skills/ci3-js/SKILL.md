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

## 3. CI3 Golden Rule for AJAX

In CodeIgniter 3, `$this->input->is_ajax_request()` checks for the presence of the HTTP header:
`HTTP_X_REQUESTED_WITH === 'xmlhttprequest'`

### Mandatory Guidelines:
1. **All asynchronous frontend communication MUST be treated as an AJAX request.**
2. When using `fetch()`, it is **MANDATORY** to explicitly include the `'X-Requested-With': 'XMLHttpRequest'` header:
   ```javascript
   fetch('/admin/usuarios/dados', {
       method: 'GET',
       headers: {
           'X-Requested-With': 'XMLHttpRequest', // MANDATORY for CI3 detection
           'Accept': 'application/json'
       }
   })
   .then(response => response.json())
   .then(data => { /* ... */ });
   ```
3. `jQuery.ajax()` includes this header automatically. Omission in `fetch()` calls causes CodeIgniter 3 to process the request as a standard HTML page load, breaking the expected flow.

---

## 4. JavaScript Code Review Checklist

When creating or modifying JavaScript code, verify the following:

- [ ] **Zero Inline Scripts:** The `.php` view contains no inline `<script>` blocks with business logic or library initializations.
- [ ] **AJAX Header Present:** Every `fetch()` call includes the `'X-Requested-With': 'XMLHttpRequest'` header.
- [ ] **Null Checks (Guards):** Scripts verify DOM element existence before manipulating them (`if (!tableEl) return;`).
- [ ] **Scoped Execution:** Event listeners are bound to `DOMContentLoaded` or scoped to page-specific IDs/classes.
- [ ] **Error Handling:** Promises and HTTP requests handle failures cleanly using `.catch()` or `try/catch` blocks.
