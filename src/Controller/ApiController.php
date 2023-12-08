<?php

namespace Controller;

use Model\Passageiro;
use Model\Motorista;
use Model\Corrida;
use Presenter\CorridaPresenter;
use Service\CorridaService;

class ApiController
{
  public function create($params=null, $body=null)
  {   
    $resp = $this->criarCorrida($body);
    $corrida = $resp["corrida"];

    $campos = ['uuid', 'origem', 'destino', 'tipoCorrida', 'preco', 'status', 'data'];
    $presenter = new CorridaPresenter($corrida, $campos);

    if($resp["error"]) {
      return $presenter->error($resp["msg"]);
    }
    
    if(!$this->calculaPreco($corrida)) {
      return $presenter->error('Não foi possível calcular o valor da corrida. Campo tipo_corrida invalido!');
    }

    if(!$this->autorizarCorrida($corrida)) {
      return $presenter->error('Não foi possível autorizar a corrida.');
    }

    return $presenter->success();
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

      $_passageiro = $dados["corrida"]["passageiro"] ?? null;
      $_motorista = $dados["corrida"]["motorista"] ?? null;

      $passageiro = $this->criarPassageiro($_passageiro);
      $motorista = $this->criarMotorista($_motorista);

      $origem = $dados["corrida"]["origem"] ?? null;
      $destino = $dados["corrida"]["destino"] ?? null;
      $tipoCorrida = $dados["corrida"]["tipo_corrida"] ?? null;
      $precoEstimado = $dados["corrida"]["preco_estimado"] ?? null;
      $tipoPagamento = $dados["corrida"]["tipo_pagamento"] ?? null;
      $autenticacao = $dados["corrida"]["autenticacao"] ?? null;
      $horarioPico = isset($dados["corrida"]["horarioPico"]) && $dados["corrida"]["horarioPico"] === true;

      $corrida = new Corrida(
        $origem,
        $destino,
        $passageiro,
        $motorista,
        $tipoCorrida,
        $precoEstimado,
        $tipoPagamento,
        $horarioPico,
        $autenticacao,
      );

      return ["corrida" => $corrida, "error" => false];
    } catch (\InvalidArgumentException $e) {
      return ["corrida" => null, "error" => true, "msg" => $e->getMessage()];
    } catch (\Exception $e) {
      return ["corrida" => null, "error" => true, "msg" => "Erro desconhecido"];
    }
  }

}