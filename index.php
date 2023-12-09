<?php
header('Content-Type: application/json');

require_once __DIR__ . '/vendor/autoload.php';

$apiController = new \Controller\ApiController();
$router = new \Router\Router($apiController);

$response = $router->handleRequest();
echo $response;