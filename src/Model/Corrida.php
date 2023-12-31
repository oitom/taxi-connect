<?php

namespace Model;

use Model\Passageiro;
use Model\Motorista;
use Ramsey\Uuid\Uuid;

class Corrida
{
  private $uuid;
  private $origem;
  private $destino;
  private $passageiro;
  private $motorista;
  private $tipoCorrida;
  private $precoEstimado;
  private $preco;
  private $tipoPagamento;
  private $autenticacao;
  private $horarioPico;
  private $status;
  private $statusDesc;
  private $data;

  public function __construct($origem, $destino, Passageiro $passageiro, Motorista $motorista, $tipoCorrida, $precoEstimado, $tipoPagamento, $horarioPico, $autenticacao)
  {
    $this->validarCampo($origem, 'origem');
    $this->validarCampo($destino, 'destino');
    $this->validarCampo($passageiro, 'passageiro');
    $this->validarCampo($motorista, 'motorista');
    $this->validarCampo($tipoCorrida, 'tipoCorrida');
    $this->validarCampo($tipoPagamento, 'tipoPagamento');
    $this->validarCampo($autenticacao, 'autenticacao');
    $this->validarCampo($precoEstimado, 'precoEstimado');
    
    $uuid = Uuid::uuid4();
    $this->uuid = $uuid->toString();
    $this->origem = $origem;
    $this->destino = $destino;
    $this->passageiro = $passageiro;
    $this->motorista = $motorista;
    $this->tipoCorrida = $tipoCorrida;
    $this->precoEstimado = $precoEstimado;
    $this->tipoPagamento = $tipoPagamento;
    $this->horarioPico = (bool) $horarioPico;
    $this->autenticacao = $autenticacao;
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
      $this->setStatus("Não autenticado");
      $this->setStatusDesc("Credenciais de autenticação invalidas");
      return false;
    }
  }

  private function validarCampo($valor, $campo)
  {
    if (empty($valor)) {
      throw new \InvalidArgumentException(sprintf('O campo %s não pode estar vazio.', $campo));
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

  public function getTarifaExtraHorarioPico()
  {
    $tarifaExtra = 5.0; 
    return $tarifaExtra;
  }

  public function getTarifaExtraLocalDificilAcesso()
  {
    $tarifaExtra = 3.0; 
    return $tarifaExtra;
  }

  public function getTempo()
  {
    $distancia = $this->calcularDistancia($this->getOrigem(), $this->getDestino());
    $tempoBase = $distancia * 0.4;

    if ($this->horarioPico) {
      $tempoBase += 10.2; 
    }
    return $tempoBase;
  }
  
  public function getTarifaExtraTempoExcedido()
  {
    $tempoTotal = $this->getTempo();
    $limiteTempo = 12;
    $tarifaExtra = max($tempoTotal - $limiteTempo, 0) * 2;

    return $tarifaExtra;
  }
  
  public function getUuid() { return $this->uuid; }
  public function setUuid($uuid) { $this->uuid = $uuid; }

  public function getOrigem() { return $this->origem; }
  public function getDestino() { return $this->destino; }
  public function getPassageiro() { return $this->passageiro; }
  public function getMotorista() { return $this->motorista; }
  public function getTipoCorrida() { return $this->tipoCorrida; }
  public function getPrecoEstimado() { return $this->precoEstimado; }
  public function getTipoPagamento() { return $this->tipoPagamento; }
  public function getAutenticacao() { return $this->autenticacao; }
  public function getHorarioPico() { return $this->horarioPico; }

  public function getData() { return date('Y-m-d H:i:s'); }
  public function setData($data) { return $this->data = $data; }

  public function getStatus() { return $this->status; }
  public function setStatus($status) { $this->status = $status; }

  public function getStatusDesc() { return $this->statusDesc; }
  public function setStatusDesc($statusDesc) { $this->statusDesc = $statusDesc; }

  public function getPreco() { return $this->preco; }
  public function setPreco($preco) { $this->preco = $preco; }

  public function toArray()
  {
    $reflection = new \ReflectionClass($this);
    $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

    $result = [];

    foreach ($properties as $property) {
      $property->setAccessible(true);
      $value = $property->getValue($this);

      if (is_object($value) && method_exists($value, 'toArray')) {
        $result[$property->getName()] = $value->toArray();
      } else {
        $result[$property->getName()] = $value;
      }
    }

    return $result;
  }
}