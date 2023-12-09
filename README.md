# taxi-connect
Taxi Connect é uma API robusta e confiável projetada para simplificar e fortalecer o processo de autorização em serviços de corridas de táxi.

[![CodeFactor](https://www.codefactor.io/repository/github/oitom/taxi-connect/badge)](https://www.codefactor.io/repository/github/oitom/taxi-connect)
[![Maintainability](https://api.codeclimate.com/v1/badges/84647828e6ee2628a303/maintainability)](https://codeclimate.com/github/oitom/taxi-connect/maintainability)

## Configuração do Ambiente
Pré-requisitos

- Docker
- Composer

## Instalação
1. Clone este repositório:

```
git clone https://github.com/oitom/taxi-connect.git
```

2. Acesse o diretório:
```
cd taxi-connect
```

3. Inicie o contêiner Docker:
```
docker-compose up -d
```

4. Instale as dependências do Composer:
```
docker-compose run web composer install
```

5. Acesse a aplicação em http://localhost:8080.

## Rodar os testes com PHPUnit

1. Gerando um reltório de cobertura de testes phpunit
```
docker exec -it taxi-connect-web-1 vendor/bin/phpunit --coverage-html=coverage/
```

## Utilização
Testando a API
Você pode usar ferramentas como Postman ou curl para testar as chamadas da API. Certifique-se de incluir os cabeçalhos CLIENT_ID e CLIENT_SECRET nas suas solicitações.

### Exemplo de solicitação cURL GET:

```
curl -X GET -H "CLIENT_ID: seu_cliente_id" -H "CLIENT_SECRET: seu_cliente_secret" http://localhost:8080/
```
### Exemplo de solicitação cURL POST:

```
curl -X POST -H "CLIENT_ID: seu_cliente_id" -H "CLIENT_SECRET: seu_cliente_secret" -d "param1=valor1&param2=valor2" http://localhost:8080/
```
### Exemplo de solicitação cURL DELETE:

```
curl -X DELETE -H "CLIENT_ID: seu_cliente_id" -H "CLIENT_SECRET: seu_cliente_secret" http://localhost:8080/
```

#### Lembre-se de substituir seu_cliente_id, seu_cliente_secret e pelos valores reais do seu client.

## Encerrando o Ambiente
Para encerrar o ambiente Docker, execute:

```
docker-compose down
```
Isso desligará o contêiner Docker e liberará os recursos.
