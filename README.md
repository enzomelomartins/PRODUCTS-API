# GEMINI-API

## **Descrição**
O projeto **GEMINI-API** é uma API desenvolvida em Laravel para gerenciar categorias, produtos e anexos. Ele utiliza uma arquitetura baseada em serviços e repositórios, com suporte a validações, tratamento de exceções e respostas padronizadas em JSON.

---

## **Requisitos do Sistema**

- **PHP**: Versão 8.1 ou superior
- **Composer**: Versão 2.0 ou superior
- **Banco de Dados**: MySQL 8.0 ou superior (ou outro banco compatível com Laravel)
- **Extensões PHP**:
  - `pdo`
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `fileinfo`
- **Servidor Web**: Apache ou Nginx
- **Node.js** (opcional): Para gerenciar dependências front-end, se necessário.

---

## **Instalação**

### 1. **Clonar o Repositório**
Clone o repositório do projeto para sua máquina local:
```bash
git clone https://github.com/seu-usuario/gemini-api.git
cd gemini-api
```

### 2. **Instalar Dependências**
Instale as dependências do projeto usando o Composer:
```bash
composer install
```

### 3. **Configurar o Arquivo `.env`**
Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente:
```bash
cp .env.example .env
```

Edite o arquivo `.env` para configurar o banco de dados e outras variáveis:
```env
APP_NAME=GEMINI-API
APP_ENV=local
APP_KEY=base64:gerar-sua-chave
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gemini_api
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4. **Gerar a Chave da Aplicação**
Gere a chave da aplicação:
```bash
php artisan key:generate
```

---

## **Migração e Seeders**

### 1. **Executar as Migrações**
Execute as migrações para criar as tabelas no banco de dados:
```bash
php artisan migrate
```

### 2. **Executar os Seeders (Opcional)**
Se houver seeders configurados, execute-os para popular o banco de dados com dados iniciais:
```bash
php artisan db:seed
```

---

## **Servir a Aplicação**

Inicie o servidor de desenvolvimento do Laravel:
```bash
php artisan serve
```

Acesse a aplicação em: [http://localhost:8000](http://localhost:8000)

---

## **Estrutura do Projeto**

### **Principais Diretórios**
- **`app/Http/Controllers/Api/V1`**: Controladores da API.
- **`app/Http/Middleware`**: Middlewares personalizados, como `ApiResponseMiddleware`.
- **`app/Models`**: Modelos Eloquent, como `Category`, `Product` e `Attachment`.
- **`app/Services`**: Camada de serviços para lógica de negócios.
- **`app/Repositories`**: Camada de repositórios para acesso ao banco de dados.
- **`app/Exceptions`**: Tratamento de exceções personalizadas.
- **`routes/api.php`**: Definição das rotas da API.

---

## **Rotas da API**

### **Categorias**
- **GET** `/api/v1/categories`: Listar categorias.
- **POST** `/api/v1/categories`: Criar uma nova categoria.
- **GET** `/api/v1/categories/{id}`: Obter detalhes de uma categoria.
- **PUT** `/api/v1/categories/{id}`: Atualizar uma categoria.
- **DELETE** `/api/v1/categories/{id}`: Deletar uma categoria.

### **Produtos**
- **GET** `/api/v1/products`: Listar produtos.
- **POST** `/api/v1/products`: Criar um novo produto.
- **GET** `/api/v1/products/{id}`: Obter detalhes de um produto.
- **PUT** `/api/v1/products/{id}`: Atualizar um produto.
- **DELETE** `/api/v1/products/{id}`: Deletar um produto.

### **Anexos**
- **POST** `/api/v1/products/{product}/attachments`: Fazer upload de um anexo para um produto.
- **GET** `/api/v1/attachments/{id}`: Obter detalhes de um anexo.
- **DELETE** `/api/v1/attachments/{id}`: Deletar um anexo.

---

## **Tratamento de Exceções**

O projeto utiliza a classe `ApiExceptionHandler` para capturar e formatar exceções em respostas JSON. Exemplo de resposta para uma rota inexistente:

```json
{
    "success": false,
    "status": 404,
    "message": "Rota não encontrada.",
    "errors": null,
    "debug": {
        "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
        "message": "",
        "trace": [...]
    }
}
```

---

## **Testes**

### 1. **Testes Automatizados**
Se houver testes configurados, execute-os com:
```bash
php artisan test
```

### 2. **Testar Manualmente**
- Use ferramentas como **Postman** ou **Insomnia** para testar as rotas da API.
- Certifique-se de enviar os cabeçalhos e payloads corretos para cada rota.

---

## **Contribuição**

1. Faça um fork do repositório.
2. Crie uma branch para sua feature:
   ```bash
   git checkout -b minha-feature
   ```
3. Faça commit das suas alterações:
   ```bash
   git commit -m "Minha nova feature"
   ```
4. Envie para o repositório remoto:
   ```bash
   git push origin minha-feature
   ```
5. Abra um Pull Request.

---

## **Licença**

Este projeto está licenciado sob a [MIT License](LICENSE).