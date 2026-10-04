---
name: ci3-ui
description: Use when creating or modifying UI components, styling, or layouts for both Admin, Student, Public, and Auth areas. Enforces the use of the canonical .edu-* Design System, centralized CSS tokens (Light/Dark mode), and reusable UI layout patterns.
---

# UI & Styling Guidelines

Este projeto utiliza o **Design System Canônico `.edu-*`**, centralizado e compatível com Light e Dark Mode através de tokens CSS em `public/assets/css/shared/tokens.css` e carregado via `public/assets/css/theme.css` e `public/assets/css/shared/components.css`.

---

## 1. Master Layouts & Carregamento Direto

O projeto **não utiliza views intermediárias ou proxies** (como `admin/index.php` ou `login.php`). Os controllers carregam diretamente os layouts mestres em `application/views/layout/`:

* `layout/admin.php` — Painel Administrativo (sidebar retrátil, topbar com alternador de tema e breadcrumbs).
* `layout/student.php` — Área do Aluno (navegação orientada a aprendizagem e trilhas).
* `layout/auth.php` — Telas de autenticação (login, recuperação de senha centralizadas).
* `layout/public.php` — Páginas públicas e institucionais.

### Exemplo de Chamada no Controller:

```php
$data = [
	'page_name' => 'admin/users/index',
	'title' => 'Gestão de Usuários',
	'page_js' => ['admin/users/index.js']
];
$this->load->view('layout/admin', $data);
```

### Injeção de White-Label Branding (`render_branding_styles`):
Todos os layouts mestres devem obrigatoriamente chamar o helper de branding dentro do `<head>` para permitir a sobreposição dinâmica de tokens CSS por organização/inquilino:

```html
<!-- White-Label Tenant Overrides -->
<?= render_branding_styles() ?>
```

---

## 2. Design Tokens CSS Canônicos (`--edu-*`)

Todos os estilos devem utilizar estritamente os tokens canônicos com prefixo `--edu-*`. Tokens legados (`--bg-main`, `--bg-card`, `--brand-primary`) são mantidos exclusivamente em `tokens.css` como **aliases de retrocompatibilidade** e NÃO devem ser usados em novas implementações.

### Principais Tokens Globais:

```css
/* Cores de Marca e Destaque */
--edu-primary:          #4f46e5;
--edu-primary-hover:    #4338ca;
--edu-primary-ghost:    rgba(79, 70, 229, 0.08);
--edu-accent:           #06b6d4;

/* Status e Feedback */
--edu-success:          #10b981;
--edu-warning:          #f59e0b;
--edu-danger:           #ef4444;
--edu-info:             #3b82f6;

/* Superfícies (Light Mode padrão / invertidas automaticamente no Dark Mode) */
--edu-bg-canvas:        #f8fafc;
--edu-bg-surface:       #ffffff;
--edu-bg-subtle:        #f1f5f9;
--edu-bg-elevated:      #ffffff;

/* Hierarquia de Tipografia */
--edu-text-heading:     #0f172a;
--edu-text-body:        #334155;
--edu-text-muted:       #64748b;

/* Bordas, Raios e Sombras */
--edu-border:           #e2e8f0;
--edu-border-strong:    #cbd5e1;
--edu-radius-md:        0.5rem;
--edu-radius-lg:        0.75rem;
--edu-radius-pill:      9999px;
--edu-shadow-sm:        0 1px 3px rgba(0, 0, 0, 0.05);
```

### Dark Mode (`data-theme="dark"`):
* O tema escuro é ativado pelo atributo `data-theme="dark"` na tag `<html>`.
* As variáveis `--edu-bg-*`, `--edu-text-*` e `--edu-border*` invertem seus valores automaticamente.
* **Proibido:** Usar classes utilitárias rígidas do Bootstrap que quebram o tema escuro (ex: `.bg-white`, `.text-dark`, `.text-gray-800`).

---

## 3. Padrão de Cabeçalho de Página (`.edu-page-header` / `.page-header`)

Todas as páginas internas utilizam a estrutura padrão com breadcrumbs acessíveis, hierarquia semântica de títulos e bloco de ações:

```html
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
			<a href="<?= base_url('admin/painel') ?>">Início</a>
			<span class="sep" aria-hidden="true">/</span>
			<span class="active" aria-current="page">Usuários</span>
		</nav>
		<h1 class="page-header-title">Gestão de Usuários</h1>
		<p class="page-header-subtitle">Gerencie os acessos e permissões do sistema.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/usuarios/novo') ?>" class="edu-btn edu-btn-primary">
			<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Novo Usuário
		</a>
	</div>
</div>
```

---

## 4. Padrão de Formulários Seccionados (`form-layout.css`)

Formulários de cadastro e edição não devem ser cards genéricos. Eles devem seguir a composição em seções visuais através de `.edu-form-card`, `.edu-form-section` e `.edu-form-actions`:

```html
<div class="edu-form-card animate-fade-up animate-delay-1">
	<form action="<?= current_url() ?>" method="POST" id="main-form">
		<!-- Seção do Formulário -->
		<div class="edu-form-section">
			<div class="edu-form-section-header">
				<div class="edu-form-section-icon">
					<i class="bi bi-person-badge" aria-hidden="true"></i>
				</div>
				<div>
					<h2 class="edu-form-section-title">Dados Pessoais</h2>
					<p class="edu-form-section-desc">Identificação principal do usuário no sistema.</p>
				</div>
			</div>
			
			<div class="row g-3">
				<div class="col-md-6 edu-form-field">
					<label class="edu-label edu-label-required" for="user-name">Nome Completo</label>
					<input type="text" class="edu-input" id="user-name" name="name" required>
					<span class="edu-form-hint">Nome visível nos certificados e relatórios.</span>
				</div>
				<div class="col-md-6 edu-form-field">
					<label class="edu-label edu-label-required" for="user-email">E-mail</label>
					<input type="email" class="edu-input" id="user-email" name="email" required>
				</div>
			</div>
		</div>

		<!-- Barra de Ações (Rodapé Padronizado) -->
		<div class="edu-form-actions">
			<a href="<?= base_url('admin/usuarios') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
			<button type="submit" class="edu-btn edu-btn-primary" id="btn-submit">Salvar Alterações</button>
		</div>
	</form>
</div>
```

> **Comportamento Mobile:** Em resoluções móveis (`max-width: 768px`), `.edu-form-actions` inverte a ordem (`column-reverse`), deixando o botão principal no topo e esticando a largura para 100%.

---

## 5. Padrão de Listagens com Toolbar Desacoplada e DataTables (`data-table.css`)

Tabelas operacionais utilizam o padrão `.edu-table-card` composto por uma toolbar desacoplada (`.edu-data-toolbar`) e a tabela de dados (`.edu-data-table`):

```html
<div class="edu-card edu-table-card animate-fade-up animate-delay-1">
	<!-- Toolbar Desacoplada -->
	<div class="edu-data-toolbar">
		<div class="edu-toolbar-start">
			<div class="edu-search-box">
				<i class="bi bi-search edu-search-icon" aria-hidden="true"></i>
				<input type="search" class="edu-search-input" id="users-search-input" placeholder="Buscar por nome ou e-mail..." aria-label="Buscar usuários">
			</div>
		</div>
		<div class="edu-toolbar-end">
			<select class="edu-toolbar-filter" id="users-status-filter" aria-label="Filtrar por status">
				<option value="">Todos os status</option>
				<option value="active">Ativos</option>
				<option value="inactive">Inativos</option>
			</select>
			<div class="edu-toolbar-count" id="users-count" aria-live="polite">
				Carregando contagem...
			</div>
		</div>
	</div>

	<!-- Tabela Responsiva -->
	<div class="edu-table-responsive">
		<table class="edu-data-table" id="users-table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Nome</th>
					<th>E-mail</th>
					<th>Papel</th>
					<th>Criado em</th>
					<th class="text-center">Status</th>
					<th class="text-center">Ações</th>
				</tr>
			</thead>
			<tbody>
				<!-- Preenchido via DataTables Server-Side -->
			</tbody>
		</table>
	</div>
</div>
```

---

## 6. Componentes Reutilizáveis Oficiais

### Botões (`buttons.css`):
* Primários: `.edu-btn.edu-btn-primary`
* Secundários / Neutros: `.edu-btn.edu-btn-secondary`
* Contorno: `.edu-btn.edu-btn-outline`
* Perigo: `.edu-btn.edu-btn-danger`
* Ícones / Tamanhos: `.edu-btn-icon`, `.edu-btn-sm`, `.edu-btn-lg`
*(Classes `.btn-theme-*` existem apenas como aliases de compatibilidade).*

### Badges Semânticos (`badges.css`):
* Status com indicador: `<span class="edu-badge edu-badge-success edu-badge-dot">Ativo</span>`
* Variações: `.edu-badge-primary`, `.edu-badge-warning`, `.edu-badge-danger`, `.edu-badge-neutral`, `.edu-badge-accent`
* Formato pílula: `.edu-badge-pill`

### Ações de Linha em Tabelas (`data-table.css`):
```html
<div class="edu-action-group">
	<a href="..." class="edu-action-btn edu-action-btn-edit" aria-label="Editar registro" title="Editar">
		<i class="bi bi-pencil" aria-hidden="true"></i>
	</a>
	<button type="button" class="edu-action-btn edu-action-btn-delete" aria-label="Excluir registro" title="Excluir">
		<i class="bi bi-trash3" aria-hidden="true"></i>
	</button>
</div>
```

---

## 7. Diretriz de Animações de Entrada (`animate-fade-up`)

Todas as páginas devem aplicar classes de animação suave de forma consistente:
* **Header da página:** `<div class="page-header animate-fade-up">`
* **Card / Tabela / Formulário principal:** `<div class="edu-card edu-table-card animate-fade-up animate-delay-1">` ou `<div class="edu-form-card animate-fade-up animate-delay-1">`
* **Cards secundários:** `.animate-fade-up` com atrasos escalonados (`.animate-delay-2`, `.animate-delay-3`).
* **Acessibilidade:** Suporte automático a `prefers-reduced-motion: reduce` definido em `shared/base.css`.

---

## 8. Anti-Patterns

❌ **Cores Hexadecimais Hardcoded**: Usar `#ffffff`, `#1a1a1a`, ou `#4f46e5` diretamente no CSS ao invés das variáveis canônicas (`var(--edu-bg-surface)`, `var(--edu-primary)`, etc.).
❌ **Uso de Classes Legadas em Novas Telas**: Usar `.btn-theme-primary` ou `.card-theme` em vez das classes canônicas `.edu-btn-primary` e `.edu-card`.
❌ **Classes Utilitárias Rígidas do Bootstrap**: Usar `.bg-white`, `.text-dark`, ou `.text-muted` em cards e títulos, quebrando a legibilidade no Dark Mode.
❌ **Views Intermediárias / Proxies**: Tentar carregar telas através de views ponte (`admin/index.php`) em vez de carregar diretamente `layout/admin` no controller.
❌ **Formulários sem Estrutura Semântica**: Colocar inputs soltos em `.card` genérico sem utilizar `.edu-form-section`, `.edu-form-header` e `.edu-form-actions`.
❌ **Tabelas com Controles Nativos Desalinhados**: Deixar o DataTables injetar seus inputs e selects padrão sem utilizar o `.edu-data-toolbar` desacoplado.
