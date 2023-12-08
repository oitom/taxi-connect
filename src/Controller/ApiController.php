<?php

namespace Controller;

use Service\ListCorridaService;
use Service\CreateCorridaService;
use Service\CancelCorridaService;
use Service\AticvateCorridaService;

class ApiController
{
  public function list($params=null)
  {
    $corridaService = new ListCorridaService($params);
    $response = $corridaService->list();

    return $response;
  }

  public function create($params=null, $body=null)
  {  
    $corridaService = new CreateCorridaService($body);
    $response = $corridaService->create();

    return $response;
  }

  public function cancel($params=null)
  {
    $corridaService = new CancelCorridaService($params);
    $response = $corridaService->cancel();
    
    return $response;
  }

  public function activate($params=null)
  {
    $corridaService = new AticvateCorridaService($params);
    $response = $corridaService->aticvate();
    
    return $response;
  }
}