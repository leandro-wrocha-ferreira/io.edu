---
name: ci3-js
description: Use when creating, modifying or organizing JavaScript files and logic. Enforces modularity, page-specific scripts, hybrid JS guidelines (jQuery vs Vanilla JS), DataTables decoupled toolbar integration, and CI3 AJAX rules.
---

# JavaScript Guidelines

Este projeto adota uma abordagem **modular, limpa e híbrida** para organização e execução de scripts JavaScript.

---

## 1. File & Directory Organization

1. **Proibição de Scripts Inline nas Views**:
   * É **estritamente proibido** inserir blocos `<script>` com lógica de negócio, seletores ou inicialização de bibliotecas dentro dos arquivos de view `.php` em `application/views/`.
   * Todo código JavaScript deve residir em arquivos `.js` estáticos na estrutura `public/assets/js/`.

2. **Estrutura de Pastas**:
   * **Global/Theme:** `public/assets/js/theme.js` (gerenciador de tema no `<head>` para evitar FOUC).
   * **Global HTTP Client:** `public/assets/js/http.js` (cliente Fetch nativo com CSRF e headers padronizados).
   * **Layout Base:** `public/assets/js/admin/layout.js` (sidebar responsivo, backdrop e toggle).
   * **Componentes Reutilizáveis:** `public/assets/js/components/` (diálogos, confirmações modais, etc.).
   * **Scripts por Página:** `public/assets/js/pages/<area>/<controller>/<action>.js` (carregados sob demanda via `$page_js`).

3. **Carregamento Dinâmico de Scripts de Página (`$page_js`)**:
   * No Controller:
     ```php
     $data = [
         'page_name' => 'admin/users/index',
         'title' => 'Gestão de Usuários',
         'page_js' => ['admin/users/index.js'], // relativo a public/assets/js/pages/
     ];
     $this->load->view('layout/admin', $data);
     ```
   * No Layout Mestre (`application/views/layout/admin.php`):
     ```php
     <?php if (!empty($page_js)): ?>
         <?php foreach ((array)$page_js as $js): ?>
             <script src="<?= base_url('public/assets/js/pages/' . $js) ?>"></script>
         <?php endforeach; ?>
     <?php endif; ?>
     ```

---

## 2. Estratégia Híbrida (jQuery vs Vanilla JS)

O projeto adota uma estratégia híbrida equilibrando bibliotecas consolidadas com a performance do JavaScript moderno (ES6+):

### Quando Usar jQuery:
* **DataTables:** Inicialização, configuração de colunas, ordenação, paginação e eventos do plugin (`draw.dt`).
* **Manipulação de DOM Estritamente Legada:** Apenas quando a sintaxe do jQuery evitar código boilerplate excessivo em plugins existentes.

### Quando Usar Vanilla JS (ES6+):
* **Lógica de Layout e Tema:** Abertura da sidebar, backdrop, alternador de tema e observers.
* **Requisições Assíncronas:** Todas as operações AJAX via `Http` (`public/assets/js/http.js`).
* **Eventos e Debounce de Formulários:** `addEventListener`, `FormData`, `setTimeout`/`clearTimeout`.
* **Web APIs Modernas:** `localStorage`, `IntersectionObserver`, `CustomEvent`.

---

## 3. Padrão DataTables com Toolbar Desacoplada e Debounce

Tabelas de listagem operacional utilizam o componente `.edu-data-toolbar` desacoplado no HTML e controlado via JavaScript. A inicialização do DataTables deve seguir rigorosamente a receita abaixo:

```javascript
/**
 * Script de Listagem Operacional — io.edu LMS
 */
document.addEventListener('DOMContentLoaded', function () {
    const tableEl = document.getElementById('users-table');
    
    // Guard Clause: Verifica existência da tabela e do plugin antes de executar
    if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
        return;
    }

    // 1. Inicialização do DataTables
    const dt = jQuery(tableEl).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.origin + '/admin/usuarios/dados',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        },
        columns: [
            { data: 'id', width: '70px', className: 'd-none d-md-table-cell ps-3' },
            { data: 'name', className: 'fw-semibold' },
            { data: 'email', className: 'text-muted' },
            { data: 'role', orderable: false, searchable: false },
            { data: 'status', orderable: false, searchable: false, width: '110px', className: 'text-center' },
            { data: 'actions', orderable: false, searchable: false, width: '110px', className: 'text-center' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
            // Estados Vazios Personalizados com classes .edu-table-empty
            emptyTable: `
                <div class="edu-table-empty">
                    <i class="bi bi-people edu-table-empty-icon" aria-hidden="true"></i>
                    <div class="edu-table-empty-title">Nenhum registro cadastrado</div>
                    <div class="edu-table-empty-desc">Cadastre o primeiro registro para começar.</div>
                    <a href="/admin/usuarios/novo" class="edu-btn edu-btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Criar Novo
                    </a>
                </div>
            `,
            zeroRecords: `
                <div class="edu-table-empty">
                    <i class="bi bi-search edu-table-empty-icon" aria-hidden="true"></i>
                    <div class="edu-table-empty-title">Nenhum resultado encontrado</div>
                    <div class="edu-table-empty-desc">Não encontramos registros correspondentes à busca informada.</div>
                </div>
            `,
            processing: '<div class="d-flex align-items-center gap-2"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div><span>Carregando dados...</span></div>'
        },
        pageLength: 25,
        order: [[0, 'desc']],
        // Suprime a barra de busca e seleção padrão do DataTables (delegada à .edu-data-toolbar)
        dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between p-3 border-top"ip>'
    });

    // 2. Busca na Toolbar com Debounce de 300ms
    const searchInput = document.getElementById('users-search-input');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                dt.search(this.value).draw();
            }, 300);
        });
    }

    // 3. Filtro por Select Integrado
    const statusFilter = document.getElementById('users-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            dt.search(searchInput ? searchInput.value : '').draw();
        });
    }

    // 4. Sincronização Dinâmica do Contador de Registros da Toolbar
    dt.on('draw.dt', function () {
        const info = dt.page.info();
        const countEl = document.getElementById('users-count');
        if (countEl) {
            if (info.recordsTotal === 0) {
                countEl.textContent = 'Nenhum registro';
            } else if (info.recordsDisplay < info.recordsTotal) {
                countEl.textContent = `Exibindo ${info.recordsDisplay} de ${info.recordsTotal} registros (filtrado)`;
            } else {
                countEl.textContent = `Total de ${info.recordsTotal} registros cadastrados`;
            }
        }
    });
});
```

---

## 4. Modern Fetch & Cliente HTTP Centralizado (`http.js`)

Todas as requisições assíncronas do frontend DEVEM utilizar o cliente nativo centralizado em `public/assets/js/http.js`. Chamadas brutas com `fetch()` ou `$.ajax` são **estritamente proibidas**.

### Funcionalidades Automáticas do `Http`:
1. **Headers Canônicos Injetados**:
   * `X-App-Json: application/json`
   * `X-Requested-With: XMLHttpRequest`
   * `Accept: application/json`
2. **Proteção CSRF**: Lê e anexa automaticamente o token CSRF (`X-CSRF-TOKEN` e em `FormData`).
3. **Gestão de Estado de Botões**: Desabilita o botão acionador e renderiza spinner acessível durante a chamada.
4. **Tratamento de Exceções**: Rejeita com mensagem tratada em status HTTP >= 400.

### Exemplo de Uso em Formulário Assíncrono:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('btn-submit');
    const formEl = document.getElementById('main-form');

    if (saveBtn && formEl) {
        formEl.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = new FormData(formEl);

            try {
                // Passa o botão como 3º parâmetro para ativar loading automático
                const response = await Http.post(formEl.action, payload, saveBtn);

                if (response.success) {
                    window.location.href = response.redirect || '/admin/usuarios';
                }
            } catch (error) {
                console.error('Falha na operação:', error);
                alert(error.message || 'Ocorreu um erro ao processar a solicitação.');
            }
        });
    }
});
```

---

## 5. JavaScript Code Review Checklist

Ao criar ou revisar arquivos JavaScript, valide os seguintes pontos:

- [ ] **Zero Scripts Inline:** A view `.php` não possui tags `<script>` com código executável.
- [ ] **Guard Clauses:** Todo script verifica a existência dos elementos no DOM antes de executar (`if (!tableEl) return;`).
- [ ] **DataTables Desacoplado:** A propriedade `dom` oculta a busca/length padrão (`dom: 'rt<...>ip'`) e utiliza `.edu-data-toolbar`.
- [ ] **Debounce de 300ms:** Pesquisas de texto em listagens aplicam debounce para não sobrecarregar requisições AJAX.
- [ ] **Estados Vazios Ricos:** Configuração de `emptyTable` e `zeroRecords` utilizando a classe `.edu-table-empty`.
- [ ] **Uso Exclusivo do `Http`:** Requisições assíncronas passam por `public/assets/js/http.js`.
- [ ] **Loading nos Botões:** Triggers de submissão recebem estado de carregamento durante operações de rede.

---

## 6. Anti-Patterns

❌ **Scripts Inline em Views**: Inserir código JS dentro de arquivos de view `.php`.
❌ **Requisições com `fetch()` Bruto ou `$.ajax`**: Fazer chamadas diretas de rede contornando `public/assets/js/http.js`.
❌ **Busca no DataTables sem Debounce**: Disparar `dt.search().draw()` em cada evento de tecla sem temporizador.
❌ **DataTables sem Guard Clause**: Inicializar o DataTables sem verificar se o elemento existe no DOM, gerando erros em telas diferentes.
❌ **Uso da Toolbar Nativa do DataTables**: Deixar controles padrão sem estilização ao invés de usar `.edu-data-toolbar`.
❌ **Submissões Assíncronas sem Desabilitar Botão**: Permitir múltiplos cliques repetidos do usuário durante a requisição.
