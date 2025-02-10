# testNewCorban
 Teste técnico back-end para New Corban

## Requisitos

- Docker
- Git

## Tecnologias Utilizadas

- PHP 8.2
- MySQL 8.0
- Nginx
- JWT para autenticação

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/gustavosimonato/testNewCorban.git
cd testNewCorban
```

2. Inicie os containers Docker:
```bash
docker-compose up -d --build
```

3. Instale as dependências via Composer:
```bash
docker-compose exec php composer install
```

4. Execute o script SQL para criar as tabelas:
```bash
docker-compose exec -T mysql mysql -uapp_user -papp_password clients_db < schema.sql
```

5. Verifique se a instalação está funcionando:
```bash
curl http://localhost:8000
```