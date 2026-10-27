# Segurança (Security Policy & Advisories)

## Atualização Crítica do PHP (<= 8.2.34)

Foi identificada recentemente uma **vulnerabilidade crítica de segurança** nas versões do PHP anteriores à **8.2.35**.

Como o [Dockerfile](Dockerfile) utiliza a imagem base `php:8.2-fpm`, o Docker pode manter em cache local uma versão anterior vulnerável caso a imagem já tenha sido baixada previamente na máquina ou no servidor.

### Como atualizar o ambiente Docker

Para garantir que o container utilize a versão corrigida (**PHP 8.2.35** ou superior), force a atualização da imagem base e recrie os containers executando os comandos abaixo:

```bash
docker compose build --pull
docker compose up -d
```

### Verificando a versão instalada

Após recriar o container, confirme se a versão em execução é igual ou superior à `8.2.35`:

```bash
docker compose exec app php -v
```
