<?php
namespace Router;

use Router\HeaderProviderInterface;
use Router\DefaultHeaderProvider;

class Router
{
  private $clientCredentials = [
    'client_id'     => 'taxiconnect',
    'client_secret' => '550e8400-e29b-41d4-a716-446655440000'
  ];

  private $apiController;

  public function __construct(\Controller\ApiController $apiController)
  {
    $this->apiController = $apiController;
  }

  public function handleRequest(HeaderProviderInterface $headerProvider = null)
  {
    if(!$this->authenticate($headerProvider)) {
      http_response_code(401);
      $result = ["message" => "Autenticação falhou. Credenciais inválidas"];
      return json_encode($result);
    }

    $queryParams = $_GET;
    $method = $_SERVER['REQUEST_METHOD'];
    $body = json_decode(file_get_contents("php://input"), true);

    switch ($method) {
      case 'GET':
        $result = $this->apiController->list($queryParams);
        break;
      case 'POST':
        $result = $this->apiController->create($queryParams, $body);
        break;
      case 'DELETE':
        $result = $this->apiController->cancel($queryParams);
        break;
      case 'PATCH':
        $result = $this->apiController->activate($queryParams);
        break;
      default:
        http_response_code(403);
        $result = ["message" => "Método inválido!"];
        break;
    }
    return $result;
  }

  private function authenticate($headerProvider)
  {
    if ($headerProvider === null) {
      $headerProvider = new DefaultHeaderProvider();
    }
    $headers = $headerProvider->getHeaders();    

    $clientId = isset($headers['CLIENT_ID']) ? $headers['CLIENT_ID'] : null;
    $clientSecret = isset($headers['CLIENT_SECRET']) ? $headers['CLIENT_SECRET'] : null;

    if ($clientId !== $this->clientCredentials['client_id'] || $clientSecret !== $this->clientCredentials['client_secret']) {
     return false;
    }

    return true;
  }
}
