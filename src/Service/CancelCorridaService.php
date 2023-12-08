<?php

namespace Service;

use Model\Corrida;
use Presenter\CorridaPresenter;

class CancelCorridaService extends CorridaService
{
  private $params;

  public function __construct($params) {
    $this->params = $params;
  }

  public function cancel()
  {
    $resp = parent::getCorridaPorUuid($this->params['uuid']);
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

  private function podeCancelarCorrida(Corrida $corrida) 
  {
    if($corrida->getStatus() == "ativa") {
      $corrida->setStatusDesc("Corrida em andamento!");
      return false;
    }
    
    return true;
  }

  private function cancelarCorrida(Corrida $corrida)
  {
    $data_cancelamento = date("Y-m-d H:i:s");

    $corrida->setStatus("cancelada");
    $corrida->setPreco(0);
    $corrida->setStatusDesc("A corrida foi cancelada em $data_cancelamento");
    return true;
  }

}