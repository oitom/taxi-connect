<?php

namespace Service;

use Presenter\CorridaPresenter;

class ListCorridaService extends CorridaService
{
  private $params;

  public function __construct($params) {
    $this->params = $params;
  }

  public function list()
  {
    if ($this->params['uuid'] == null) {
      $response = parent::getCorridas();
      $presenter = new CorridaPresenter(null, null);
      return $presenter->toAll($response);
    }
    else {
      $resp = parent::getCorridaPorUuid($this->params['uuid']);
      $corrida = $resp["corrida"] ?? null;

      $presenter = new CorridaPresenter($corrida, null);
    
      if (!$corrida) {
        return $presenter->error('Corrida não encontrada.');
      }
      else {
        $presenter = new CorridaPresenter($corrida, null);
        return $presenter->toAllArray();
      }
    }
  }
}