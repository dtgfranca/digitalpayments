# Digital Payments API

Este projeto é uma API de pagamentos digitais simplificada, desenvolvida com o framework Laravel. A aplicação permite a criação de usuários (clientes e lojistas), realização de depósitos em carteiras e transferências de dinheiro entre usuários, seguindo regras de negócio específicas para cada tipo de perfil.

## Tecnologias e Bibliotecas Utilizadas

As principais ferramentas utilizadas no desenvolvimento deste projeto foram:

- **[Laravel 12](https://laravel.com/)**: Framework PHP principal para o desenvolvimento da aplicação.
- **[JWT-auth (php-open-source-saver/jwt-auth)](https://github.com/PHP-Open-Source-Saver/jwt-auth)**: Utilizado para autenticação segura baseada em tokens JSON Web Tokens.
- **[L5-Swagger](https://github.com/DarkaOnline/L5-Swagger)**: Integração do Swagger/OpenAPI para documentação interativa da API.
- **[PHPUnit](https://phpunit.de/)**: Framework de testes para garantir a qualidade e o funcionamento do código.
- **[Docker & Docker Compose](https://www.docker.com/)**: Utilizados para a containerização da aplicação, facilitando o ambiente de desenvolvimento local.
- **MySQL**: Banco de dados relacional utilizado para persistência dos dados.

## Estrutura de Pastas

O projeto segue uma arquitetura inspirada em **Domain-Driven Design (DDD)** e **Clean Architecture**, organizada da seguinte forma:

- `app/Domain`: Contém o núcleo do negócio (entidades, objetos de valor, interfaces de repositório e regras de negócio puras).
- `app/Application`: Contém os casos de uso (Use Cases) e DTOs, orquestrando a lógica de negócio entre a infraestrutura e o domínio.
- `app/Infrastructure`: Implementações técnicas de detalhes, como persistência de banco de dados (Eloquent), adaptadores de serviços externos (autorizadores, notificadores) e provedores.
- `app/Http`: Camada de entrega via HTTP, contendo Controllers, Requests e a configuração de rotas API.
- `app/Models`: Modelos do Eloquent para mapeamento objeto-relacional (ORM).
- `database/`: Migrações e seeders do banco de dados.
- `routes/`: Definições de rotas da aplicação (principalmente `api.php`).
- `tests/`: Testes automatizados (Unitários e de Funcionalidade/Feature).

##  Configuração e Execução Local

Siga os passos abaixo para configurar o projeto em seu ambiente local utilizando Docker:

### Pré-requisitos
- Docker instalado.
- Docker Compose instalado.

### Passo a Passo

1. **Clonar o Repositório:**
   ```bash
   git clone <url-do-repositorio>
   cd digitalpaments
   ```

2. **Subir os Containers:**
   Este comando irá construir as imagens e iniciar os serviços de aplicação, banco de dados e servidor web (Nginx). O script de entrypoint cuidará da instalação das dependências do Composer, cópia do `.env`, geração de chaves e execução das migrações.
   ```bash
   docker compose up -d --build
   ```

3. **Verificar os Logs (Opcional):**
   Aguarde até que o processo de instalação e migração termine. Você pode acompanhar pelos logs:
   ```bash
   docker compose logs -f app
   ```

4. **Acessar a Aplicação:**
   A API estará disponível em: `http://localhost:8000`

## Documentação da API (Swagger)

A documentação interativa das rotas pode ser acessada diretamente pelo navegador. Lá você encontrará os endpoints disponíveis, os parâmetros necessários e os modelos de resposta.

- **URL do Swagger:** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

## 🧪 Executando Testes

Para rodar os testes automatizados da aplicação, utilize o comando abaixo dentro do container:

```bash
docker compose exec app php artisan test
```

##  Observação sobre Testes End-to-End

Os testes end-to-end (E2E) deste projeto realizam integração com uma API externa indicada no enunciado do desafio.  
Por esse motivo, eventualmente esses testes podem apresentar comportamento flaky, dependendo da disponibilidade, latência ou instabilidade desse serviço externo.

Essa decisão foi tomada para manter o cenário de testes o mais próximo possível de um ambiente real de integração.  
Os testes unitários e de integração interna, por outro lado, não dependem de serviços externos e perma

## Fluxo do Sistema

1. Cadastro do usuário como **Regular** ou **Merchant**
2. Login com email e senha
3. Recebimento do token JWT
4. Uso do token para consultar saldo, realizar depósitos e transferências

## Arquitetura

As decisões técnicas e arquiteturais deste projeto, incluindo o uso de **Domain-Driven Design (DDD)**, **CQRS**, geração de identificadores fora do banco de dados, padrões de projeto e estratégias de escalabilidade, estão documentadas no arquivo abaixo:

➡️ **[Architecture & Decisions](./architecture.md)**
