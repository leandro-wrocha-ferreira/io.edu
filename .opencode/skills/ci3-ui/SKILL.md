---
name: ci3-ui
description: Use when creating or modifying UI components, styling, or layouts for both Admin and Student areas. Enforces the use of a shared centralized palette (Light/Dark mode) and reusable UI patterns.
---

# UI & Styling Guidelines

This project uses a centralized UI system that supports Light and Dark Mode through CSS variables in `public/assets/css/theme.css`.

## Mandatory Rules

1. **Style Reuse Between Admin and Student**:
   - DO NOT create separate CSS files with different palettes for Admin and Student.
   - Always use `theme.css` variables.

2. **CSS Variable Usage (Theme)**:
   - Use global variables (`--bg-main`, `--bg-card`, `--text-main`, `--brand-primary`, etc.) instead of hex colors (e.g. `#ffffff` or `#333`).
   - Use existing utility classes like `.bg-theme-main`, `.bg-theme-card`, `.text-theme-heading`, `.border-theme`.

3. **UI Component Reuse (Button Standardization)**:
   - If a button or visual element is used more than once with the same pattern, you MUST centralize its styles and name the button type in `theme.css` (e.g. `.btn-theme-primary`, `.btn-theme-secondary`, `.btn-theme-outline`).
   - Avoid inline styles or repetitive ad-hoc classes. Reuse the created layers and classes.
   - Standardize consistent elements to generate cohesion.

4. **Dark Mode Support**:
   - The `data-theme="dark"` attribute (managed on the HTML root) inverts color variables.
   - Avoid rigid Bootstrap utility classes that break dark mode, such as `.bg-white`, `.text-dark`, or `.text-gray-800`.
   - Replace them with `.bg-theme-card` and `.text-theme-heading`.

## CSS Inclusion Pattern

In base layout files (e.g. `index.php`), ensure `theme.css` is referenced BEFORE other custom CSS, and that the theme detection script is in the `<head>` (to prevent FOUC - Flash of Unstyled Content):

```html
<!-- Script to avoid FOUC -->
<script>
    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>

<link rel="stylesheet" href="<?= base_url('public/assets/css/theme.css') ?>">
```
