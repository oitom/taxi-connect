<?php

namespace Service;

use Model\Corrida;
use Presenter\CorridaPresenter;

class AticvateCorridaService extends CorridaService
{
  private $params;

  public function __construct($params) {
    $this->params = $params;
  }

  public function aticvate()
  {
    $resp = parent::getCorridaPorUuid($this->params['uuid']);
    $corrida = $resp["corrida"] ?? null;
    $campos = ['uuid', 'origem', 'destino', 'tipoCorrida', 'status', 'statusDesc', 'data'];
    
    $presenter = new CorridaPresenter($corrida, $campos);

    if (!$corrida) {
      return $presenter->error('Corrida não encontrada.');
    }
    else {
      $presenter = new CorridaPresenter($resp['corrida'], $campos);

      if (!$this->podeAtivarCorrida($corrida)) {
        return $presenter->error('Não foi possível ativar a corrida.');
      }
 
     $this->ativarCorrida($corrida);
     return $presenter->success();
    }  
  }

  private function podeAtivarCorrida(Corrida $corrida)
  {
    if($corrida->getStatus() == "cancelada") {
      $corrida->setStatusDesc("Corrida cancelada");
      return false;
    }
    if($corrida->getStatus() == "ativa") {
      $corrida->setStatusDesc("Corrida já foi ativada");
      return false;
    }
    
    return true;
  }

  private function ativarCorrida(Corrida $corrida)
  {
    $data_ativacao = date("Y-m-d H:i:s");

    $dados_atualizar = array(
      "status" => "ativa",
      "statusDesc" => "A corrida foi ativada em $data_ativacao",
    );

    $corrida->setStatus($dados_atualizar["status"]);
    $corrida->setStatusDesc($dados_atualizar["statusDesc"]);
    $corrida_atualizada = parent::atualizarCorrida($corrida->getUuid(), $dados_atualizar);
  }
}