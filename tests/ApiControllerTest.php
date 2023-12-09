<?php

namespace Tests\Controller;

use PHPUnit\Framework\TestCase;
use Controller\ApiController;

class ApiControllerTest extends TestCase
{
  public function testListdSuccess()
  {
    $controller = new ApiController();
    $response = $controller->list();

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    $qtd = count($responseData);

    $this->assertGreaterThan(0, $qtd, 'A quantidade de elementos no array deve ser maior que 0.');
  }

  public function testListByUuidSuccess()
  {
    $params = ['uuid' => '81397774-c68f-4931-bf68-0817ecaaef72'];
    $controller = new ApiController();
    $response = $controller->list($params);

    $this->assertIsString($response);
    $responseData = json_decode($response, true);
    
    $this->assertArrayHasKey('uuid', $responseData);
  }

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

  public function testCreateSuccess()
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

  
}
