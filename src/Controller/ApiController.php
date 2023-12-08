<?php

namespace Controller;

use Model\Passageiro;
use Model\Motorista;
use Model\Corrida;
use Presenter\CorridaPresenter;
use Service\CorridaService;

class ApiController
{
  public function create($parms=null, $body=null)
  {   
    $resp = $this->criarCorrida($body);

    if($resp["error"]) {
      http_response_code(401);
      return json_encode(array('message' => $resp["msg"]),  JSON_PRETTY_PRINT);
    }
    $corrida = $resp["corrida"];

    if(!$this->calculaPreco($corrida)) {
      http_response_code(401);
      return json_encode(array('message' => 'Não foi possível calcular o valor da corrida. Campo tipo_corrida invalido!'),  JSON_PRETTY_PRINT);
    }

    // $res = $this->autorizarCorrida($corrida);
    
    $presenter = new CorridaPresenter($corrida);
    $camposDesejados = ['origem', 'destino', 'tipoCorrida', 'preco', 'status'];
    $res = $presenter->toArray($camposDesejados);
    
    $response = array(
      'message' => 'Corrida criada com sucesso!',
      'response'=> array(
        "corrida"=> $res,
      )
    );

    http_response_code(201);
    echo json_encode($response,  JSON_PRETTY_PRINT);
  }

  public function delete($parms=null, $body=null)
  {
    echo json_encode(['message' => 'cancel']);
  }

  private function calculaPreco(corrida $corrida) 
  {
    $corridaService = new CorridaService();
    return $corridaService->calculaPreco($corrida);
  }

  private function autorizarCorrida(Corrida $corrida)
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
    try {
      $cpf = $dados["cpf"] ?? null;
      $nome = $dados["nome"] ?? null;
      $telefone = $dados["telefone"] ?? null;

      return new Passageiro($cpf, $nome, $telefone);
    } catch (\InvalidArgumentException $e) {
      throw $e;
    } catch (\Exception $e) {
      throw new \Exception("Erro ao criar Passageiro", 0, $e);
    }
  }
  
  private function criarMotorista($dados): Motorista
  {
    try {
      $cnpj = $dados["cnpj"] ?? null;
      $nome = $dados["nome"] ?? null;
      $placaVeiculo = $dados["placa_veiculo"] ?? null;
      $modeloVeiculo = $dados["modelo_veiculo"] ?? null;

      return new Motorista($cnpj, $nome, $placaVeiculo, $modeloVeiculo);
    } catch (\InvalidArgumentException $e) {
      throw $e;
    } catch (\Exception $e) {
      throw new \Exception("Erro ao criar Motorista", 0, $e);
    }
  }

  private function criarCorrida($dados): array
  {
    try {
      $passageiro = $this->criarPassageiro($dados["corrida"]["passageiro"]);
      $motorista = $this->criarMotorista($dados["corrida"]["motorista"]);

      $origem = $dados["corrida"]["origem"] ?? null;
      $destino = $dados["corrida"]["destino"] ?? null;
      $tipoCorrida = $dados["corrida"]["tipo_corrida"] ?? null;
      $precoEstimado = $dados["corrida"]["preco_estimado"] ?? null;
      $tipoPagamento = $dados["corrida"]["tipo_pagamento"] ?? null;
      $horarioPico = $dados["corrida"]["horarioPico"] ?? null;
      $autenticacao = $dados["corrida"]["autenticacao"] ?? null;

      $corrida = new Corrida(
        $origem,
        $destino,
        $passageiro,
        $motorista,
        $tipoCorrida,
        $precoEstimado,
        0,
        $tipoPagamento,
        $horarioPico,
        $autenticacao,
        'created'
      );

      return ["corrida" => $corrida, "error" => false];
    } catch (\InvalidArgumentException $e) {
      return ["corrida" => null, "error" => true, "msg" => $e->getMessage()];
    } catch (\Exception $e) {
      return ["corrida" => null, "error" => true, "msg" => "Erro desconhecido"];
    }
  }

}