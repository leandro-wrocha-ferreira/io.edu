---
name: ci3-ui
description: Use when creating or modifying UI components, styling, or layouts for both Admin and Student areas. Enforces the use of a shared centralized palette (Light/Dark mode) and reusable UI patterns.
---

# UI & Styling Guidelines

Este projeto usa um sistema de UI centralizado que suporta Light e Dark Mode através de variáveis CSS no arquivo `public/assets/css/theme.css`.

## Regras Obrigatórias

1. **Reutilização de Estilos entre Admin e Aluno**:
   - NÃO crie arquivos CSS separados com paletas distintas para Admin e Aluno.
   - Use sempre as variáveis do `theme.css`.

2. **Uso de Variáveis CSS (Theme)**:
   - Use as variáveis globais (`--bg-main`, `--bg-card`, `--text-main`, `--brand-primary`, etc.) ao invés de cores hexadecimais (ex: `#ffffff` ou `#333`).
   - Você pode usar as classes utilitárias já existentes como `.bg-theme-main`, `.bg-theme-card`, `.text-theme-heading`, `.border-theme`.

3. **Reutilização de Padrões de Botões e UI (Button Standardization)**:
   - Se um botão ou elemento visual for utilizado mais de uma vez com o mesmo padrão, você DEVE centralizar seus estilos e nomear o tipo de botão no `theme.css` (ex: `.btn-theme-primary`, `.btn-theme-secondary`, `.btn-theme-outline`).
   - Evite adicionar estilos inline ou classes ad-hoc repetitivas. Reutilize as camadas e classes criadas.
   - Padronize elementos consistentes para gerar coesão.

4. **Suporte a Dark Mode**:
   - Lembre-se que o atributo `data-theme="dark"` (gerenciado na raiz do HTML) inverte as variáveis de cor.
   - Evite usar classes utilitárias rígidas do Bootstrap que quebram o dark mode, como `.bg-white`, `.text-dark`, ou `.text-gray-800`.
   - Substitua-as por `.bg-theme-card` e `.text-theme-heading`.

5. **Diretriz de Animações de Entrada (`animate-fade-up`)**:
   - Como padrão visual recomendado, as páginas aplicam animações de entrada suave nos seus contêineres principais:
     - Header da página: `<div class="page-header animate-fade-up">`
     - Card / Tabela / Formulário principal: `<div class="card card-theme animate-fade-up animate-delay-1">` ou `<div class="card-theme-form animate-fade-up animate-delay-1">`
     - Elementos secundários / Stat cards: Usar `.animate-fade-up` com atrasos escalonados (`.animate-delay-1`, `.animate-delay-2`, `.animate-delay-3`).
   - **Exceções Justificadas**: A animação NÃO é um requisito arquitetural absoluto e deve ser omitida ou ajustada em:
     - Usuários com preferência de movimento reduzido (`prefers-reduced-motion: reduce`) — respeitado automaticamente via `admin/layout.js`.
     - Modais, dropdowns e popovers (usam as transições próprias do Bootstrap).
     - Páginas de erro HTTP (404, 500).
     - Componentes atualizados continuamente via polling/tempo real onde a animação cause cintilação ou perda de foco.

## Padrão de Inclusão do CSS

Nos arquivos de layout base (ex: `index.php`), assegure que o `theme.css` esteja referenciado ANTES de outros CSS customizados, e que o script de detecção de tema esteja no `<head>` (para evitar FOUC - Flash of Unstyled Content):

```html
<!-- Script to avoid FOUC -->
<script>
    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>

<link rel="stylesheet" href="<?= base_url('public/assets/css/theme.css') ?>">
```

---

## Anti-Patterns

❌ **Cores Hexadecimais Hardcoded**: Usar `#ffffff`, `#1a1a1a`, ou `#0d6efd` diretamente nos estilos ao invés das variáveis do `theme.css` (`var(--bg-card)`, `var(--text-main)`, etc.).
❌ **Classes Utilitárias Rígidas do Bootstrap**: Usar `.bg-white`, `.text-dark`, ou `.text-muted` em cards e títulos, quebrando a legibilidade no Dark Mode.
❌ **Paletas Paralelas**: Criar arquivos CSS separados duplicando ou divergindo paletas entre as áreas do Admin e do Aluno.
❌ **Animações Forçadas sem Exceções**: Impor classes de animação sem considerar acessibilidade (`prefers-reduced-motion`) ou em contextos de modais e erros.

