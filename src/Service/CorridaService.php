<?php

namespace Service;

use Model\Passageiro;
use Model\Motorista;
use Model\Corrida;

class CorridaService 
{
  public function __construct() {
  }
  
  protected function criarCorrida($dados, $tipo = "criar", $status = ""): array
  {    
    try {
      $_passageiro = $dados["corrida"]["passageiro"] ?? null;
      $_motorista = $dados["corrida"]["motorista"] ?? null;

      $passageiro = $this->criarPassageiro($_passageiro);
      $motorista = $this->criarMotorista($_motorista);

      $origem = $dados["corrida"]["origem"] ?? null;
      $destino = $dados["corrida"]["destino"] ?? null;
      $tipoCorrida = $dados["corrida"]["tipoCorrida"] ?? null;
      $precoEstimado = $dados["corrida"]["precoEstimado"] ?? null;
      $tipoPagamento = $dados["corrida"]["tipoPagamento"] ?? null;
      $autenticacao = $dados["corrida"]["autenticacao"] ?? null;
      $horarioPico = isset($dados["corrida"]["horarioPico"]) && $dados["corrida"]["horarioPico"] === true;
      $data_criacao = $dados["corrida"]["data"] ?? date("Y-m-d H:i:s");
      
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
      
      $corrida->setStatus($status);
      $corrida->setData($data_criacao);

      if($tipo == "carregar") {
        $corrida->setUuid($dados["corrida"]["uuid"]);
      }

      return ["corrida" => $corrida, "error" => false];
    } catch (\InvalidArgumentException $e) {
      return ["corrida" => null, "error" => true, "msg" => $e->getMessage()];
    } catch (\Exception $e) {
      return ["corrida" => null, "error" => true, "msg" => "Erro desconhecido"];
    }
  }

  protected function criarPassageiro($dados): Passageiro
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
  
  protected function criarMotorista($dados): Motorista
  {
    try {
      $cnpj = $dados["cnpj"] ?? null;
      $nome = $dados["nome"] ?? null;
      $placaVeiculo = $dados["placaVeiculo"] ?? null;
      $modeloVeiculo = $dados["modeloVeiculo"] ?? null;

      return new Motorista($cnpj, $nome, $placaVeiculo, $modeloVeiculo);
    } catch (\InvalidArgumentException $e) {
      throw $e;
    } catch (\Exception $e) {
      throw new \Exception("Erro ao criar Motorista", 0, $e);
    }
  }

  protected function getCorridas()
  {
    // $jsonFilePath = '/var/www/html/data/corridas.json';
    $jsonFilePath = __DIR__ . '/../../data/corridas.json';
    return file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];
  }

  protected function setCorridas($corridas)
  {
    // $jsonFilePath = '/var/www/html/data/corridas.json';
    $jsonFilePath = __DIR__ . '/../../data/corridas.json';
    file_put_contents($jsonFilePath, json_encode($corridas, JSON_PRETTY_PRINT));
  }

  protected function getCorridaPorUuid($uuid)
  {
    $corridas = $this->getCorridas();

    if($corridas) {
      foreach ($corridas as $corrida) {
        if ($corrida['uuid'] === $uuid) {
          $corridaModel = $this->criarCorrida(array("corrida" => $corrida), "carregar", $corrida["status"]);
          return $corridaModel;
        }
      }
    }

    return null;
  }

  protected function atualizarCorrida($uuid, $dadosAtualizados)
  {
    $corridas = $this->getCorridas();

    foreach ($corridas as &$corrida) {
      if ($corrida['uuid'] === $uuid) {
        $corrida = array_merge($corrida, $dadosAtualizados);

        $this->setCorridas($corridas);

        return $this->criarCorrida(["corrida" => $corrida], "carregar", $corrida["status"]);
      }
    }
    return null;
  }
}
