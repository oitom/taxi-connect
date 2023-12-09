<?php

require_once 'vendor/autoload.php';

// Este trecho de código cria um objeto Router para lidar com as solicitações da API.
// Ele recebe as solicitações e encaminha para o método apropriado para processamento.
$router = new \Router\Router();
$router->handleRequest();