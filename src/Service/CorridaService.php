<?php

namespace Service;

use Model\Corrida;
use Presenter\CorridaPresenter;

class CorridaService
{
  public function __construct() {
  }

  public function calculaPreco(Corrida $corrida)
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
}
