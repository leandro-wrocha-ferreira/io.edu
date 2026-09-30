TITULO: Padronização de Exceções em Inglês e Tradução Dinâmica por Localidade do Usuário

DESCRIÇÃO:
Padronizar o lançamento de todas as exceções do sistema (camadas de Domínio, Casos de Uso e Apresentação) para o idioma inglês, eliminando mensagens em português diretamente no código PHP, e implementar um mecanismo de tradução dinâmica na camada de apresentação (`MY_Controller`) baseado na localidade do usuário (padrão EN; se o usuário for do Brasil, retornar PT-BR; para qualquer outro país, retornar EN).

1. Padronização do Lançamento de Exceções em Inglês (Domain & Application Layers):
- Localizar todas as ocorrências de `throw new` nas camadas `application/domain/`, `application/usecases/` e `application/controllers/`.
- Substituir todas as mensagens escritas em português por suas mensagens equivalentes canônicas em inglês. Exemplos:
  - `"Usuário não encontrado"` → `"User not found"`
  - `"Perfil não encontrado"` → `"Role not found"`
  - `"E-mail já está em uso"` → `"Email is already in use"`
  - `"Perfis padrão do sistema não podem ser excluídos."` → `"Default system roles cannot be deleted"`
  - `"O perfil AdminMaster é protegido e não pode ser alterado."` → `"The AdminMaster role is protected and cannot be modified"`
  - `"Você não pode alterar o status do seu próprio usuário."` → `"You cannot alter the status of your own user"`
  - `"Você não pode excluir o seu próprio usuário."` → `"You cannot delete your own user"`
  - `"Você não pode editar ou alterar as configurações do seu próprio usuário nesta tela."` → `"You cannot edit your own user on this screen"`
- As exceções semânticas (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`) continuam sendo utilizadas normalmente, sempre com mensagens canônicas em inglês.

2. Catálogo de Traduções de Exceções (Language Files):
- Criar/atualizar arquivos de tradução dedicados a mensagens de exceções:
  - `application/language/english/exceptions_lang.php`
  - `application/language/portuguese-brazilian/exceptions_lang.php`
- Mapear as mensagens ou identificadores canônicos para seus textos traduzidos correspondentes.
- Exemplo em `english/exceptions_lang.php`:
  ```php
  $lang['exception_user_not_found'] = 'User not found';
  $lang['exception_role_not_found'] = 'Role not found';
  $lang['exception_email_in_use']   = 'Email is already in use';
  ```
- Exemplo em `portuguese-brazilian/exceptions_lang.php`:
  ```php
  $lang['exception_user_not_found'] = 'Usuário não encontrado';
  $lang['exception_role_not_found'] = 'Perfil não encontrado';
  $lang['exception_email_in_use']   = 'E-mail já está em uso';
  ```

3. Detecção de Localidade do Usuário e Tradução na Apresentação (`MY_Controller`):
- Regra de detecção de localidade:
  - **Padrão**: `english` (`EN`).
  - **Brasil**: Se o cabeçalho `HTTP_ACCEPT_LANGUAGE` contiver `pt-BR` ou `pt`, ou se a preferência de idioma/sessão do usuário indicar o Brasil, a localidade resolvida deve ser `portuguese-brazilian` (`PT-BR`).
  - **Demais Países**: Se o usuário for de qualquer outro país (ex: `en-US`, `es-ES`, `fr-FR`, `de-DE`, etc.), a localidade resolvida DEVE ser estritamente `english` (`EN`).
- No método interceptador `MY_Controller::handle_app_exception()`:
  - Traduzir a mensagem da exceção para a localidade do usuário antes de enviar a resposta (seja via `json_response()` ou via flashdata/HTML).
  - Caso uma mensagem não possua tradução mapeada no arquivo da localidade, utilizar a própria mensagem original em inglês como fallback seguro.

4. Atualização e Validação de Testes:
- Atualizar todos os testes unitários em `tests/unit/usecases/` que faziam assert de mensagens em português (ex: `expectExceptionMessage('Usuário não encontrado')` para `expectExceptionMessage('User not found')`).
- Criar testes unitários para o mecanismo de tradução e mapeamento de mensagens de exceções para localidades brasileira (`PT-BR`) e internacional (`EN`).
- Garantir 100% de aprovação na suíte de testes do PHPUnit:
  ```bash
  docker compose exec app vendor/bin/phpunit
  ```

CRITÉRIOS DE ACEITE:
- Nenhuma exceção na aplicação ou domínio é disparada com texto em português brasileiro (100% em inglês).
- Catálogos de idiomas criados em `application/language/english/exceptions_lang.php` e `application/language/portuguese-brazilian/exceptions_lang.php`.
- Respostas de erro para requisições de usuários do Brasil retornam em `PT-BR`.
- Respostas de erro para usuários de qualquer outro país retornam em `EN`.
- Respostas em JSON (`X-App-Json` / AJAX) e em HTML (flashdata) recebem a mensagem traduzida de forma uniforme.
- Todos os testes unitários e de integração passando com sucesso.
