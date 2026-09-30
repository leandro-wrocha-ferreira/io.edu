TITULO: Investigação Arquitetural de Rotas Bilíngues no CodeIgniter 3 (EN / PT-BR)

DESCRIÇÃO:
Realizar uma investigação técnica e arquitetural detalhada sobre o funcionamento do roteamento de URIs no CodeIgniter 3 (`CI_Router`, `CI_URI`, `application/config/routes.php`), avaliando estratégias viáveis para suportar rotas duplas/bilíngues apontando para o mesmo controller (uma rota canônica em inglês e outra em português brasileiro para usuários do Brasil).

1. Contexto Atual do Projeto:
- As rotas atuais em `application/config/routes.php` estão estáticas e hardcoded em português (`entrar`, `sair`, `admin/painel`, `admin/usuarios`, `aluno/painel`).
- Os hooks (`Middleware.php`, `Language_check.php`) e controllers utilizam essas rotas para verificações de autenticação, redirecionamentos e geração de links.
- O objetivo é permitir que a plataforma atenda tanto usuários internacionais (rotas em inglês, ex: `/login`, `/admin/dashboard`, `/admin/users`) quanto usuários brasileiros (rotas em português, ex: `/entrar`, `/admin/painel`, `/admin/usuarios`), mapeando de forma limpa para os mesmos controllers sem duplicação de regras de negócio.

2. Escopo da Investigação Técnica:
- **Anatomia do Roteamento no CI3**:
  - Mapeamento e ordem de resolução de rotas no array `$route`.
  - Comportamento de curingas e expressões regulares (`(:num)`, `(:any)`).
  - Como o CI3 instancia o controller e action após a resolução da URI.
- **Avaliação de Estratégias**:
  - **Estratégia 1: Aliases Estáticos Paralelos**:
    - Definir rotas duplas diretamente no `routes.php` (ex: `$route['login'] = 'auth/login';` e `$route['entrar'] = 'auth/login';`).
    - *Pontos a analisar*: Facilidade de implementação, volume de linhas, impacto na geração de URLs reversas (`site_url()` / `base_url()`) e SEO (URLs canônicas).
  - **Estratégia 2: Rotas com Prefixo de Idioma (/pt/ vs /en/)**:
    - Uso de prefixos de primeiro segmento na URI (ex: `/pt/admin/painel` vs `/en/admin/dashboard`).
    - *Pontos a analisar*: Como remover o prefixo antes do roteamento no `MY_Router` ou via hook `pre_system`, e sincronização com a biblioteca de idiomas.
  - **Estratégia 3: Carregamento Dinâmico de Dicionário de Rotas**:
    - Inspecionar a preferência/localidade do usuário (`HTTP_ACCEPT_LANGUAGE` / cookie / sessão) dentro de `routes.php` ou através de arquivos particionados (ex: `routes_pt.php` e `routes_en.php`).
    - *Pontos a analisar*: Disponibilidade da sessão ou cookies no momento do carregamento de `routes.php` (fase inicial do bootstrap do CI3 antes de bibliotecas estarem ativas).
  - **Estratégia 4: Extensão Customizada `MY_Router`**:
    - Criar `application/core/MY_Router.php` sobrescrevendo `_parse_routes()` para traduzir segmentos sob demanda.
    - *Pontos a analisar*: Robustez, compatibilidade com upgrades do framework e manutenibilidade.
- **Impacto em Middleware e Geração de Links**:
  - Como o middleware de autorização (`application/hooks/Middleware.php`) valida permissões quando a mesma ação possui duas rotas distintas.
  - Como criar ou adaptar helpers de URL para gerar automaticamente links em inglês ou português conforme a localidade da sessão ativa do usuário.

3. Entregável Obrigatório:
- Documento técnico detalhado em `docs/investigations/bilingual-routing-ci3.md` contendo:
  - Análise do pipeline de execução do roteamento no CI3.
  - Tabela comparativa das estratégias (Complexidade, Manutenibilidade, Performance, DX/Geração de URLs, SEO).
  - Prova de Conceito (PoC) com snippets de código funcionais para a estratégia recomendada.
  - Recomendações e plano de ação em fases para implementação posterior.

CRITÉRIOS DE ACEITE:
- Arquivo de documentação `docs/investigations/bilingual-routing-ci3.md` criado e detalhado.
- Todas as 4 estratégias analisadas com prós, contras e viabilidade no CI3.
- Impactos no hook de autenticação/RBAC (`Middleware.php`) e helpers de URL devidamente mapeados.
- Prova de conceito (PoC) clara apresentada para a abordagem recomendada.
- Nenhuma quebra nas rotas e fluxos existentes do sistema durante a realização da pesquisa.
