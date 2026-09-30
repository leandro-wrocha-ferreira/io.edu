LEMBRA DE CRIAR UM CLIENTE HTTP QUE UTILIZE O FETCH DO JAVASCRIPT
PARA REALIZAR REQUISIÇÕES E MANTER O PADRÃO DO HEADER COM A PROPRIEDADE
X-APP-JSON = APPLICATION/JSON PARA IDENTIFICAR JUNTO COM REQUISIÇÕES AJAX
E CONSEGUIR REALIZAR MAPEAMENTO DO FLUXO DE TRATATIVA DE RETORNO PARA SESSION OU JSON

MUDAR A FORMA DE LANÇAR EXCEÇÕES PARA MANTER PADRÃO DE CLASSES PARA APPEXCEPTION

CORRIGIR DECLARAÇÃO DOS MAPPERS E DTOS PARA USAR PADRÃO PSR-4
HOJE: 
"classmap": [
			"application/models/mappers/",
			"application/models/dtos/"
		]

CORRETO:
"psr-4": {
    "app\\domain\\": "application/domain/",
    "app\\usecases\\": "application/usecases/",
    "app\\factories\\": "application/factories/",
    "app\\models\\dtos\\": "application/models/dtos/",
    "app\\models\\mappers\\": "application/models/mappers/"
}