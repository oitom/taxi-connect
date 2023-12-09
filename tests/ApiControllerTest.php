<?php

namespace Tests\Controller;

use PHPUnit\Framework\TestCase;
use Controller\ApiController;

class ApiControllerTest extends TestCase
{
  # get /  - list - success
  public function testListdSuccess()
  {
    $controller = new ApiController();
    $response = $controller->list();

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    $qtd = count($responseData);

    $this->assertGreaterThan(0, $qtd, 'A quantidade de elementos no array deve ser maior que 0.');
  }

  # get /:uuid - list - success
  public function testListByUuidSuccess()
  {
    $params = ['uuid' => '81397774-c68f-4931-bf68-0817ecaaef72'];
    $controller = new ApiController();
    $response = $controller->list($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertArrayHasKey('uuid', $responseData);
  }

  # get /:uuid - list - error
  public function testListByUuidError()
  {
    $params = ['uuid' => '123'];
    $controller = new ApiController();
    $response = $controller->list($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);

    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Corrida não encontrada.', $responseData['message']);
  }

  # post / - create taximetro - success
  public function testCreateTaximetroSuccess()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $this->assertNotNull($body);

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(201, $responseData['code']);
    $this->assertArrayHasKey('data', $responseData);
    $this->assertArrayHasKey('uuid', $responseData['data']);
    $this->assertArrayHasKey('preco', $responseData['data']);
    $this->assertEquals('autorizada', $responseData['data']['status']);
    $this->assertEquals('Corrida autorizada com sucesso!', $responseData['message']);
  }

  # post / - create preco_fixo - success
  public function testCreatePrecoFixoSuccess()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $body["corrida"]["tipoCorrida"] = "preco_fixo";
    $this->assertNotNull($body);

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(201, $responseData['code']);
    $this->assertArrayHasKey('data', $responseData);
    $this->assertArrayHasKey('uuid', $responseData['data']);
    $this->assertArrayHasKey('preco', $responseData['data']);
    $this->assertEquals('autorizada', $responseData['data']['status']);
    $this->assertEquals('Corrida autorizada com sucesso!', $responseData['message']);
  }

  # post / - create - error
  public function testCreateError()
  {
    $jsonFilePath = __DIR__ . '/mock/error_create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $this->assertNotNull($body);

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('O campo tipoCorrida não pode estar vazio.', $responseData['message']);
  }

  # post / - create - error credenciais autenticacao
  public function testCreateAutenticacaoError()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $this->assertNotNull($body);
    $body["corrida"]["autenticacao"]["token_acesso"] = null;

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
  
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não foi possível autorizar a corrida.', $responseData['message']);
    $this->assertEquals('Credenciais de autenticação invalidas', $responseData['reason']);
    $this->assertEquals('Não autenticado', $responseData['status']);
  }
  
  # post / - create - error passageiro
  public function testCreatePassagieroError()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $this->assertNotNull($body);
    $body["corrida"]["passageiro"] = null;

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('O campo passageiro cpf não pode estar vazio.', $responseData['message']);
  }

  # post / - create - error motorista
  public function testCreateMotoristaError()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $this->assertNotNull($body);
    $body["corrida"]["motorista"] = null;

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
  
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('O campo motorista cnpj não pode estar vazio.', $responseData['message']);
  }

  # post / - create - error tipo_corrida
  public function testCreateTypeCorridaError()
  {
    $jsonFilePath = __DIR__ . '/mock/error_create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $body["corrida"]["tipoCorrida"] = "123";
    $this->assertNotNull($body);

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não foi possível calcular o valor da corrida. Campo tipo_corrida invalido!', $responseData['message']);
  }
  
  # post / - create - error preco estimado
  public function testCreateEstimatedPriceError()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $body["corrida"]["precoEstimado"] = 50;
    $this->assertNotNull($body);

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não foi possível autorizar a corrida.', $responseData['message']);
    $this->assertEquals('A diferença entre o preço real e o preço estimado excedeu o limite permitido.', $responseData['reason']);
    $this->assertEquals('rejeitada', $responseData['status']);
  }

  # post / - create - error horario de pico
  public function testCreateRushHourError()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $this->assertFileExists($jsonFilePath);

    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);
    $body["corrida"]["precoEstimado"] = 30;
    $body["corrida"]["horarioPico"] = true;
    $this->assertNotNull($body);

    $controller = new ApiController();
    $response = $controller->create(null, $body);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
   
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não foi possível autorizar a corrida.', $responseData['message']);
    $this->assertEquals('O preço total da corrida excedeu o limite permitido.', $responseData['reason']);
    $this->assertEquals('rejeitada', $responseData['status']);
  }

  # delete /:uuid - cancel - success
  public function testCancelSuccess()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);

    $controller = new ApiController();
    $response = $controller->create(null, $body);
    $responseData = json_decode($response, true);

    $params = ['uuid' => $responseData['data']['uuid']];
    $controller = new ApiController();
    $response = $controller->cancel($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(200, $responseData['code']);
    $this->assertEquals('Corrida cancelada com sucesso!', $responseData['message']);
    $this->assertEquals('cancelada', $responseData['data']['status']);
  }

  # delete /:uuid - cancel - error
  public function testCancelError()
  {
    $params = ['uuid' => '00f37b57-4122-4530-ba19-83fac18debfe'];
    $controller = new ApiController();
    $response = $controller->cancel($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não é possível cancelar a corrida neste momento.', $responseData['message']);
    $this->assertEquals('Corrida já foi cancelada', $responseData['reason']);
    $this->assertEquals('cancelada', $responseData['status']);
  }

  # delete /:uuid - cancel - error not found
  public function testCancelNotFoundError()
  {
    $params = ['uuid' => '123'];
    $controller = new ApiController();
    $response = $controller->cancel($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Corrida não encontrada.', $responseData['message']);
  }

  # delete /:uuid - cancel - error has canceled
  public function testCancelHasCanceledError()
  {
    $params = ['uuid' => '00f37b57-4122-4530-ba19-83fac18debfe'];
    $controller = new ApiController();
    $response = $controller->cancel($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não é possível cancelar a corrida neste momento.', $responseData['message']);
    $this->assertEquals('Corrida já foi cancelada', $responseData['reason']);
    $this->assertEquals('cancelada', $responseData['status']);
  }

  # delete /:uuid - cancel - error has ativa
  public function testCancelHasActivatedError()
  {
    $params = ['uuid' => 'e894be4f-4dad-4170-abbe-38e2a403c3f4'];
    $controller = new ApiController();
    $response = $controller->cancel($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não é possível cancelar a corrida neste momento.', $responseData['message']);
    $this->assertEquals('Corrida em andamento!', $responseData['reason']);
    $this->assertEquals('ativa', $responseData['status']);
  }
  
  # patch /:uuid - ativate - success
  public function testActivateSuccess()
  {
    $jsonFilePath = __DIR__ . '/mock/create_payload.json';
    $jsonPayload = file_get_contents($jsonFilePath);
    $body = json_decode($jsonPayload, true);

    $controller = new ApiController();
    $response = $controller->create(null, $body);
    $responseData = json_decode($response, true);

    $params = ['uuid' => $responseData['data']['uuid']];
    $controller = new ApiController();
    $response = $controller->activate($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(200, $responseData['code']);
    $this->assertEquals('Corrida ativa com sucesso!', $responseData['message']);
    $this->assertEquals('ativa', $responseData['data']['status']);
  }

  # patch /:uuid - ativate - error
  public function testActivateError()
  {
    $params = ['uuid' => '00f37b57-4122-4530-ba19-83fac18debfe'];
    $controller = new ApiController();
    $response = $controller->activate($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não foi possível ativar a corrida.', $responseData['message']);
    $this->assertEquals('Corrida cancelada', $responseData['reason']);
    $this->assertEquals('cancelada', $responseData['status']);
  }

  # patch /:uuid - ativate - error not found
  public function testActivateNotFoundError()
  {
    $params = ['uuid' => '123'];
    $controller = new ApiController();
    $response = $controller->activate($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Corrida não encontrada.', $responseData['message']);
  }

  # patch /:uuid - ativate - has activate
  public function testActivateHasAcvatedError()
  {
    $params = ['uuid' => 'e894be4f-4dad-4170-abbe-38e2a403c3f4'];
    $controller = new ApiController();
    $response = $controller->activate($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertEquals(401, $responseData['code']);
    $this->assertEquals('Não foi possível ativar a corrida.', $responseData['message']);
    $this->assertEquals('Corrida já foi ativada', $responseData['reason']);
    $this->assertEquals('ativa', $responseData['status']);
  }

}