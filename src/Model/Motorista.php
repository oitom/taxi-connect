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
    $this->validarCampo($cnpj, 'cnpj');
    $this->validarCampo($nome, 'nome');
    $this->validarCampo($placaVeiculo, 'placaVeiculo');
    $this->validarCampo($modeloVeiculo, 'modeloVeiculo');

    $this->cnpj = $cnpj;
    $this->nome = $nome;
    $this->placaVeiculo = $placaVeiculo;
    $this->modeloVeiculo = $modeloVeiculo;
  }

  private function validarCampo($valor, $campo)
  {
    if (empty($valor) || $valor === null) {
      throw new \InvalidArgumentException(sprintf('O campo motorista %s não pode estar vazio.', $campo));
    }
  }

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