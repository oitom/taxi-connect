<?php

namespace Controller;

use Model\Passageiro;
use Model\Motorista;
use Model\Corrida;
use Presenter\CorridaPresenter;
use Service\CorridaService;

class ApiController
{
  public function __construct() {
    // header('Content-Type: application/json');
  }

  public function getList($parms=null, $body=null)
  {
    echo json_encode(['message' => 'GetList']);
  }

  public function create($parms=null, $body=null)
  {
    $corrida = $this->criarCorrida($body);
    $res = $this->autorizarCorrida($corrida);
    
    // $presenter = new CorridaPresenter($corrida);
    // $camposDesejados = ['origem', 'destino', 'tipoCorrida', 'status'];
    // $res = $presenter->toArray($camposDesejados);

    $response = array(
      'message' => 'create',
      'response'=> $res
    );

    echo json_encode($response,  JSON_PRETTY_PRINT);
  }

  public function delete($parms=null, $body=null)
  {
    echo json_encode(['message' => 'cancel']);
  }

  public function notAllow()
  {
    echo json_encode(['message' => 'notAllow']);
  }

  public function autorizarCorrida(Corrida $corrida)
  {
    $corridaService = new CorridaService();

    if ($corridaService->validarCorrida($corrida)) {
      return true;
    } else {
      return false;
    }
  }
  
  private function criarPassageiro($dados): Passageiro
  {
    return new Passageiro($dados["cpf"], $dados["nome"], $dados["telefone"]);
  }

  private function criarMotorista($dados): Motorista
  {
    return new Motorista($dados["cnpj"], $dados["nome"], $dados["placa_veiculo"], $dados["modelo_veiculo"]);
  }

  private function criarCorrida($dados): Corrida
  {
    $passageiro = $this->criarPassageiro($dados["corrida"]["passageiro"]);
    $motorista = $this->criarMotorista($dados["corrida"]["motorista"]);

    return new Corrida(
      $dados["corrida"]["origem"],
      $dados["corrida"]["destino"],
      $passageiro,
      $motorista,
      $dados["corrida"]["tipo_da_corrida"],
      $dados["corrida"]["preco_estimado"],
      $dados["corrida"]["tipo_pagamento"],
      $dados["corrida"]["autenticacao"],
      $dados["corrida"]["horarioDePico"],
    );
  }
}