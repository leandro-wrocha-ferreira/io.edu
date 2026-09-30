TITULO: Padronização PSR-4 de Mappers/DTOs, Cliente HTTP Fetch e Exceções AppException

DESCRIÇÃO:
Implementar melhorias estruturais de arquitetura e infraestrutura no projeto para reforçar o autoload PSR-4, o tratamento padronizado de exceções de domínio e a comunicação assíncrona no frontend.

1. Padronização PSR-4 para DTOs e Mappers:
- Atualizar o `composer.json` substituindo a declaração legada de `"classmap"` dos diretórios `models/dtos/` e `models/mappers/` por namespaces PSR-4 explícitos sob o namespace base `app\`:
  - `"app\\models\\dtos\\": "application/models/dtos/"`
  - `"app\\models\\mappers\\": "application/models/mappers/"`
- Declarar o namespace correspondente nos arquivos PHP:
  - DTOs em `application/models/dtos/` devem utilizar `namespace app\models\dtos;`
  - Mappers em `application/models/mappers/` devem utilizar `namespace app\models\mappers;`
- Atualizar todas as classes (Models, Use Cases, Repositórios) que utilizam esses DTOs e Mappers para importá-los via instrução `use` (ex: `use app\models\mappers\UserMapper;`).
- Executar `composer dump-autoload` via Docker para regenerar o autoloader.

2. Cliente HTTP JavaScript com Header `X-App-Json`:
- Criar um módulo/cliente HTTP reutilizável em JavaScript nativo (utilizando a Fetch API) em `public/assets/js/http.js`.
- Configurar o cliente para enviar por padrão o header customizado:
  - `X-App-Json: application/json`
  - `X-Requested-With: XMLHttpRequest`
- Garantir que o backend (`MY_Controller::_remap()` e `response_helper.php`) utilize a presença desse header para mapear se a resposta deve ser formatada como JSON ou tratada via fluxo de sessão/flashdata com redirecionamento HTML.
- O cliente HTTP deve realizar o parsing automático de JSON e tratar erros HTTP (status >= 400), rejeitando a Promise com as mensagens de erro formatadas pela API.

3. Padronização de Exceções de Domínio (`AppException`):
- Garantir que todas as exceções semânticas da aplicação em `app\domain\exceptions\` herdem diretamente de `app\domain\exceptions\AppException`:
  - `NotFoundException extends AppException`
  - `ValidationException extends AppException`
  - `ConflictException extends AppException`
  - `UnauthorizedException extends AppException`
  - `ForbiddenException extends AppException`
- Garantir que `AppException` forneça mensagem, código HTTP semântico (404, 422, 409, 401, 403) e método de acesso para resposta consistente.
- Padronizar use cases para lançar exclusivamente classes que herdem de `AppException`, eliminando o lançamento direto de `\RuntimeException` ou `\Exception`.
- Assegurar que `MY_Controller::_remap()` capture `AppException` e utilize o código de status HTTP configurado para respostas JSON e flashdata.

CRITÉRIOS DE ACEITE:
- `composer.json` sem diretórios de models no `classmap` e com mapeamento PSR-4 para DTOs e Mappers.
- DTOs e Mappers declarados com namespace PSR-4 e importados via `use`.
- Todas as exceções semânticas herdando de `AppException`.
- Cliente `http.js` implementado com cabeçalho `X-App-Json: application/json`.
- Todos os testes unitários passando (`docker compose exec app vendor/bin/phpunit`) com 0 erros e 0 falhas.