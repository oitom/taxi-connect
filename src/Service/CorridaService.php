<?php

namespace Service;

use Model\Corrida;
use Presenter\CorridaPresenter;

class CorridaService
{
  public function __construct() {
  }

  public function validarCorrida(Corrida $corrida)
  {

    if ($corrida->getTipoCorrida() === 'taximetro') {
      $tarifaPorDistancia = $corrida->getTarifaPorDistancia();
      $valorTarifa = $corrida->getValorTarifa();

      if ($this->isHorarioDePico()) {
        $tarifaPorDistancia += $corrida->getTarifaExtraHorarioPico();
      }

      if ($this->isLocalDificilAcesso($corrida->getOrigem()) || $this->isLocalDificilAcesso($corrida->getDestino())) {
        $tarifaPorDistancia += $corrida->getTarifaExtraLocalDificilAcesso();
      }

      if ($this->isTempoExcedido($corrida->getTempo())) {
        $tarifaPorDistancia += $corrida->getTarifaExtraTempoExcedido();
      }

      if ($tarifaPorDistancia >= $valorTarifa) {
        return true;
      } else {
        return false;
      }
    }

    return true;
  }

  private function isHorarioDePico()
  {
    return false;
  }

  private function isLocalDificilAcesso($local)
  {
    return false;
  }

  private function isTempoExcedido($tempo)
  {
    return false;
  }
}
