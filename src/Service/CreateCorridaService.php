<?php

namespace Service;

use Model\Corrida;
use Presenter\CorridaPresenter;

class CreateCorridaService extends CorridaService
{
  private $body;

  public function __construct($body) {
    $this->body = $body;
  }

  public function create()
  { 
    $resp = parent::criarCorrida($this->body);
    $response = $this->validaCorrida($resp);

    return $response;
  }

  private function validaCorrida($dados)
  {
    $corrida = $dados["corrida"];

    $presenter = new CorridaPresenter($corrida, ['uuid', 'origem', 'destino', 'tipoCorrida', 'preco', 'status', 'statusDesc', 'data']);

    if($dados["error"]) {
      return $presenter->error($dados["msg"]);
    }
    
    if(!$this->autenticarCorrida($corrida)) {
      return $presenter->error('Não foi possível autorizar a corrida.');
    }

    if(!$this->calculaPreco($corrida)) {
      return $presenter->error('Não foi possível calcular o valor da corrida. Campo tipo_corrida invalido!');
    }

    if(!$this->autorizarCorrida($corrida)) {
      return $presenter->error('Não foi possível autorizar a corrida.');
    }

    $this->inserirCorrida($corrida->toArray());
    $this->autorizaCorrida($corrida);
    
    return $presenter->success();
  }

  private function calculaPreco(Corrida $corrida) 
  {
    if ($corrida->getTipoCorrida() === 'taximetro') {
      $tarifaPorDistancia = $corrida->getTarifaPorDistancia();

      if ($corrida->getHorarioPico()) {
        $tarifaPorDistancia += $corrida->getTarifaExtraHorarioPico();
      }

      if ($this->isLocalDificilAcesso($corrida->getOrigem()) || $this->isLocalDificilAcesso($corrida->getDestino())) {
        $tarifaPorDistancia += $corrida->getTarifaExtraLocalDificilAcesso();
      }
      
      if ($this->isTempoExcedido($corrida->getTempo())) {
        $tarifaPorDistancia += $corrida->getTarifaExtraTempoExcedido();
      }
      $corrida->setPreco($tarifaPorDistancia);
    }
    else if ($corrida->getTipoCorrida() === 'preco_fixo') {
      $corrida->setPreco($corrida->getPrecoEstimado());
    }
    else {
      return false;
    }
    
    return true;
  }

  private function isLocalDificilAcesso($local)
  {
    return true;
  }

  private function isTempoExcedido($tempo)
  {
    return true;
  }

  private function autorizarCorrida(Corrida $corrida)
  {
    $diferencaMaximaPermitida = 10.0;
    $valorMaximoPermitido = 30.0;

    $preco = $corrida->getPreco();
    $precoEstimado = $corrida->getPrecoEstimado();
    
    $diferenca = abs($preco - $precoEstimado);
    if ($diferenca > $diferencaMaximaPermitida) {
      $corrida->setStatus('rejeitada');
      $corrida->setStatusDesc('A diferença entre o preço real e o preço estimado excedeu o limite permitido.');
      return false;
    }
    
    if ($preco > $valorMaximoPermitido) {
      $corrida->setStatus('rejeitada');
      $corrida->setStatusDesc('O preço total da corrida excedeu o limite permitido.');
      return false;
    }

    return true;
  }

  private function inserirCorrida($corrida)
  {
    $corridas = parent::getCorridas();
    $corridas[] = $corrida;
    parent::setCorridas($corridas);
    return true;
  }

  private function autenticarCorrida(Corrida $corrida)
  {
    return $corrida->autenticarCorrida();
  }

  private function autorizaCorrida(Corrida $corrida)
  {
    $data_autorizada = date("Y-m-d H:i:s");

    $dados_atualizar = array(
      "status" => "autorizada",
      "statusDesc" => "A corrida foi autorizada em $data_autorizada",
    );

    $corrida->setStatus($dados_atualizar["status"]);
    $corrida->setStatusDesc($dados_atualizar["statusDesc"]);
    $corrida_atualizada = parent::atualizarCorrida($corrida->getUuid(), $dados_atualizar);

    return true;
  }
}