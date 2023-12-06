<?php
namespace Model;

class Passageiro
{
  private $cpf;
  private $nome;
  private $telefone;

  public function __construct($cpf, $nome, $telefone)
  {
    $this->cpf = $cpf;
    $this->nome = $nome;
    $this->telefone = $telefone;
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