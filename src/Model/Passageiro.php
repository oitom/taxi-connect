<?php
namespace Model;

class Passageiro
{
  private $cpf;
  private $nome;
  private $telefone;

  public function __construct($cpf, $nome, $telefone)
  {
    $this->validarCampo($cpf, 'cpf');
    $this->validarCampo($telefone, 'telefone');

    $this->cpf = $cpf;
    $this->nome = $nome;
    $this->telefone = $telefone;
  }
  
  private function validarCampo($valor, $campo)
  {
    if (empty($valor) || $valor === null) {
      throw new \InvalidArgumentException(sprintf('O campo passageiro %s não pode estar vazio.', $campo));
    }
  }

  public function getCPF() { return $this->cpf; }
  public function getNome() { return $this->nome; }
  public function getTelefone() { return $this->telefone; }

  public function toArray()
  {
    return [
      'cpf' => $this->cpf,
      'nome' => $this->nome,
      'telefone' => $this->telefone,
    ];
  }
}