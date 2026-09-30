TITULO: Atualizar skill ci3-js para refletir novo cliente HTTP Fetch

DESCRIÇÃO:
Atualizar a documentação da skill `.agents/skills/ci3-js/SKILL.md` para incorporar formalmente o cliente HTTP nativo recém-criado em `public/assets/js/http.js`, estabelecendo-o como o padrão oficial do projeto para todas as requisições assíncronas do frontend.

1. Atualização da Seção 3 ("Modern Fetch & Custom Header Pattern"):
- Atualizar o caminho oficial do utilitário para `public/assets/js/http.js` (eliminando menções antigas a `components/http.js`).
- Documentar os cabeçalhos padrão obrigatórios enviados automaticamente pelo cliente:
  - `X-App-Json: application/json`
  - `X-Requested-With: XMLHttpRequest`
  - `Accept: application/json`
- Documentar as capacidades nativas do cliente `http.js`:
  - Envio e serialização automática de objetos JSON (`Content-Type: application/json; charset=utf-8`).
  - Suporte a payloads `FormData` nativos (sem sobrescrever o boundary do navegador).
  - Detecção e injeção automática de tokens CSRF (`csrf_cookie_name` ou meta tag `csrf-token`).
  - Gerenciamento de estados de botões (`disabled` e spinner animado) via argumento de elemento ou opção `{ button: buttonEl }`.
  - Tratamento de erros HTTP (status >= 400) com rejeição automática e parsing de mensagens de erro formatadas pelo backend.
  - Métodos utilitários disponíveis no objeto global: `Http.get()`, `Http.post()`, `Http.put()`, `Http.patch()`, `Http.delete()`.

2. Atualização dos Exemplos Práticos:
- Substituir snippets de exemplo para demonstrar o uso direto de `Http.get()` e `Http.post()` em scripts de páginas (`public/assets/js/pages/...`).
- Demonstrar tratamento de sucesso e captura de exceções via `try/catch` consumindo mensagens padronizadas retornadas pela API.

3. Atualização do Checklist de Revisão JavaScript:
- Adicionar verificação obrigatória: *"Requisições assíncronas utilizam exclusivamente o cliente centralizado `Http` (`public/assets/js/http.js`), sem invocações avulsas de `fetch()` ou jQuery AJAX."*
- Reforçar a proibição de cabeçalhos obsoletos (ex: `X-App-Response`) em favor do cabeçalho canônico `X-App-Json: application/json`.

CRITÉRIOS DE ACEITE:
- Arquivo `.agents/skills/ci3-js/SKILL.md` atualizado com referências canônicas a `public/assets/js/http.js`.
- Cabeçalhos `X-App-Json` e `X-Requested-With` documentados como padrão obrigatório.
- Exemplos de código atualizados e alinhados à implementação real.
- Checklist de revisão e anti-patterns atualizados para proibir chamadas de rede não padronizadas.
