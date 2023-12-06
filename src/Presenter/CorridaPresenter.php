<?php
namespace Presenter;
use Model\Corrida;

class CorridaPresenter
{
  private $corrida;

  public function __construct(Corrida $corrida)
  {
    $this->corrida = $corrida;
  }

  public function toArray(array $campos = [])
  {
    $dados = [
      'origem' => $this->corrida->getOrigem(),
      'destino' => $this->corrida->getDestino(),
      'passageiro' => $this->corrida->getPassageiro()->toArray(),
      'motorista' => $this->corrida->getMotorista()->toArray(),
      'tipoCorrida' => $this->corrida->getTipoCorrida(),
      'precoEstimado' => $this->corrida->getPrecoEstimado(),
      'tipoPagamento' => $this->corrida->getTipoPagamento(),
      'autenticacao' => $this->corrida->getAutenticacao(),
      'status' => $this->corrida->getStatus(),
    ];

    // Filtrar os campos conforme a lista fornecida
    if (!empty($campos)) {
      $dados = array_intersect_key($dados, array_flip($campos));
    }

    return $dados;
  }
}
