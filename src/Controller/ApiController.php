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

    
    $this->inserirCorrida($corrida->toArray());
    return $presenter->success();
  }

  public function delete($params=null, $body=null)
  {
  
    $resp = $this->getCorridaPorUuid($params['uuid']);
    $corrida = $resp["corrida"] ?? null;
    $campos = ['uuid', 'origem', 'destino', 'tipoCorrida', 'preco', 'status', 'statusDesc', 'data'];
    
    $presenter = new CorridaPresenter($corrida, $campos);
    
    if (!$corrida) {
      return $presenter->error('Corrida não encontrada.');
    }
    else {
      $presenter = new CorridaPresenter($resp['corrida'], $campos);

      if (!$this->podeCancelarCorrida($corrida)) {
        return $presenter->error('Não é possível cancelar a corrida neste momento.');
      }
 
     $this->cancelarCorrida($corrida);
     return $presenter->success();
    }   
  }

  private function calculaPreco(Corrida $corrida) 
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
  
  private function cancelarCorrida(Corrida $corrida)
  {
    $corridaService = new CorridaService();
    return $corridaService->cancelarCorrida($corrida);
  }

  private function podeCancelarCorrida(Corrida $corrida) 
  {
    $corridaService = new CorridaService();
    return $corridaService->verificarCancelamento($corrida);
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
      $placaVeiculo = $dados["placaVeiculo"] ?? null;
      $modeloVeiculo = $dados["modeloVeiculo"] ?? null;

      return new Motorista($cnpj, $nome, $placaVeiculo, $modeloVeiculo);
    } catch (\InvalidArgumentException $e) {
      throw $e;
    } catch (\Exception $e) {
      throw new \Exception("Erro ao criar Motorista", 0, $e);
    }
  }

  private function criarCorrida($dados, $status=""): array
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

      return ["corrida" => $corrida, "error" => false];
    } catch (\InvalidArgumentException $e) {
      return ["corrida" => null, "error" => true, "msg" => $e->getMessage()];
    } catch (\Exception $e) {
      return ["corrida" => null, "error" => true, "msg" => "Erro desconhecido"];
    }
  }

  private function getCorridaPorUuid($uuid)
  {
    $corridas = $this->getCorridas();

    if($corridas) {
      foreach ($corridas as $corrida) {
        if ($corrida['uuid'] === $uuid) {
          $corridaModel = $this->criarCorrida(array("corrida" => $corrida), $corrida["status"]);
          return $corridaModel;
        }
      }
    }

    return null;
  }

  private function inserirCorrida($corrida)
  {
    $corridas = $this->getCorridas();
    $corridas[] = $corrida;
    $this->setCorridas($corridas);
    return true;
  }

  private function getCorridas()
  {
    $jsonFilePath = '/var/www/html/data/corridas.json';
    return file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];
  }

  private function setCorridas($corridas)
  {
    $jsonFilePath = '/var/www/html/data/corridas.json';
    file_put_contents($jsonFilePath, json_encode($corridas, JSON_PRETTY_PRINT));
  }
}