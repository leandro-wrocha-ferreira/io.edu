---
name: ci3-js
description: Use when creating, modifying or organizing JavaScript files and logic. Enforces modularity and global reusability rules for scripts in the public directory.
---

# JavaScript Guidelines

Este projeto utiliza uma abordagem modular e global para a organização de arquivos JavaScript.

## Regras Obrigatórias

1. **Sem Scripts Inline Complexos**:
   - NÃO utilize blocos `<script>` com lógicas extensas diretamente nas views (`.php`). 
   - A lógica deve ser sempre extraída para arquivos `.js` estáticos na pasta `public/assets/js/`.

2. **Organização por Módulos**:
   - Scripts que pertencem a um contexto ou módulo específico (ex: admin, aluno, autenticação, cursos) devem ser agrupados em subdiretórios correspondentes.
   - Exemplo: `public/assets/js/admin/layout.js` (para lógica exclusiva da área administrativa).

3. **Scripts Globais Soltos na Raiz**:
   - Scripts que compartilham comportamento entre vários módulos (ex: alternador de temas, validações genéricas, formatação de máscaras) devem ser colocados soltos na raiz da pasta `js/` e bem nomeados para serem facilmente encontrados.
   - Exemplo: `public/assets/js/theme.js` (lida com o dark mode tanto no admin quanto no student).
   - Scripts globais devem ser construídos considerando a ausência de elementos no DOM (use `if (!element) return;` ou certifique-se de validar a existência do node antes de vincular eventos).

4. **Inclusão em Views**:
   - Ao incluir os scripts nas views, utilize sempre a função `base_url()`:
     `<script src="<?= base_url('public/assets/js/module/file.js') ?>"></script>`
   - Para scripts visuais que previnem FOUC (Flash of Unstyled Content), como o tema, inclua-os na tag `<head>`. Outros scripts comportamentais devem ir para o final do `<body>`.
