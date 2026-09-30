TITULO: Modernização do Layout Frontend: Redesign Visual da Home, Login e Dashboard

DESCRIÇÃO:
Modernizar a interface frontend da plataforma educacional (LMS), elevando o padrão estético, usabilidade, responsividade e consistência visual das páginas principais do sistema: Home (Landing Page), Página de Login e Dashboards (Admin e Aluno), respeitando a paleta centralizada (Light/Dark mode) definida na skill `.agents/skills/ci3-ui/SKILL.md`.

---

### REFERÊNCIAS VISUAIS E BENCHMARKS (EDITE ESTA SEÇÃO COM SEUS LINKS):

> Preencha os campos abaixo com os links de inspiração, mockups (Figma/Dribbble/Behance) ou sites de referência para nortear o design:

- **Referência Home / Landing Page:**
  <!-- ADICIONAR LINK DE REFERÊNCIA - HOME: [Cole aqui o link de referência para a Home/Landing Page] -->

- **Referência Página de Login:**
  <!-- ADICIONAR LINK DE REFERÊNCIA - LOGIN: [Cole aqui o link de referência para a tela de Login] -->

- **Referência Dashboard (Admin & Aluno):**
  <!-- ADICIONAR LINK DE REFERÊNCIA - DASHBOARD: [Cole aqui o link de referência para os Dashboards] -->

- **Referências Complementares / Design System:**
  <!-- ADICIONAR LINK DE REFERÊNCIA - DESIGN SYSTEM GERAL: [Cole aqui referências adicionais de componentes, cores ou tipografia] -->

---

1. Modernização da Home / Landing Page (`application/views/welcome_message.php` ou view de landing):
- Substituir a página padrão básica do CodeIgniter por uma landing page educacional contemporânea e atraente.
- **Hero Section**:
  - Tipografia de alto impacto com título cativante, subtítulo explicativo e botões de chamada para ação (CTA primário: "Começar Agora" / "Acessar Plataforma" e secundário: "Conhecer Cursos" ou "Entrar").
  - Elemento visual ilustrativo ou mockup moderno da plataforma.
- **Seção de Recursos / Diferenciais**:
  - Grid de cards de benefícios com ícones do Bootstrap Icons, bordas sutis e elevação ao passar o mouse (`hover`).
- **Seção de Vitrine / Cursos ou Estatísticas**:
  - Apresentação de dados da plataforma (ex: alunos ativos, cursos disponíveis, taxa de conclusão).
- **Header e Footer**:
  - Header fixo com logotipo da marca, navegação fluida e botão de alternância de tema (Light/Dark mode).
  - Footer refinado com links institucionais, políticas e direitos reservados.

2. Redesign da Página de Login (`application/views/login.php`):
- Transformar a página simples de formulário centralizado em uma experiência de autenticação moderna e segura:
  - Layout elegante (ex: split screen com branding institucional ilustrativo de um lado e área de formulário do outro, ou card flutuante com elevação e acabamento refinado).
  - Campos de entrada de dados com estados de foco expressivos, labels flutuantes ou acessíveis e ícone para alternar visualização de senha (mostrar/ocultar senha).
  - Integração do botão de submissão com o cliente `public/assets/js/http.js` (gerenciamento automático de estado desabilitado e spinner animado durante requisição).
  - Alertas de erro/sucesso visualmente integrados à paleta de status, sem causar saltos bruscos de layout.
  - Links auxiliares ("Esqueci minha senha", "Precisa de ajuda?").

3. Redesign dos Dashboards (`admin/dashboard.php` e `student/dashboard.php`):
- **Dashboard Administrativo**:
  - Banner de boas-vindas contextual com saudação baseada no horário do dia e data atual.
  - Cards de métricas/KPIs revitalizados com ícones estilizados em gradientes sutis, badges de evolução/tendência e tipografia em destaque.
  - Seções em grid para gráficos/relatórios rápidos e tabela de atividades recentes com status estilizados (pills/badges).
- **Dashboard do Aluno**:
  - Visão geral de progresso nos cursos matriculados com barras de progresso elegantes.
  - Card de "Continuar de onde parou" com atalho direto para a última aula assistida.
  - Avisos/notificações acadêmicas em formato limpo.

4. Padrões Técnicos e Diretrizes de Design (`ci3-ui` / `ci3-js`):
- Utilizar exclusivamente os tokens de cores e variáveis CSS do sistema centralizado (`public/assets/css/theme.css` ou design tokens do projeto), garantindo suporte pleno aos modos Claro e Escuro.
- Aplicar classes canônicas de animação de entrada (`animate-fade-up` no `.page-header` e `.animate-fade-up.animate-delay-1` nos cards e seções).
- Conformidade estrita com acessibilidade (WCAG 2.1 AA: contraste adequado, foco visível pelo teclado, sem dependência exclusiva de cores).
- Responsividade fluida em resoluções mobile, tablet e desktop.

CRITÉRIOS DE ACEITE:
- Seção de referências visuais com marcadores claros e prontos para adição dos links pelo usuário.
- Layout da Home revitalizado e funcional, substituindo o scaffold padrão do CI3.
- Página de Login redesenhada com visual moderno, responsivo e estados de interação dinâmicos.
- Dashboards Administrativo e do Aluno com novos cards de KPIs e estrutura visual alinhada.
- Estilos 100% integrados à paleta compartilhada com alternância correta entre Light e Dark mode.
- Animações de entrada presentes e sem impacto negativo em performance ou acessibilidade.
