<?php

namespace Controller;

class ApiController
{
  public function __construct() {
    header('Content-Type: application/json');
  }

  public function getList()
  {
    echo json_encode(['message' => 'GetList']);
  }

  public function create()
  {
    echo json_encode(['message' => 'create']);
  }

  public function delete()
  {
    echo json_encode(['message' => 'cancel']);
  }

  public function notAllow()
  {
    echo json_encode(['message' => 'notAllow']);
  }
}