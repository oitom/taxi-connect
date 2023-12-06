<?php

namespace Model;

use Model\Passageiro;
use Model\Motorista;

class Corrida
{
  private $origem;
  private $destino;
  private $passageiro;
  private $motorista;
  private $tipoCorrida;
  private $precoEstimado;
  private $tipoPagamento;
  private $autenticacao;
  private $status;
  private $horarioPico;

  public function __construct($origem, $destino, Passageiro $passageiro, Motorista $motorista, $tipoCorrida, $precoEstimado, $tipoPagamento, $autenticacao, $horarioPico, $status = 'created' )
  {
    $this->validarCampo($origem, 'origem');
    $this->validarCampo($destino, 'destino');
    $this->validarCampo($passageiro, 'passageiro');
    $this->validarCampo($motorista, 'motorista');
    $this->validarCampo($tipoCorrida, 'tipoCorrida');
    $this->validarCampo($tipoPagamento, 'tipoPagamento');
    $this->validarCampo($autenticacao, 'autenticacao');
    
    $this->origem = $origem;
    $this->destino = $destino;
    $this->passageiro = $passageiro;
    $this->motorista = $motorista;
    $this->tipoCorrida = $tipoCorrida;
    $this->precoEstimado = $precoEstimado;
    $this->tipoPagamento = $tipoPagamento;
    $this->autenticacao = $autenticacao;
    $this->$horarioPico = $horarioPico;
    $this->status = $status;
  }
  
  public function calcularPreco()
  {
    $taxaFixa = 2.5;
    $this->precoEstimado = $taxaFixa * $this->calcularDistancia();

    return $this->precoEstimado;
  }

  public function autenticarCorrida()
  {
    $tokenEsperado = "token123";
    $chaveEsperada = "segredo456";

    if (isset($this->autenticacao['token_acesso']) &&
      isset($this->autenticacao['chave_seguranca']) &&
      $this->autenticacao['token_acesso'] === $tokenEsperado &&
      $this->autenticacao['chave_seguranca'] === $chaveEsperada
    ) {
      return true;
    } else {
      return false;
    }
  }

  private function validarCampo($valor, $campo)
  {
    if (empty($valor)) {
        throw new InvalidArgumentException(sprintf('O campo "%s" não pode estar vazio.', $campo));
    }
  }

  public function getTarifaPorDistancia()
  {
    $distancia = $this->calcularDistancia($this->getOrigem(), $this->getDestino());
    $tarifaPorDistancia = $distancia * 2;

    return $tarifaPorDistancia;
  }

  private function calcularDistancia($origem, $destino)
  {
    $distancia = 10.0;
    return $distancia;
  }

  public function getValorTarifa()
  {
    $precoEstimado = $this->getPrecoEstimado();

    if($this->getTipoCorrida() == "taximetro")
      $valorTarifa = 2.5;
    else
      $valorTarifa = $precoEstimado * 1.2;
    
    return $valorTarifa;
  }

  public function getTarifaExtraHorarioPico()
  {
    $horaCorrida = date("H");
    $tarifaExtra = ($horaCorrida >= 18 && $horaCorrida <= 22) ? 5.0 : 0.0; 

    return $tarifaExtra;
  }

  public function getTempo()
  {
    $distancia = $this->calcularDistancia($this->getOrigem(), $this->getDestino());
    $tempoBase = $distancia * 10;

    if ($this->horarioPico) {
      $tempoBase += 10; 
    }
    return $tempoBase;
  }
  
  public function getTarifaExtraTempoExcedido()
  {
    $tempoTotal = $this->getTempo();
    $limiteTempo = 30;
    $tarifaExtra = max($tempoTotal - $limiteTempo, 0) * 0.5;

    return $tarifaExtra;
  }
  
  public function getOrigem() { return $this->origem; }
  public function getDestino() { return $this->destino; }
  public function getPassageiro() { return $this->passageiro; }
  public function getMotorista() { return $this->motorista; }
  public function getTipoCorrida() { return $this->tipoCorrida; }
  public function getPrecoEstimado() { return $this->precoEstimado; }
  public function getTipoPagamento() { return $this->tipoPagamento; }
  public function getAutenticacao() { return $this->autenticacao; }
  public function getStatus() { return $this->status; }
}