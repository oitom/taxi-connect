<?php
namespace Model;

class Motorista
{
  private $cnpj;
  private $nome;
  private $placaVeiculo;
  private $modeloVeiculo;

  public function __construct($cnpj, $nome, $placaVeiculo, $modeloVeiculo)
  {
    $this->cnpj = $cnpj;
    $this->nome = $nome;
    $this->placaVeiculo = $placaVeiculo;
    $this->modeloVeiculo = $modeloVeiculo;
  }

  public function getNome() { return $this->nome; }
  public function getPlacaVeiculo() { return $this->placaVeiculo; }
  public function getModeloVeiculo() { return $this->modeloVeiculo; }

  public function toArray()
  {
    return [
      'cnpj' => $this->cnpj,
      'nome' => $this->nome,
      'placaVeiculo' => $this->placaVeiculo,
      'modeloVeiculo' => $this->modeloVeiculo,
    ];
  }
}