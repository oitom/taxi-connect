<?php
namespace Presenter;

use Model\Corrida;

class CorridaPresenter
{
  private $corrida;
  private $campos;

  public function __construct(Corrida $corrida, $campos)
  {
    $this->corrida = $corrida;
    $this->campos = $campos;
  }

  public function error($msg) {
    $response = array(
      'code' => 401,
      'message' => $msg,
      'reason' => $this->corrida->getStatusDesc(), 
      'status'=> $this->corrida->getStatus()
    );

    http_response_code(401);
    return json_encode($response, JSON_PRETTY_PRINT);
  }

  public function success() {
    $status = $this->corrida->getStatus();

    $response = array(
      'code' => 201,
      'message' => "Corrida $status com sucesso!",
      'data'=> $this->dadosToArray()
    );

    http_response_code(201);
    return json_encode($response,  JSON_PRETTY_PRINT);
  }

  private function dadosToArray()
  {
    $dados = [
      'uuid' => $this->corrida->getUuid(),
      'origem' => $this->corrida->getOrigem(),
      'destino' => $this->corrida->getDestino(),
      'passageiro' => $this->corrida->getPassageiro()->toArray(),
      'motorista' => $this->corrida->getMotorista()->toArray(),
      'tipoCorrida' => $this->corrida->getTipoCorrida(),
      'precoEstimado' => $this->corrida->getPrecoEstimado(),
      'tipoPagamento' => $this->corrida->getTipoPagamento(),
      'autenticacao' => $this->corrida->getAutenticacao(),
      'preco' => $this->corrida->getPreco(),
      'status' => $this->corrida->getStatus(),
      'data' => $this->corrida->getData(),
    ];

    if (!empty($this->campos)) {
      $dados = array_intersect_key($dados, array_flip($this->campos));
    }

    return $dados;
  }
}
