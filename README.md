# GeoLayers - Sistema de Gerenciamento de Camadas Geográficas

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-4-FDAE4B)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-17-336791?logo=postgresql)
![PostGIS](https://img.shields.io/badge/PostGIS-3.5-4169E1)

Sistema web para gerenciamento e visualização de camadas geográficas com upload de arquivos GeoJSON, desenvolvido com **Laravel 12**, **Filament 4** e **PostGIS**.

---

## Índice

- [Características](#-características)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Arquitetura do Projeto](#-arquitetura-do-projeto)
- [Instalação e Configuração](#-instalação-e-configuração)
- [Como Usar](#-como-usar)
- [Estrutura de Arquivos](#-estrutura-de-arquivos)
- [Decisões Arquiteturais](#-decisões-arquiteturais)
- [API Endpoints](#-api-endpoints)
- [Credenciais de Acesso](#-credenciais-de-acesso)

---

## Características

### Painel Administrativo (`/painel`)
- CRUD completo de camadas geográficas
- Upload de arquivos GeoJSON (até 10MB)
- Validação de GeoJSON no backend
- Armazenamento de geometrias no PostgreSQL com PostGIS
- Índice espacial GIST para performance
- Interface em português com Filament 4
- Autenticação protegida por senha

### Visualizador de Mapas (`/`)
- Mapa interativo com ArcGIS Maps SDK v4.31
- Exibição de todas as camadas cadastradas
- Popups com informações das camadas
- Legenda expansível
- Zoom automático para visualizar todas as geometrias
- Consumo de dados via API REST

---

## Tecnologias Utilizadas

| Tecnologia | Versão | Finalidade |
|------------|--------|------------|
| **PHP** | 8.4 | Linguagem backend |
| **Laravel** | 12 | Framework PHP |
| **Filament** | 4.1 | Painel administrativo |
| **PostgreSQL** | 17 | Banco de dados |
| **PostGIS** | 3.5 | Extensão espacial para PostgreSQL |
| **Laravel Eloquent Spatial** | 4.5 | Manipulação de dados geográficos |
| **ArcGIS Maps SDK** | 4.31 | Renderização de mapas |
| **Docker / Sail** | - | Ambiente de desenvolvimento |

---

## Arquitetura do Projeto

Este projeto segue os princípios **SOLID** e utiliza **arquitetura em camadas** para separar responsabilidades:

```
app/
├── Actions/                    # Ações isoladas (SRP)
│   └── ProcessGeoJsonFileAction.php
├── DTOs/                       # Data Transfer Objects
│   └── LayerData.php
├── Models/                     # Eloquent Models
│   └── Layer.php
├── Repositories/               # Repository Pattern (DIP)
│   ├── LayerRepositoryInterface.php
│   └── LayerRepository.php
├── Services/                   # Lógica de negócio
│   └── LayerService.php
├── Providers/                  # Service Providers
│   └── RepositoryServiceProvider.php
├── Filament/                   # Admin Panel
│   └── Resources/Layers/
│       ├── LayerResource.php
│       ├── Schemas/LayerForm.php
│       ├── Tables/LayersTable.php
│       └── Pages/
│           ├── CreateLayer.php
│           ├── EditLayer.php
│           └── ListLayers.php
└── Http/Controllers/Api/       # Controllers REST
    └── LayerController.php
```

### Camadas da Arquitetura

```
┌─────────────────────────────────────┐
│  Interface (Filament / API / View)  │
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│        Service Layer                │  ← Orquestra lógica de negócio
│        (LayerService)                │
└─────────────────┬───────────────────┘
                  │
         ┌────────┴────────┐
         │                 │
┌────────▼────────┐  ┌────▼────────────┐
│  Actions        │  │  Repository     │  ← Abstração de dados
│  (Process GeoJSON)│  │  (Interface)  │
└─────────────────┘  └────┬────────────┘
                          │
                  ┌───────▼────────┐
                  │  Model (Layer) │  ← Eloquent + PostGIS
                  └────────────────┘
```

---

## Instalação e Configuração

### Pré-requisitos

- Docker Desktop instalado
- Git

### Passo 1: Clonar o Repositório

```bash
git clone <seu-repositorio>
cd geolayers
```

### Passo 2: Configurar Ambiente

```bash
cp .env.example .env
```

Edite o `.env` e configure (já está configurado para Docker):

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=secret
```

### Passo 3: Iniciar Containers Docker

```bash
./vendor/bin/sail up -d
```

### Passo 4: Instalar Dependências

```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

### Passo 5: Gerar Application Key

```bash
./vendor/bin/sail artisan key:generate
```

### Passo 6: Executar Migrations

```bash
./vendor/bin/sail artisan migrate
```

### Passo 7: Criar Usuário Admin (se ainda não criou)

```bash
./vendor/bin/sail artisan make:filament-user
```

**Credenciais de exemplo:**
- Nome: Admin
- Email: admin@admin.com
- Senha: Password@123

### Passo 8: Compilar Assets (opcional)

```bash
./vendor/bin/sail npm run build
```

### Passo 9: Acessar o Sistema

- **Visualizador de Mapas**: [http://localhost](http://localhost)
- **Painel Admin**: [http://localhost/painel](http://localhost/painel)

---

## Como Usar

### 1. Adicionar uma Camada Geográfica

1. Acesse `/painel` e faça login
2. Clique em **"Camadas"** no menu
3. Clique em **"Criar"**
4. Preencha:
   - **Nome da Camada**: ex: "Estados do Brasil"
   - **Arquivo GeoJSON**: faça upload de um arquivo `.geojson`
5. Clique em **"Salvar"**

### 2. Visualizar Camadas no Mapa

1. Acesse `/` (página inicial)
2. As camadas aparecerão automaticamente no mapa
3. Clique em qualquer camada para ver informações

### 3. Editar uma Camada

1. No painel admin, clique no ícone de edição
2. Atualize o nome ou faça upload de um novo GeoJSON (opcional)
3. Salve as alterações

### 4. Excluir uma Camada

1. No painel admin, clique no ícone de edição
2. Clique no botão **"Excluir"** no canto superior direito

---

## Estrutura de Arquivos

```
geolayers/
├── app/
│   ├── Actions/                    # Ações isoladas
│   ├── DTOs/                       # Data Transfer Objects
│   ├── Filament/Resources/         # Recursos do Filament
│   ├── Http/Controllers/Api/       # Controllers da API
│   ├── Models/                     # Models Eloquent
│   ├── Providers/                  # Service Providers
│   ├── Repositories/               # Repository Pattern
│   └── Services/                   # Lógica de negócio
├── database/migrations/            # Migrations
├── resources/views/                # Views Blade
│   └── home.blade.php             # Página com mapa
├── routes/
│   ├── api.php                    # Rotas da API
│   └── web.php                    # Rotas web
├── docker-compose.yml             # Configuração Docker
└── README.md
```

---

## Decisões Arquiteturais

### 1. **Repository Pattern**

**Por quê?**
- Desacopla a lógica de acesso a dados do restante da aplicação
- Facilita testes unitários (mock da interface)
- Permite trocar a implementação sem impactar outras camadas

**Exemplo:**
```php
interface LayerRepositoryInterface {
    public function all(): Collection;
}

class LayerRepository implements LayerRepositoryInterface {
    public function all(): Collection {
        return Layer::all();
    }
}
```

### 2. **Data Transfer Objects (DTOs)**

**Por quê?**
- Transfere dados entre camadas de forma imutável
- Evita acoplamento direto com Models do Eloquent
- Valida e estrutura dados de entrada

**Exemplo:**
```php
readonly class LayerData {
    public function __construct(
        public string $name,
        public Geometry $geometry
    ) {}
}
```

### 3. **Actions (Single Responsibility)**

**Por quê?**
- Cada action tem uma única responsabilidade
- Reutilizável em diferentes contextos
- Facilita testes isolados

**Exemplo:**
```php
class ProcessGeoJsonFileAction {
    public function execute(UploadedFile $file): Geometry {
        // Processa apenas o arquivo GeoJSON
    }
}
```

### 4. **Service Layer**

**Por quê?**
- Orquestra Actions e Repositories
- Contém a lógica de negócio da aplicação
- Ponto central para operações complexas

**Exemplo:**
```php
class LayerService {
    public function createLayer(string $name, UploadedFile $file): Layer {
        $geometry = $this->processGeoJsonAction->execute($file);
        return $this->repository->create(new LayerData($name, $geometry));
    }
}
```

### 5. **Dependency Injection**

**Por quê?**
- Laravel resolve dependências automaticamente
- Facilita testes (injetar mocks)
- Implementa o princípio "D" do SOLID (Dependency Inversion)

**Exemplo:**
```php
public function __construct(
    private readonly LayerRepositoryInterface $repository,
    private readonly ProcessGeoJsonFileAction $action
) {}
```

---

## API Endpoints

### `GET /api/layers`

Retorna todas as camadas em formato **GeoJSON FeatureCollection**.

**Resposta:**
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "id": 1,
      "properties": {
        "name": "Estados do Brasil",
        "created_at": "2025-11-02T23:00:00.000000Z"
      },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[[...]]]
      }
    }
  ]
}
```

### `GET /api/layers/{id}`

Retorna uma camada específica em formato **GeoJSON Feature**.

**Resposta:**
```json
{
  "type": "Feature",
  "id": 1,
  "properties": {
    "name": "Estados do Brasil",
    "created_at": "2025-11-02T23:00:00.000000Z"
  },
  "geometry": {
    "type": "Polygon",
    "coordinates": [[[...]]]
  }
}
```

---

## Credenciais de Acesso

### Painel Administrativo

- **URL**: http://localhost/painel
- **Email**: admin@admin.com
- **Senha**: Password@123

---

## Executar Testes

```bash
./vendor/bin/sail artisan test
```

---

## Licença

Este projeto é um **teste técnico** para vaga de PHP Sênior.

---

## Autor

Desenvolvido com Laravel 12, Filament 4, PostGIS e ArcGIS Maps SDK v4.

---

## Referências

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Filament 4 Documentation](https://filamentphp.com/docs)
- [Laravel Eloquent Spatial](https://github.com/MatanYadaev/laravel-eloquent-spatial)
- [ArcGIS Maps SDK for JavaScript](https://developers.arcgis.com/javascript/latest/)
- [PostGIS Documentation](https://postgis.net/documentation/)

---

## Conceitos Técnicos Demonstrados

**SOLID Principles**
- Single Responsibility (Actions)
- Open/Closed (Interfaces)
- Liskov Substitution (Repository)
- Interface Segregation (Contracts específicos)
- Dependency Inversion (Service Provider)

**Design Patterns**
- Repository Pattern
- Dependency Injection
- Data Transfer Object (DTO)
- Service Layer
- Factory Method (Actions)

**Boas Práticas**
- Separation of Concerns
- Clean Code
- RESTful API
- Database Indexing (Spatial Index)
- Validation & Error Handling
