# LaSuiteAPI

Sistema de gerenciamento de quartos, tarifas, disponibilidades e reservas para hotelaria.

Construído com **Laravel 12**, utilizando arquitetura **MVC**, **Service Layer**, **DTOs** e práticas avançadas de **Clean Code**.

O projeto segue os princípios **SOD** do SOLID:
- **S**: Single Responsibility — Cada classe tem uma única responsabilidade clara.
- **O**: Open/Closed — O sistema é facilmente extensível sem necessidade de modificar código existente.
- **D**: Dependency Inversion — Utiliza injeção de dependências e abstração para desacoplar componentes.

---

## 💳 Módulo de Pagamentos

- ✅ Integração com a **Cielo API** para processamento de pagamentos via **PIX** e **Cartão de Crédito/Débito**.
- ✅ Arquitetura de pagamentos totalmente **desacoplada**, seguindo princípios de **Dependency Inversion**, permitindo fácil integração com novos gateways bancários.
- ✅ Implementação baseada em **Drivers/Processors**, permitindo adicionar outros bancos ou métodos de pagamento (ex: Itaú, Bradesco, Stripe) sem alterar o core do sistema.
- ✅ Suporte a novos meios de pagamento com mínimo esforço, mantendo o sistema escalável e de fácil manutenção.

---

## 🚀 Tecnologias Utilizadas

- PHP 8.2+
- Laravel 12
- MySQL / PostgreSQL
- Docker / Laravel Sail (opcional)
- Laravel Sanctum (para autenticação de API)
- Swagger/OpenAPI para documentação da API
- Integração com **Cielo API 3.0**

---

## 📦 Instalação do Projeto

### 1. Clone o repositório

```bash
git clone https://github.com/rodrigodenner/LaSuiteAPI.git
cd LaSuiteAPI
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o ambiente

Copie o `.env.example` para `.env`:

```bash
cp .env.example .env
```

Atualize as variáveis de ambiente:

```
APP_NAME=LaSuiteAPI
APP_ENV=local
APP_KEY= # Será gerado no próximo passo
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lasuite_db
DB_USERNAME=root
DB_PASSWORD=secret

# Configurações da Cielo API
CIELO_MERCHANT_ID=seu_merchant_id
CIELO_MERCHANT_KEY=sua_merchant_key
CIELO_ENVIRONMENT=sandbox # ou production
```

---

### 4. Gere a chave da aplicação

```bash
php artisan key:generate
```

---

### 5. Execute as migrations e seeders

```bash
php artisan migrate --seed
```

---

### 6. (Opcional) Rode o projeto com Laravel Sail (Docker)

```bash
./vendor/bin/sail up
```

---

## 🔒 Autenticação

O sistema utiliza autenticação via **Laravel Sanctum**.

1. Registre um novo usuário via API.
2. Realize o login para obter o token.
3. Envie o token no cabeçalho das requisições:

```
Authorization: Bearer {seu-token}
```

---

## 📚 Documentação da API

A documentação da API está disponível via Swagger.

Após iniciar o projeto, acesse:

```
http://localhost/api/documentation
```

---

## 🧪 Executando os Testes

Para rodar os testes de API e unidades:

```bash
php artisan test
```

Ou, caso utilize Sail:

```bash
./vendor/bin/sail test
```

---

## 🛠️ Estrutura de Pastas

- `app/Models` — Modelos Eloquent
- `app/Http/Controllers` — Controladores da API
- `app/Http/Requests` — Validações específicas
- `app/Services` — Regras de negócio separadas dos controllers
- `app/DTOs` — Data Transfer Objects
- `app/Payments/Processors` — Processadores de pagamento desacoplados (Cielo, outros bancos)
- `database/migrations` — Scripts de banco de dados

---

## 📝 Licença

Este projeto está licenciado sob a Licença MIT — veja o arquivo [LICENSE](LICENSE) para mais detalhes.
