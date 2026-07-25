---
name: ci3-js
description: Use when creating, modifying or organizing JavaScript files and logic. Enforces modularity and global reusability rules for scripts in the public directory.
---

# JavaScript Guidelines

This project uses a modular and global approach for organizing JavaScript files.

## Mandatory Rules

1. **No Complex Inline Scripts**:
   - DO NOT use `<script>` blocks with extensive logic directly in views (`.php`).
   - Logic must always be extracted to static `.js` files in the `public/assets/js/` folder.

2. **Module Organization**:
   - Scripts belonging to a specific context or module (e.g. admin, student, auth, courses) must be grouped in corresponding subdirectories.
   - Example: `public/assets/js/admin/layout.js` (exclusive logic for the admin area).

3. **Global Scripts at Root**:
   - Scripts that share behavior across multiple modules (e.g. theme toggler, generic validations, mask formatting) should be placed at the root of the `js/` folder and well-named for easy discovery.
   - Example: `public/assets/js/theme.js` (handles dark mode in both admin and student).
   - Global scripts must be built considering the absence of DOM elements (use `if (!element) return;` or ensure node existence before binding events).

4. **Inclusion in Views**:
   - When including scripts in views, always use the `base_url()` function:
     `<script src="<?= base_url('public/assets/js/module/file.js') ?>"></script>`
   - For visual scripts that prevent FOUC (Flash of Unstyled Content), such as the theme, include them in the `<head>` tag. Other behavioral scripts should go at the end of `<body>`.
