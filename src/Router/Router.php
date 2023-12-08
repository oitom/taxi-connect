<?php

namespace Router;

class Router
{
  public function __construct() {
    header('Content-Type: application/json');
  }

  private $clientCredentials = [
    'client_id'     => 'taxiconnect',
    'client_secret' => '550e8400-e29b-41d4-a716-446655440000'
  ];

  public function handleRequest()
  {
    header('Content-Type: application/json');
  
    $this->authenticate();

    $controller = new \Controller\ApiController();
    
    $queryParams = $_GET;
    $method = $_SERVER['REQUEST_METHOD'];
    $body = json_decode(file_get_contents("php://input"), true);
    switch ($method) {
      case 'GET':
        echo $controller->list($queryParams);
        break;
      case 'POST':
        echo $controller->create($queryParams, $body);
        break;
      case 'DELETE':
        echo $controller->cancel($queryParams);
        break;
      case 'PATCH':
        echo $controller->activate($queryParams);
        break;
      default:
        http_response_code(403);
        echo json_encode(["message" => "Método inválido!"]);
        break;
    }
  }

  private function authenticate()
  {
    $headers = getallheaders();

    $clientId = isset($headers['CLIENT_ID']) ? $headers['CLIENT_ID'] : null;
    $clientSecret = isset($headers['CLIENT_SECRET']) ? $headers['CLIENT_SECRET'] : null;

    if ($clientId !== $this->clientCredentials['client_id'] || $clientSecret !== $this->clientCredentials['client_secret']) {
      http_response_code(401);
      echo json_encode(["message" => "Autenticação falhou. Credenciais inválidas"]);
      exit();
    }
  }
}
