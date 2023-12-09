# taxi-connect
Taxi Connect é uma API robusta e confiável projetada para simplificar e fortalecer o processo de autorização em serviços de corridas de táxi.

[![CodeFactor](https://www.codefactor.io/repository/github/oitom/taxi-connect/badge)](https://www.codefactor.io/repository/github/oitom/taxi-connect)
[![Maintainability](https://api.codeclimate.com/v1/badges/84647828e6ee2628a303/maintainability)](https://codeclimate.com/github/oitom/taxi-connect/maintainability)
[![codecov](https://codecov.io/github/oitom/taxi-connect/graph/badge.svg?token=gVgSyIhi4P)](https://codecov.io/github/oitom/taxi-connect)

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

## Testando a API
Você pode usar ferramentas como Postman ou curl para testar as chamadas da API. Certifique-se de incluir os cabeçalhos CLIENT_ID e CLIENT_SECRET nas suas solicitações.

- client_id: 
```
taxiconnect
```

- client_secret: 
```
550e8400-e29b-41d4-a716-446655440000
```


### Listar corridas:
```
curl --location 'http://localhost:8080/' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

### Listar corridas por UUID:
```
curl --location 'http://localhost:8080/?uuid=81397774-c68f-4931-bf68-0817ecaaef72' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

### Criar corrida:
```
curl --location 'http://localhost:8080/' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000' \
--header 'Content-Type: application/json' \
--data '{
  "corrida": {
    "origem": {
      "latitude": 32.7749,
      "longitude": -122.4194,
      "endereco": "123 Main Street, Cidade A"
    },
    "destino": {
      "latitude": 37.3352,
      "longitude": -121.8811,
      "endereco": "456 Oak Street, Cidade B"
    },
    "passageiro": {
      "cpf": "451.643.942-24",
      "nome": "João Silva",
      "telefone": "+551234567890"
    },
    "motorista": {
      "cnpj": "12.124.543.0001/20",
      "nome": "Maria Joana Oliveira",
      "placaVeiculo": "ABC123",
      "modeloVeiculo": "Toyota Corolla"
    },
    "tipoCorrida": "taximetro",
    "precoEstimado": 20,
    "horarioPico": false,
    "tipoPagamento": "cartao_credito",
    "autenticacao": {
      "token_acesso": "token123",
      "chave_seguranca": "segredo456"
    }
  }
}'
```
Para o tipo preço fixo, utilizar o valor  "preco_fixo" no campo "tipoCorrida"
```
"corrida": {
  "tipoCorrida": "preco_fixo"
}
```

### Ativar corrida:
```
curl --location --request PATCH 'http://localhost:8080/?uuid=385b0c25-8ea9-4cd1-b615-627c9f974a68' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```
### Cancelar corrida:
```
curl --location --request DELETE 'http://localhost:8080/?uuid=385b0c25-8ea9-4cd1-b615-627c9f974a68' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

## Encerrando o Ambiente
Para encerrar o ambiente Docker, execute:

```
docker-compose down
```
Isso desligará o contêiner Docker e liberará os recursos.
