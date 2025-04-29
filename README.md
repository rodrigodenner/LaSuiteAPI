# 🏨 La Suite API

Esta é a API responsável pela gestão de reservas de quartos de hotel, controle de disponibilidade, tarifas, e integração entre o sistema web e o sistema local do hotel.

---

## 📄 Documentação da API

A documentação completa (Swagger/OpenAPI) está disponível em:


Substitua `<seu-dominio>` pela URL do seu ambiente (produção, homologação ou localhost).
```bash
<seu-dominio>/api/documentation
```

A documentação fornece:
- Descrição dos endpoints
- Parâmetros esperados
- Exemplos de respostas
- Detalhes de autenticação

---


## 🔑 Autenticação

### 🎯 **Como gerar o Token de Acesso (Admin)**

Utilize o endpoint de geração de token (exemplo: `POST /generate-token`) enviando as seguintes credenciais padrão:

```json
{
  "email": "admin@webav.com.br",
  "password": "110308dj"
}

