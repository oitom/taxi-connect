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
    $this->authenticate();

    $method = $_SERVER['REQUEST_METHOD'];
    $controller = new \Controller\ApiController();

    switch ($method) {
      case 'GET':
        $controller->getList();
        break;
      case 'POST':
        $controller->create();
        break;
      case 'DELETE':
        $controller->delete();
        break;
      default:
        $controller->notAllow();
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
