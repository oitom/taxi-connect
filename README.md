# taxi-connect
Taxi Connect é uma API robusta e confiável projetada para simplificar e fortalecer o processo de autorização em serviços de corridas de táxi.

[![CodeFactor](https://www.codefactor.io/repository/github/oitom/taxi-connect/badge)](https://www.codefactor.io/repository/github/oitom/taxi-connect)
[![Maintainability](https://api.codeclimate.com/v1/badges/84647828e6ee2628a303/maintainability)](https://codeclimate.com/github/oitom/taxi-connect/maintainability)
[![codecov](https://codecov.io/github/oitom/taxi-connect/graph/badge.svg?token=gVgSyIhi4P)](https://codecov.io/github/oitom/taxi-connect)

## Solução
Este projeto foi desenvolvido seguindo os princípios de uma API REST para gerenciar operações relacionadas à criação, cancelamento, ativação/início e listagem de corridas. Uma abordagem de PHP puro foi adotada, evitando a utilização de frameworks externos. A estrutura da API é detalhada no próximo tópico.

Além disso, a integridade do código foi assegurada por meio de testes unitários implementados com PHPUnit. Para facilitar a execução do projeto, uma configuração Docker foi estabelecida.

### Estrutura do projeto
```
/project
│
├── index.php
├── src
│   └── Controller
│       └── ApiController.php
│   └── Model
│       └── Corrida.php
│       └── Motorista.php
│       └── Passageiro.php
│   └── Router
│       └── Router.php
│   └── Service
│       └── ActivateCorridaService.php
│       └── CancelCorridaService.php
│       └── CorridaCorridaService.php
│       └── CreateCorridaService.php
│       └── ListCorridaService.php
```

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

## Execução de Testes com PHPUnit

1. Gerando um Relatório de Cobertura de Testes com PHPUnit:
```
docker exec -it taxi-connect-web-1 vendor/bin/phpunit --coverage-html=coverage/
```
2. Se tudo der certo, o relatório estará disponível em: http://localhost:8080/coverage/index.html

## Executando o Projeto
Para testar as chamadas da API, você pode seguir estas instruções utilizando ferramentas como Postman (no) ou curl. Certifique-se de incluir os cabeçalhos `CLIENT_ID` e `CLIENT_SECRET` em todas as suas solicitações para autenticação adequada. Siga os passos abaixo:

#### Importando collection no postman
Se você escolher utilizar o Postman, pode importar facilmente os arquivos de collection e environment presentes na raiz do projeto. Para isso, siga estes passos simples:

1. Abra o Postman;
2. Clique em "Import" no canto superior esquerdo;
3. Selecione a opção "File" e escolha os arquivos de `collection` (*.json) e `environment` (*.json) que está na raiz do projeto;
4. Após a importação, os endpoints e variáveis necessárias estarão disponíveis para uso imediato.

#### Credenciais

- `CLIENT_ID`: 
```
taxiconnect
```

- `CLIENT_SECRET`: 
```
550e8400-e29b-41d4-a716-446655440000
```

### Cenário de Sucesso

#### Listar corridas:
```
curl --location 'http://localhost:8080/' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

#### Listar corridas por UUID:
```
curl --location 'http://localhost:8080/?uuid=81397774-c68f-4931-bf68-0817ecaaef72' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

#### Criar corrida:
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

#### Ativar corrida:
```
curl --location --request PATCH 'http://localhost:8080/?uuid=385b0c25-8ea9-4cd1-b615-627c9f974a68' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```
#### Cancelar corrida:
```
curl --location --request DELETE 'http://localhost:8080/?uuid=385b0c25-8ea9-4cd1-b615-627c9f974a68' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```
### Cenário de Erros

#### Listar corridas não encontradas
```
curl --location 'http://localhost:8080/?uuid=123' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

#### Criar corrida - erro limite entre preço x preço estimado
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
    "precoEstimado": 50,
    "horarioPico": false,
    "tipoPagamento": "cartao_credito",
    "autenticacao": {
      "token_acesso": "token123",
      "chave_seguranca": "segredo456"
    }
  }
}'
```

#### Criar corrida - Preço da corrida ultrapassou o limite
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
    "precoEstimado": 30,
    "horarioPico": true,
    "tipoPagamento": "cartao_credito",
    "autenticacao": {
      "token_acesso": "token123",
      "chave_seguranca": "segredo456"
    }
  }
}'
```
#### Criar corrida - credenciais invalidas
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
    "precoEstimado": 30,
    "horarioPico": false,
    "tipoPagamento": "cartao_credito",
    "autenticacao": {
      "token_acesso": "token123567",
      "chave_seguranca": "segredo456678"
    }
  }
}'
```

#### Cancelar corrida - Erro corrida em andamento
```
curl --location --request DELETE 'http://localhost:8080/?uuid=e894be4f-4dad-4170-abbe-38e2a403c3f4' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

#### Ativar corrida - Corrida já cancelada
```
curl --location --request DELETE 'http://localhost:8080/?uuid=e894be4f-4dad-4170-abbe-38e2a403c3f4' \
--header 'CLIENT_ID: taxiconnect' \
--header 'CLIENT_SECRET: 550e8400-e29b-41d4-a716-446655440000'
```

## Encerrando o Ambiente
Para encerrar o ambiente Docker, execute:

```
docker-compose down
```
Isso desligará o contêiner Docker e liberará os recursos.
