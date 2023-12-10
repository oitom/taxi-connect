<?php

use PHPUnit\Framework\TestCase;
use Router\Router;
use Controller\ApiController;
use Router\HeaderProviderInterface;
use Router\DefaultHeaderProvider;

class FakeHeaderProvider implements HeaderProviderInterface
{
  private $headers;

  public function __construct(array $headers = [])
  {
    $this->headers = $headers;
  }

  public function getHeaders()
  {
    return $this->headers;
  }
}

class RouterTest extends TestCase
{
  # get / list - success
  public function testHandleRequestGET()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $expectedResult = ['result' => 'list_result'];
    $apiControllerMock->expects($this->once())->method('list')->willReturn($expectedResult);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'GET';
    $headerProvider = new FakeHeaderProvider([
      'CLIENT_ID' => 'taxiconnect',
      'CLIENT_SECRET' => '550e8400-e29b-41d4-a716-446655440000'
    ]);
    $result = $router->handleRequest($headerProvider);
    $this->assertEquals($expectedResult, $result);
  }

  # post / create - success
  public function testHandleRequestPOST()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $expectedResult = ['result' => 'create_result'];
    $apiControllerMock->expects($this->once())->method('create')->willReturn($expectedResult);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'POST';
    $headerProvider = new FakeHeaderProvider([
      'CLIENT_ID' => 'taxiconnect',
      'CLIENT_SECRET' => '550e8400-e29b-41d4-a716-446655440000'
    ]);
    $result = $router->handleRequest($headerProvider);
    $this->assertEquals($expectedResult, $result);
  }

  # delete / cancel - success
  public function testHandleRequestDELETE()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $expectedResult = ['result' => 'cancel_result'];
    $apiControllerMock->expects($this->once())->method('cancel')->willReturn($expectedResult);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'DELETE';
    $headerProvider = new FakeHeaderProvider([
      'CLIENT_ID' => 'taxiconnect',
      'CLIENT_SECRET' => '550e8400-e29b-41d4-a716-446655440000'
    ]);
    $result = $router->handleRequest($headerProvider);
    $this->assertEquals($expectedResult, $result);
  }

  # patch / activate - success
  public function testHandleRequestPATCH()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $expectedResult = ['result' => 'activate_result'];
    $apiControllerMock->expects($this->once())->method('activate')->willReturn($expectedResult);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'PATCH';
    $headerProvider = new FakeHeaderProvider([
      'CLIENT_ID' => 'taxiconnect',
      'CLIENT_SECRET' => '550e8400-e29b-41d4-a716-446655440000'
    ]);
    $result = $router->handleRequest($headerProvider);
    $this->assertEquals($expectedResult, $result);
  }

  # put / - error metodo invaldo
  public function testHandleRequestPUT()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $apiControllerMock->expects($this->any())->method('list')->willReturn(['result' => 'list_result']);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $headerProvider = new FakeHeaderProvider([
      'CLIENT_ID' => 'taxiconnect',
      'CLIENT_SECRET' => '550e8400-e29b-41d4-a716-446655440000'
    ]);
    $result = $router->handleRequest($headerProvider);
    
    $this->assertEquals(403, http_response_code());
    $this->assertEquals('Método inválido!', $result['message']);
  }

  # get / list - error autenticação
  public function testHandleRequestAuthenticationFail()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $apiControllerMock->expects($this->any())->method('list')->willReturn(['result' => 'list_result']);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'GET';
    $headerProvider = new FakeHeaderProvider();
    $result = $router->handleRequest($headerProvider);
    
    $this->assertEquals(401, http_response_code());
    $this->assertEquals('Autenticação falhou. Credenciais inválidas', json_decode($result, true)['message']);
  }

  
  # get / list - error autenticação com interface api
  public function testHandleRequestAuthenticationFailHeader()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $apiControllerMock->expects($this->any())->method('list')->willReturn(['result' => 'list_result']);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'GET';
    $headerProvider = new DefaultHeaderProvider();
    $result = $router->handleRequest($headerProvider);
    
    $this->assertEquals(401, http_response_code());
    $this->assertEquals('Autenticação falhou. Credenciais inválidas', json_decode($result, true)['message']);
  }

  # get / list - error autenticação com header null
  public function testHandleRequestAuthenticationFailHeaderNull()
  {
    $apiControllerMock = $this->createMock(ApiController::class);
    $apiControllerMock->expects($this->any())->method('list')->willReturn(['result' => 'list_result']);

    $router = new Router($apiControllerMock);

    $_SERVER['REQUEST_METHOD'] = 'GET';
    $result = $router->handleRequest();
    
    $this->assertEquals(401, http_response_code());
    $this->assertEquals('Autenticação falhou. Credenciais inválidas', json_decode($result, true)['message']);
  }

}
