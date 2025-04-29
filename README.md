# LaSuiteAPI

Sistema de gerenciamento de quartos, tarifas, disponibilidades e reservas para hotelaria.

Construído com **Laravel 12**, utilizando arquitetura MVC, Service Layer, DTOs e boas práticas de Clean Code.

---

## 🚀 Tecnologias Utilizadas

- PHP 8.2+
- Laravel 12
- MySQL / PostgreSQL
- Docker / Laravel Sail (opcional)
- JWT ou Laravel Sanctum (para autenticação de API)
- Swagger/OpenAPI para documentação da API

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

ou, caso utilize Sail:

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
- `database/migrations` — Scripts de banco de dados

---

## 🤝 Contribuindo

Pull requests são bem-vindos!  
Para mudanças significativas, por favor abra uma issue antes para discutir o que você gostaria de alterar.

---

## 🧑‍💻 Autor

- **Rodrigo Denner** — [GitHub](https://github.com/rodrigodenner)

---

## 📝 Licença

Este projeto está licenciado sob a Licença MIT — veja o arquivo [LICENSE](LICENSE) para mais detalhes.
