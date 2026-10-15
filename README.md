# io.edu — Plataforma de Educação (LMS)

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.2-777bb4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Framework](https://img.shields.io/badge/Framework-CodeIgniter%203%20(Modernized)-ee4623?style=flat-square&logo=codeigniter&logoColor=white)](https://codeigniter.com/)
[![Database](https://img.shields.io/badge/Database-MySQL%208.0-4479a1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ed?style=flat-square&logo=docker&logoColor=white)](https://www.docker.com/)
[![Storage](https://img.shields.io/badge/Storage-AWS%20S3%20%2F%20LocalStack-ff9900?style=flat-square&logo=amazons3&logoColor=white)](https://aws.amazon.com/s3/)
[![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%205.3-7952b3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![PHPUnit Tests](https://img.shields.io/badge/Tests-PHPUnit%209-3c8dbc?style=flat-square&logo=php&logoColor=white)](https://phpunit.de/)

O **io.edu** é uma plataforma moderna e completa de gestão de aprendizagem (LMS — *Learning Management System*), projetada com foco em alta performance, clareza arquitetural e facilidade de manutenção. 

O projeto moderniza o **CodeIgniter 3** aplicando os princípios de **Domain-Driven Design simplificado (DDD-Lite)** sobre **PHP 8.2+**, garantindo tipagem estrita, inversão de controle, separação estrita de camadas e desacoplamento do framework no núcleo de negócio.

---

## Sumário

- [Visão Geral & Arquitetura](#visão-geral--arquitetura)
- [Funcionalidades Principais](#funcionalidades-principais)
- [Stack Tecnológica](#stack-tecnológica)
- [Pré-requisitos](#pré-requisitos)
- [Guia de Instalação (Quick Start)](#guia-de-instalação-quick-start)
- [Acessos & Rotas](#acessos--rotas)
- [Testes Automatizados](#testes-automatizados)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Comandos Úteis de Desenvolvimento](#comandos-úteis-de-desenvolvimento)

---

## Visão Geral & Arquitetura

O sistema adota uma arquitetura em camadas orientada ao domínio (**DDD-Lite**), garantindo que as regras de negócio permaneçam puras, testáveis e independentes de mecanismos de entrega ou banco de dados:

```text
HTTP Request
     │
     ▼
[ Presentation Layer ] ──▶ Controllers (MY_Controller) & Views (Bootstrap 5.3 + Design System)
     │
     ▼
[ Application Layer ]  ──▶ Use Cases (Orquestração de regras de negócio, 1 caso de uso por ação)
     │
     ▼
[ Domain Layer ]       ──▶ Entities, Value Objects, Domain Exceptions, Repository Interfaces
     │
     ▼
[ Infrastructure ]     ──▶ Models (MY_Model), Database DTOs, Mappers & S3 Storage Adapter
     │
     ▼
Database (MySQL 8.0) / Object Storage (AWS S3)
```

### Camadas e Responsabilidades

| Camada | Diretório | Responsabilidade |
|---|---|---|
| **Presentation** | `application/controllers/`, `views/` | Trata requisições HTTP, valida formulários de entrada e retorna respostas HTML ou JSON formatadas. Não contém lógica de negócio. |
| **Application** | `application/usecases/` | Orquestra casos de uso individuais (ex: `CreateCourseUseCase`, `InitiateMultipartUploadUseCase`). Recebe dependências via construtor. |
| **Domain** | `application/domain/` | Contém entidades puras em PHP, objetos de valor (Value Objects), constantes e contratos de repositório. Totalmente desacoplada do CI3. |
| **Infrastructure** | `application/models/`, `libraries/` | Implementa a persistência via `MY_Model`, manipula tipagem com Database DTOs, traduz com Mappers e integra serviços como AWS S3. |

### Inversão de Controle & Factories
- **[ModelFactory](application/factories/ModelFactory.php)**: Resolve instâncias de models do framework injetando-os como contratos de repositório nos Use Cases.
- **[LibraryFactory](application/factories/LibraryFactory.php)**: Carrega bibliotecas e adaptadores de infraestrutura (como `Storage_library` implementando `StorageServiceInterface`) sem acoplamento a globais.

---

## Funcionalidades Principais

### 1. Construtor de Cursos & Gestão Curricular
- **Estrutura Hierárquica**: Organização intuitiva em Cursos ➔ Módulos ➔ Aulas ➔ Blocos de Conteúdo.
- **Blocos de Conteúdo Polimórficos**:
  - **Vídeo**: Integração com streaming, metadados de duração e controle de resolução.
  - **Texto Rico**: Editor WYSIWYG moderno (Quill.js) com sanitização de segurança em HTMLPurifier.
  - **Anexos**: Upload e disponibilização de arquivos complementares para download.
- **Reordenação Interativa**: Ajuste ágil da ordem de exibição de módulos, aulas e blocos.

### 2. Controle de Acesso Baseado em Papéis (RBAC)
- Perfis de acesso configuráveis (`AdminMaster`, `Admin`, `Student`).
- Matriz granular de permissões por módulo e ação.
- Middleware global interceptando requisições no ciclo de vida do controller (`post_controller_constructor`).
- Hashing moderno e seguro de credenciais via `password_hash(PASSWORD_BCRYPT)`.

### 3. Upload Multipart Direto para S3 / MinIO
- Suporte a upload fragmentado (*multipart upload*) para arquivos pesados de mídia.
- Presigned URLs geradas pelo backend para tráfego direto entre navegador e storage, sem sobrecarregar a memória do servidor PHP.
- Emulador de S3 local integrado via **LocalStack** para desenvolvimento off-line.

### 4. Design System Centralizado (`.edu-*`)
- Design moderno, limpo e responsivo baseado em Bootstrap 5.3 e tokens CSS centralizados (`--edu-*`).
- Suporte a identidade visual configurável (branding) e temas de cores.
- Animações fluidas de carregamento (`animate-fade-up`).
- Componente de listagem assíncrona com DataTables e toolbar desacoplada.

---

## Stack Tecnológica

- **Linguagem**: PHP >= 8.2 (FPM)
- **Framework Base**: CodeIgniter 3.x (Modernizado com PSR-4 e autoload Composer)
- **Banco de Dados**: MySQL 8.0 (com triggers automáticas para auditoria de timestamps)
- **Armazenamento de Objetos**: AWS S3 SDK PHP / LocalStack 3.8
- **Front-end**: Bootstrap 5.3.8, Quill.js 2.0.3, DataTables, HTMLPurifier, CSS Tokens
- **Ambiente & Conteinerização**: Docker, Docker Compose, Nginx (Alpine)
- **Testes Unitários**: PHPUnit 9.6

---

## Pré-requisitos

Antes de iniciar, certifique-se de ter instalado em sua máquina:
- [Docker](https://docs.docker.com/get-docker/) (versão >= 24.0)
- [Docker Compose](https://docs.docker.com/compose/install/) (versão >= 2.20)
- [Git](https://git-scm.com/)

---

## Guia de Instalação (Quick Start)

### 1. Clonar o Repositório
```bash
git clone https://github.com/seu-usuario/io.edu.git
cd io.edu
```

### 2. Configurar Variáveis de Ambiente
Copie o arquivo de exemplo e ajuste as configurações conforme necessário:
```bash
cp .env.example .env
```

### 3. Iniciar os Containers Docker
Suba a stack completa em segundo plano (Nginx, PHP-FPM, MySQL e LocalStack):
```bash
docker compose up -d
```

> **Nota**: No primeiro início, o script de inicialização do container executará o `composer install` e provisionará os assets automaticamente.

### 4. Executar as Migrações do Banco de Dados
Aplique o schema do banco de dados e os seeds iniciais de dados:
```bash
docker compose exec app php index.php migrations migrate
```

Pronto! A aplicação estará acessível no seu navegador em `http://localhost:8080`.

---

## Acessos & Rotas

Após executar as migrações, as seguintes rotas e serviços estarão acessíveis:

| Recurso | URL | Descrição / Área |
|---|---|---|
| **Catálogo de Cursos** | `http://localhost:8080/` | Vitrine pública de cursos disponíveis |
| **Autenticação** | `http://localhost:8080/entrar` | Formulário de acesso ao sistema |
| **Painel Administrativo** | `http://localhost:8080/admin/painel` | Gestão de cursos, currículo, usuários e perfis |
| **Painel do Aluno** | `http://localhost:8080/aluno/painel` | Ambiente de estudo e acompanhamento de aulas |
| **LocalStack S3 (Storage)** | `http://localhost:4566` | Emulador de armazenamento de objetos S3 |

---

## Testes Automatizados

A suíte de testes unitários cobre o domínio, casos de uso e regras de negócio da aplicação de forma isolada:

### Executar Testes Unitários
```bash
docker compose exec app vendor/bin/phpunit tests/unit/
```

### Executar Testes com Cobertura de Código
```bash
docker compose exec app composer test:coverage
```

---

## Estrutura do Projeto

```text
io.edu/
├── application/
│   ├── config/              # Configurações do framework, rotas e autoload
│   ├── controllers/         # Presentation layer: rotas públicas, auth, admin e student
│   ├── core/                # MY_Controller e MY_Model estendidos
│   ├── domain/              # Camada de Domínio pura (Entidades, Value Objects, Interfaces)
│   ├── factories/           # ModelFactory e LibraryFactory para injeção de dependências
│   ├── hooks/               # Middlewares de sessão, permissão RBAC e detecção de idioma
│   ├── libraries/           # Bibliotecas e adaptadores de infraestrutura (ex: Storage_library)
│   ├── migrations/          # Versionamento do schema e seeds do banco de dados
│   ├── models/              # Infrastructure: Mappers, Database DTOs e repositórios concretos
│   ├── usecases/            # Application layer: Casos de uso de negócio orquestrados
│   └── views/               # Views e layouts Blade-like (admin, student, auth, public)
├── docker/                  # Configurações de infraestrutura Docker (Nginx, PHP, LocalStack)
├── public/                  # Document root web (index.php, CSS, JS, assets)
├── tests/                   # Suíte de testes automatizados (unit, integration, mocks)
├── .env.example             # Modelo de variáveis de ambiente
├── composer.json            # Dependências PHP e mapeamento PSR-4
├── docker-compose.yml       # Orquestração dos serviços da aplicação
└── phpunit.xml              # Configuração da suíte de testes do PHPUnit
```

---

## Comandos Úteis de Desenvolvimento

### Gerenciamento de Migrações
```bash
# Ver status atual das migrações
docker compose exec app php index.php migrations status

# Executar todas as migrações pendentes
docker compose exec app php index.php migrations migrate

# Reverter para uma versão anterior específica
docker compose exec app php index.php migrations rollback <versao>
```

### Dependências & Autoload
```bash
# Instalar ou atualizar pacotes Composer
docker compose exec app composer install

# Regenerar o classmap do Composer após criar novas classes
docker compose exec app composer dump-autoload
```

### Logs da Aplicação e Containers
```bash
# Visualizar logs em tempo real da aplicação
docker compose logs -f app

# Visualizar logs do banco de dados
docker compose logs -f db
```
