<?php

namespace Router;

class DefaultHeaderProvider implements HeaderProviderInterface
{
  public function getHeaders()
  {
    return function_exists('getallheaders') ? getallheaders() : [];
  }
}