#  Sistema de Benefícios Corporativos

Sistema desenvolvido em Laravel para gerenciamento de benefícios corporativos, permitindo que colaboradores solicitem diversos tipos de benefícios como vale-alimentação, vale-combustível, convênios e cartões-presente.

##  Índice
- [Tecnologias](#-tecnologias)
- [Funcionalidades](#-funcionalidades)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Como Usar](#-como-usar)
- [Testes](#-testes)
- [Documentação API](#-documentação-api)
- [Documento de Pensamento Crítico](#-documento-de-pensamento-crítico)

##  Tecnologias

- **PHP 8.4.2+**
- **Laravel 12** - Framework PHP
- **MySQL** - Banco de dados relacional
- **JWT Authentication** - Autenticação via tokens
- **Docker** - Containerização
- **PHPUnit** - Testes automatizados
- **Swagger/OpenAPI** - Documentação da API

##  Funcionalidades

###  Implementadas
-  Autenticação JWT com refresh token
-  CRUD de benefícios (listagem e visualização)
-  Sistema de solicitação de benefícios
-  Aprovação simples e dupla
-  Regras de negócio complexas
-  Validações robustas
-  Testes automatizados (91% de cobertura)

###  Regras de Negócio Implementadas

1. **Limite Mensal**
   - Máximo 1 solicitação por tipo de benefício por mês
   - Controle por usuário e tipo de benefício

2. **Aprovação Dupla**
   - Alguns benefícios requerem 2 aprovações diferentes
   - Mesmo aprovador não pode aprovar duas vezes
   - Status intermediário "Aprovação Dupla Pendente"

3. **Validação de Valores**
   - Respeita limite máximo por benefício
   - Validação de valor mínimo quando aplicável

4. **Controle de Acesso**
   - Funcionários: podem solicitar benefícios
   - Aprovadores: podem aprovar solicitações
   - Admins: acesso total ao sistema

5. **Regras Específicas por Benefício**
   - Justificativa obrigatória
   - Restrição por departamento
   - Valores mínimos personalizados

##  Instalação

### Opção 1: Docker (Recomendado)

```bash
# Clone o repositório
git clone <repo-url>
cd sistema-beneficios

# Suba os containers
docker-compose up -d

# Acesse o container da aplicação
docker exec -it laravel_app bash

# Instale as dependências
composer install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute as migrações e seeders
php artisan migrate --seed

# Gere a chave JWT
php artisan jwt:secret
```

### Opção 2: Instalação Local

```bash
# Pré-requisitos
# - PHP 8.4.2
# - Composer
# - MySQL 8.0+

# Clone e configure
git clone <repo-url>
cd sistema-beneficios
composer install

# Configure o banco
cp .env.example .env
# Edite o .env com suas configurações de banco - recomendo utilizar uma imagem MySQL

php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed

# Inicie o servidor
php artisan serve
```

##  Configuração

### Variáveis de Ambiente

```env
APP_NAME="Sistema Benefícios"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_beneficios_api
DB_USERNAME=admin
DB_PASSWORD=123

JWT_SECRET=sua_chave_jwt_aqui
JWT_TTL=60
```

### Usuários de Teste

Após executar `php artisan db:seed`, os seguintes usuários estarão disponíveis:

```
Admin:
- Email: admin@empresa.com
- Senha: 123456
- Tipo: admin

Aprovador:
- Email: aprovador@empresa.com
- Senha: 123456
- Tipo: aprovador

Funcionários:
- Email: johndee@empresa.com / Senha: 123456
- Email: fulano@empresa.com / Senha: 123456
```

##  Como Usar

### 1. Autenticação

```bash
# Realizar o login do usuário
POST /api/login
{
  "email": "admin@empresa.com",
  "senha": "123456"
}

# Resposta
{
  "sucesso": true,
  "dados": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "tipo_token": "bearer",
    "expira_em": 3600,
    "usuario": {
      "id": 1,
      "nome": "Administrador do Sistema",
      "tipo": "admin"
    }
  }
}
```

### 2. Listar Benefícios

```bash
GET /api/beneficios
Authorization: Bearer {token}
```

### 3. Solicitar Benefício

```bash
POST /api/solicitacoes
Authorization: Bearer {token}
{
  "beneficio_id": 1,
  "valor_solicitado": 300.00,
  "justificativa": "Necessário para alimentação"
}
```

### 4. Aprovar Solicitação

```bash
POST /api/solicitacoes/{id}/aprovar
Authorization: Bearer {token}
{
  "observacoes": "Aprovado pelo gestor"
}
```

### Endpoints Principais

```
Autenticação:
POST   /api/login
POST   /api/logout
POST   /api/refresh
GET    /api/perfil

Benefícios:
GET    /api/beneficios
GET    /api/beneficios/{id}

Solicitações:
GET    /api/solicitacoes
POST   /api/solicitacoes
GET    /api/solicitacoes/{id}
GET    /api/solicitacoes/pendentes-aprovacao
POST   /api/solicitacoes/{id}/aprovar
POST   /api/solicitacoes/{id}/rejeitar
```

##  Testes

Execute os testes automatizados:

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter=AuthTest
php artisan test --filter=BeneficioTest
php artisan test --filter=SolicitacaoBeneficioTest

# Com relatório de cobertura
php artisan test --coverage
```

### Cobertura de Testes

- **Autenticação**: Login, logout, refresh token, perfil
- **Benefícios**: Listagem, detalhes, filtros
- **Solicitações**: Criação, aprovação simples/dupla, rejeição
- **Regras de Negócio**: Validações, limites, permissões

##  Documentação API

A documentação completa da API está disponível via Swagger:

```bash
# Gere a documentação
php artisan l5-swagger:generate

# Acesse em: http://localhost:8000/api/documentation
```

##  Documento de Pensamento Crítico

### Modelagem de Dados

**Decisões de Design:**

1. **Separação de Responsabilidades**
   - `Usuario`: Centraliza informações dos usuários com tipos diferentes (funcionário, aprovador, admin)
   - `Beneficio`: Entidade flexível que suporta diferentes tipos e regras
   - `SolicitacaoBeneficio`: Processo completo de solicitação com rastreabilidade

2. **Flexibilidade nas Regras**
   - Campo `regras` JSON no benefício permite regras customizadas sem alterar schema
   - Enum `StatusSolicitacao` facilita controle de estado
   - Suporte a aprovação dupla com campos específicos

**Vantagens:**
- Schema flexível e extensível
- Rastreabilidade completa das aprovações
- Suporte a regras de negócio complexas

**Limitações:**
- Regras em JSON podem dificultar queries complexas
- Não suporta workflow dinâmico de aprovação

### Por que usei o MVC Comum?

O padrão **MVC (Model-View-Controller)** foi escolhido para a arquitetura deste projeto devido às seguintes razões:

1. **Integração com o Laravel**  
   O Laravel é construído nativamente com o padrão MVC, oferecendo diversas ferramentas já integradas 

2. **Separação de Responsabilidades**  
   O MVC organiza o código em camadas claras:
   - **Model**: Gerencia dados e interações com o banco (ex.: `Usuario`, `Beneficio`, `SolicitacaoBeneficio`).
   - **View**: Substituída por respostas JSON formatadas via Resources para a API.
   - **Controller**: Coordena requisições HTTP e fornce a lógica de negócio aos Services.
   Essa estrutura facilita a manutenção e a escalabilidade do sistema

3. **Simplicidade e Produtividade**  
   A estrutura padrão do Laravel permitiu configurar rapidamente a aplicação, atendendo à necessidade de entrega no curto prazo.


**Conclusão**: O MVC comum foi escolhido por sua simplicidade, integração nativa com o Laravel, suporte a testes e adequação ao escopo do projeto.

### Estrutura da Aplicação

**Arquitetura em Camadas:**

```
Controllers/     → Ponto de entrada das requisições
├─ Requests/     → Validação de entrada
├─ Resources/    → Formatação de saída
├─ Services/     → Lógica de negócio
├─ Models/       → Acesso aos dados
├─ Policies/     → Controle de acesso
└─ Enums/        → Constantes tipadas
```


### Prioridades de Desenvolvimento

**1. Segurança (Alta)**
- JWT com expiração configurável
- Middleware de autenticação robusto
- Policies para controle de acesso granular
- Validação rigorosa de entrada

**2. Regras de Negócio (Alta)**
- Validações no service layer (especialmente nas aprovações de benefícios)
- Transações para operações críticas
- Regras flexíveis via JSON

**3. Testabilidade (Média)**
- 91% de cobertura de testes
- Testes de integração e unidade

**4. Manutenibilidade (Média)**
- Código limpo e documentado
- Separação clara de responsabilidades
- Enums para constantes tipadas

### Melhorias Possíveis

**Curto Prazo:**

1. **Cache de Benefícios**
   ```php
   // Implementar cache Redis para benefícios ativos
   Cache::remember('beneficios_ativos', 3600, fn() => Beneficio::ativos()->get());
   ```

2. **Logs**
   ```php
   // Criação de alguns logs personalizados, podendo ser categorizados por criticidade
   Log::info('Solicitação aprovada', ['user' => $aprovador->id, 'solicitacao' => $id]);
   ```


**Por que usei Laravel?**
- Ecossistema maduro do PHP com JWT, Swagger e módulo de testes já integrado
- Um bom ORM para relacionamentos complexos
- Middleware e Service Container para arquitetura escalável
- Artisan commands para automação
- Fácil de iniciar uma nova aplicação (bom para o curto prazo da entrega)

**Por que MySQL?**
- JSON columns para regras flexíveis
- Performance adequada para a escala esperada (Não foram necessários os diferenciais mais complexos do Postgres)
- Suporte robusto a relacionamentos

**Por que JWT?**
- Maior segurança
- Stateless para escalabilidade
- Payload customizado com tipo de usuário
- Padrão da indústria

---

## 👨‍💻 Desenvolvedor

**Luiz Chimenes**
- GitHub: https://github.com/luizchimenes
- Email: gustavo.chimenesp@gmail.com

---
